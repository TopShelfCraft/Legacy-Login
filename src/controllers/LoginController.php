<?php

namespace topshelfcraft\legacylogin\controllers;

use Craft;
use craft\elements\User;
use craft\errors\ElementNotFoundException;
use craft\errors\MissingComponentException;
use craft\events\LoginFailureEvent;
use craft\helpers\Json;
use craft\helpers\User as UserHelper;
use craft\web\Controller;
use topshelfcraft\legacylogin\handers\BaseAuthHandler;
use topshelfcraft\legacylogin\LegacyLogin;
use topshelfcraft\legacylogin\models\Login;
use topshelfcraft\legacylogin\models\SettingsModel;
use yii\base\Exception;
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
		'actionLogin'
	];

	/**
	 * @var string
	 */
	public $defaultAction = 'login';

	/*
	 * Public methods
	 */

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action)
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
	 * @return Response
	 *
	 * @throws BadRequestHttpException
	 * @throws MissingComponentException
	 * @throws ElementNotFoundException
	 * @throws Exception
	 * @throws \Throwable
	 */
	public function actionLogin()
	{

		if (!Craft::$app->getUser()->getIsGuest()) {
			// Too easy.
			return $this->_handleSuccessfulLogin(false);
		}

		$this->requirePostRequest();

		$login = new Login([
			'loginName' => Craft::$app->getRequest()->getRequiredBodyParam('loginName'),
			'password' => Craft::$app->getRequest()->getRequiredBodyParam('password'),
			'rememberMe' => (bool) Craft::$app->getRequest()->getBodyParam('rememberMe'),
		]);

		if (!$login->validate())
		{
			return $this->_handleLoginFailure(User::AUTH_INVALID_CREDENTIALS);
		}

		/*
		 * Craft's User controller adds a random delay to the login process, to help thwart timing attacks.
		 * Legacy Login has presumably more options to execute (i.e. auth via Craft + legacy handlers), so
		 * the timing characteristics of its login action are both heavier AND less predictable.
		 * Therefore, in this case, it's seemingly not very helpful to add a delay like Craft natively does.
		 */
		usleep(42);

		// Can the current Craft app authenticate this user?

		if (LegacyLogin::$plugin->login->loginWithNativeCraft($login))
		{
			return $this->_handleSuccessfulLogin(true);
		}

		// Can any of our legacy services auth this user?

		$settings = LegacyLogin::$plugin->getSettings();

		/** @var SettingsModel $settings */
		foreach ($settings->getHandlers() as $handler)
		{

			if (LegacyLogin::$plugin->login->loginWithHandler($login, $handler))
			{
				return $this->_handleSuccessfulLogin(true);
			}

		}

		// No luck.

		return $this->_handleLoginFailure($login->authError ?: User::AUTH_INVALID_CREDENTIALS, $login->user);

	}

	/*
	 * Private methods
	 */

	/**
	 * @param bool $setNotice
	 * @param BaseAuthHandler $handler
	 *
	 * @return Response
	 *
	 * @throws MissingComponentException
	 * @throws BadRequestHttpException
	 */
	private function _handleSuccessfulLogin($setNotice = false, BaseAuthHandler $handler = null)
	{

		$request = Craft::$app->getRequest();
		$sessionService = Craft::$app->getSession();
		$userSession = Craft::$app->getUser();

		// Get the return URL...
		$returnUrl = $userSession->getReturnUrl();
		// ...and clear it out.
		$userSession->removeReturnUrl();

		// If the request wants JSON...

		if ($request->getAcceptsJson()) {

			$return = [
				'success' => true,
				'returnUrl' => $returnUrl,
				'handler' => Json::encode($handler->getAttributes()),
			];

			if (Craft::$app->getConfig()->getGeneral()->enableCsrfProtection)
			{
				$return['csrfTokenValue'] = $request->getCsrfToken();
			}

			return $this->asJson($return);

		}

		// Okay, not a JSON request...

		if ($setNotice) {
			$sessionService->setNotice(Craft::t('app', 'Logged in.'));
		}

		// Store flash info for use by the next request
		$sessionService->setFlash('legacyLoginSuccess', true);
		$sessionService->setFlash('legacyLoginHandler', $handler);

		return $this->redirectToPostedUrl($userSession->getIdentity(), $returnUrl);

	}

	/**
	 * @param string|null $authError
	 * @param User|null $user
	 *
	 * @return Response|null
	 *
	 * @throws MissingComponentException ...if `getSession()` can't access the Session service.
	 */
	private function _handleLoginFailure(string $authError = null, User $user = null)
	{

		$message = UserHelper::getLoginFailureMessage($authError, $user);

		// Fire a 'loginFailure' event
		$event = new LoginFailureEvent([
			'authError' => $authError,
			'message' => $message,
			'user' => $user,
		]);
		$this->trigger(self::EVENT_LOGIN_FAILURE, $event);

		if (Craft::$app->getRequest()->getAcceptsJson()) {

			return $this->asJson([
				'errorCode' => $authError,
				'error' => $message,
			]);

		}

		Craft::$app->getSession()->setError($event->message);

		Craft::$app->getUrlManager()->setRouteParams([
			'loginName' => Craft::$app->getRequest()->getBodyParam('loginName'),
			'rememberMe' => (bool)Craft::$app->getRequest()->getBodyParam('rememberMe'),
			'errorCode' => $authError,
			'errorMessage' => $message,
		]);

		return null;

	}

	/**
	 * Fakes a password validation, to help thwart timing attacks, in cases we don't end up with an actual submitted password to validate.
	 */
	private function _doFakeValidation()
	{
		Craft::$app->getSecurity()->validatePassword('p@ss1w0rd', '$2y$13$nj9aiBeb7RfEfYP3Cum6Revyu14QelGGxwcnFUKXIrQUitSodEPRi');
	}

}
