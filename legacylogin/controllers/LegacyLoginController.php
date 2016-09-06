<?php
namespace Craft;

/**
 * The LegacyLoginController class is a controller that handles logging-in by authenticating users from legacy systems
 * and recreating them as local Craft users if they don't exist yet.
 *
 * Note that all actions in the controller, except {@link actionLogin}, require an
 * authenticated Craft session via {@link BaseController::allowAnonymous}.
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLoginController extends BaseController
{

	// Properties
	// =========================================================================

	/**
	 * The list of actions in this controller that are available for anonymous use.
	 *
	 * @var array
	 */
	protected $allowAnonymous = array('actionLogin');


	// Public Methods
	// =========================================================================

	/**
	 * Handles log-in via POST request.
	 *
	 * @return null
	 */
	public function actionLogin()
	{

		// TODO

		// If the User is already logged in, skip straight to processing this as a successful login.
		if (craft()->userSession->isLoggedIn())
		{
			$this->_handleSuccessfulLogin(false);
		}

		// Bail if this isn't a POST request.
		$this->requirePostRequest();

		// A little house-cleaning for expired, pending users. (Same as in the UsersController, natch.)
		craft()->users->purgeExpiredPendingUsers();

		// Okay, let's go.
		$loginName = craft()->request->getPost('loginName');
		$password = craft()->request->getPost('password');
		$rememberMe = (bool) craft()->request->getPost('rememberMe');

		if ($loginType = craft()->legacyLogin->login($loginName, $password, $rememberMe))
		{
			$this->_handleSuccessfulLogin($loginType, true);
		}
		else
		{

			$errorCode = craft()->userSession->getLoginErrorCode();
			$errorMessage = craft()->userSession->getLoginErrorMessage($errorCode, $loginName);

			if (craft()->request->isAjaxRequest())
			{
				$this->returnJson(array(
					'errorCode' => $errorCode,
					'error' => $errorMessage
				));
			}
			else
			{

				craft()->userSession->setError($errorMessage);

				craft()->urlManager->setRouteVariables(array(
					'loginName' => $loginName,
					'rememberMe' => $rememberMe,
					'errorCode' => $errorCode,
					'errorMessage' => $errorMessage,
				));

			}

		}

	}


	// Private Methods
	// =========================================================================

	/**
	 * Redirects the user after a successful login attempt, or if they visited the Login page while they were already
	 * logged in.
	 *
	 * @param string $loginType What type of login was achieved, legacy or Craft native.
	 * @param bool $setNotice Whether a flash notice should be set, if this isn't an Ajax request.
	 *
	 * @return null
	 */
	private function _handleSuccessfulLogin($loginType, $setNotice = true)
	{

		// TODO

		// Get the current user
		$currentUser = craft()->userSession->getUser();

		// Were they trying to access a URL beforehand?
		$returnUrl = craft()->userSession->getReturnUrl(null, true);

		if ($returnUrl === null || $returnUrl == craft()->request->getPath())
		{

			// If this is a CP request and they can access the control panel, send them wherever
			// postCpLoginRedirect tells us
			if (craft()->request->isCpRequest() && $currentUser->can('accessCp'))
			{
				$postCpLoginRedirect = craft()->config->get('postCpLoginRedirect');
				$returnUrl = UrlHelper::getCpUrl($postCpLoginRedirect);
			}
			else
			{
				// Otherwise send them wherever postLoginRedirect tells us
				$postLoginRedirect = craft()->config->get('postLoginRedirect');
				$returnUrl = UrlHelper::getSiteUrl($postLoginRedirect);
			}

		}

		// If this was an Ajax request, just return success:true
		if (craft()->request->isAjaxRequest())
		{
			$this->returnJson(array(
				'success' => true,
				'legacyLoginType' => $loginType,
				'returnUrl' => $returnUrl
			));
		}
		else
		{
			if ($setNotice)
			{
				craft()->userSession->setNotice(Craft::t('Logged in.' ));				
			}
			//Store the login method so templates can act on the login type	
			craft()->httpSession->add("legacyLoginType", $loginType);
			$this->redirectToPostedUrl($currentUser, $returnUrl);
		}

	}

}
