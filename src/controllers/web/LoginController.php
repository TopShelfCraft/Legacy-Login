<?php
namespace TopShelfCraft\LegacyLogin\controllers\web;

use Craft;
use craft\elements\User;
use craft\events\LoginFailureEvent;
use craft\helpers\User as UserHelper;
use craft\web\Controller;
use Exception;
use TopShelfCraft\LegacyLogin\LegacyLogin;
use TopShelfCraft\LegacyLogin\login\LoginRecord;
use TopShelfCraft\LegacyLogin\login\LoginRequest;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class LoginController extends Controller
{

	/**
	 * @event LoginFailureEvent The event that is triggered when a login attempt fails.
	 */
	const EVENT_LOGIN_FAILURE = 'loginFailure';

	/**
	 * @inheritdoc
	 */
	protected $allowAnonymous = [
		'login' => self::ALLOW_ANONYMOUS_LIVE | self::ALLOW_ANONYMOUS_OFFLINE,
	];

	/**
	 * @inheritdoc
	 */
	public $defaultAction = 'login';

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action): bool
	{

		/*
		 * Don't enable CSRF validation for login requests if the user is already logged-in.
		 * (Guards against double-clicking a Login button.)
		 * c.f. Craft UsersController::beforeAction()
		 */
		if ($action->id === 'login' && !Craft::$app->getUser()->getIsGuest()) {
			$this->enableCsrfValidation = false;
		}

		return parent::beforeAction($action);

	}

	/**
	 * Attempts to fulfill a Login request using either native Craft authentication or a legacy handler.
	 *
	 * @throws BadRequestHttpException if the Request is not POST
	 */
	public function actionLogin(): ?Response
	{

		if (!Craft::$app->getUser()->getIsGuest()) {
			/*
			 * If a User is already logged in, skip straight to Success, just like UsersController::actionLogin().
			 */
			return $this->_handleSuccessfulLogin(false);
		}

		/*
		 * UsersController::actionLogin() allows non-POST requests, because the native `login` action
		 * pulls double-duty, handling both authentication (POST) and rendering of the login template (GET).
		 * This action has no such double-duty, and thus we *require* a POST request for good measure.
		 */
		$this->requirePostRequest();

		$login = new LoginRequest(
			Craft::$app->getRequest()->getRequiredBodyParam('loginName'),
			Craft::$app->getRequest()->getRequiredBodyParam('password'),
			(bool) Craft::$app->getRequest()->getBodyParam('rememberMe')
		);

		$nativeUser = Craft::$app->users->getUserByUsernameOrEmail($login->loginName);
		$nativeAuth = LegacyLogin::getInstance()->login->authenticateNative($login, $nativeUser);

		if ($nativeAuth)
		{
			try
			{
				LegacyLogin::getInstance()->login->login($nativeUser, $login->rememberMe);
				return $this->_handleSuccessfulLogin();
			}
			catch (Exception $e)
			{
				return $this->_handleLoginFailure(null, $nativeUser);
			}
		}

		/*
		 * We have *not* succeeded at authenticating a native User via Craft.
		 * Let's see if any of our legacy handlers can fulfill this request.
		 */

		$settings = LegacyLogin::getInstance()->getSettings();
		foreach ($settings->getHandlers() as $handler)
		{

			if ($handler->handle($login))
			{
				return $this->_handleSuccessfulLogin();
			}

		}

		return $this->_handleLoginFailure(($nativeUser ? $nativeUser->authError : User::AUTH_INVALID_CREDENTIALS), $nativeUser);

	}

	private function _handleSuccessfulLogin(LoginRecord $legacyLoginRecord = null, $setFlash = true): Response
	{

		$userSession = Craft::$app->getUser();

		$returnUrl = $userSession->getReturnUrl();
		$userSession->removeReturnUrl();

		if ($this->request->getAcceptsJson()) {

			$return = [
				'success' => true,
				'returnUrl' => $returnUrl,
				'legacyLogin' => $legacyLoginRecord ? $legacyLoginRecord->getAttributes() : null,
			];

			if (Craft::$app->getConfig()->getGeneral()->enableCsrfProtection)
			{
				$return['csrfTokenValue'] = $this->request->getCsrfToken();
			}

			return $this->asJson($return);

		}

		if ($setFlash && $legacyLoginRecord)
		{
			Craft::$app->getSession()->setFlash('legacyLogin', $legacyLoginRecord->getAttributes());
		}

		return $this->redirectToPostedUrl($userSession->getIdentity(), $returnUrl);

	}

	private function _handleLoginFailure(string $authError = null, User $user = null): ?Response
	{

		/*
		 * Craft's UsersController adds a random delays to the login process, to help thwart timing attacks.
		 * Legacy Login ostensibly has more options to execute (i.e. auth via Craft + legacy handlers), so
		 * the timing characteristics of its login action are both heavier AND less predictable.
		 * Therefore, it's seemingly not very helpful to add arbitrary delay here like Craft natively does.
		 */

		$message = UserHelper::getLoginFailureMessage($authError, $user);

		$event = new LoginFailureEvent([
			'authError' => $authError,
			'message' => $message,
			'user' => $user,
		]);
		$this->trigger(self::EVENT_LOGIN_FAILURE, $event);

		if ($this->request->getAcceptsJson()) {
			return $this->asJson([
				'errorCode' => $authError,
				'error' => $event->message
			]);
		}

		$this->setFailFlash($event->message);

		Craft::$app->getUrlManager()->setRouteParams([
			'loginName' => $this->request->getBodyParam('loginName'),
			'rememberMe' => (bool)$this->request->getBodyParam('rememberMe'),
			'errorCode' => $authError,
			'errorMessage' => $event->message,
		]);

		return null;

	}

}
