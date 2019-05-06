<?php
namespace topshelfcraft\legacylogin\services;

use Craft;
use craft\base\Component;
use craft\elements\User;
use craft\errors\ElementNotFoundException;
use topshelfcraft\legacylogin\handers\BaseAuthHandler;
use topshelfcraft\legacylogin\models\Login as LoginModel;
use topshelfcraft\legacylogin\records\MatchedUser;
use yii\base\Exception;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
class Login extends Component
{

	/**
	 * @param LoginModel $login
	 *
	 * @return bool Success.
	 */
	public function loginWithNativeCraft(LoginModel $login)
	{

		// Does a user exist with that username/email?
		$user = Craft::$app->getUsers()->getUserByUsernameOrEmail($login->loginName);

		if (!$user || $user->password === null) {

			// Delay again to match $user->authenticate()'s delay
			Craft::$app->getSecurity()->validatePassword('p@ss1w0rd', '$2y$13$nj9aiBeb7RfEfYP3Cum6Revyu14QelGGxwcnFUKXIrQUitSodEPRi');

			$login->authError = User::AUTH_INVALID_CREDENTIALS;

			return false;

		}

		// Set the User on the Login model.
		$login->user = $user;

		// Did they submit a valid password, and is the user capable of being logged-in?
		if (!$user->authenticate($login->password)) {

			$login->authError = $user->authError;

			return false;

		}

		return $this->login($user, $login->rememberMe);

	}

	/**
	 * @param LoginModel $login
	 * @param BaseAuthHandler $handler
	 *
	 * @return bool Success.
	 *
	 * @throws \Throwable ...if reasons
	 * @throws ElementNotFoundException ...if $newUser has an invalid $id
	 * @throws Exception ...if $newUser doesn’t have any supported sites
	 */
	public function loginWithHandler(LoginModel $login, BaseAuthHandler $handler)
	{

		$newUserCreated = false;

		/** @var BaseAuthHandler $handler */
		$legacyUserData = $handler->getLegacyUserData($login);

		if (!$legacyUserData || !$handler->_authenticate($legacyUserData, $login))
		{
			/*
			 * This handler couldn't find a legacy user corresponding to this login attempt,
			 * or it found one but couldn't authenticate the user.
			 */
			return false;
		}

		/*
		 * We found and authenticated a legacy user. Now we'll try to match that legacy user with
		 * a User from the current Craft system...
		 */

		$matchedUser = $handler->getMatchedUser($legacyUserData);

		if (!$matchedUser && !$handler->createNewUser)
		{
			/*
			 * The legacy user doesn't match with a User in the current Craft system, and this handler
			 * isn't allowed to create a new User. SKIP!
			 */
			return false;
		}

		if (!$matchedUser && $handler->createNewUser)
		{

			/*
			 * We didn't find a matched User, but this handler is allowed to create a new one!
			 */

			$newUser = $handler->prepNewUser($legacyUserData);

			// TODO: Event to let other components modify the user before saving?

			$newUser->newPassword = $login->password;
			$newUser->passwordResetRequired = $handler->requirePasswordReset;

			if (!Craft::$app->getElements()->saveElement($newUser))
			{
				/*
				 * The new User could not be saved.
				 */
				return false;
				// TODO: Log this.
			}

			// TODO: Event to let other components modify the user after saving?

			$newUserCreated = true;
			$matchedUser = $newUser;

		}

		/*
		 * If we've made it this far, either we found a matching User initially, or the handler was able to create one.
		 */

		// Set the User on the Login model for reference by response handlers.
		$login->user = $matchedUser;

		/*
		 * If this matched User has already redeemed a legacy login in the past, deny this attempt.
		 * (A matched User may only log in via legacy handlers once.)
		 */
		if (MatchedUser::find()->where(['userId' => $matchedUser->id])->exists())
		{
			$login->authError = 'legacy_login_already_redeemed';
			return false;
		}

		/*
		 * If this is an existing User, maybe we need to make some modifications.
		 * (If it's a new User, any necessary properties were already set when the User was created.)
		 */
		if (!$newUserCreated && ($handler->setPassword || $handler->requirePasswordReset))
		{

			if ($handler->setPassword)
			{
				$matchedUser->newPassword = $login->password;
			}

			$matchedUser->passwordResetRequired = $handler->requirePasswordReset;

			$matchedUser->setScenario(User::SCENARIO_PASSWORD);
			Craft::$app->getElements()->saveElement($matchedUser, false);

			/*
			 * What if we fail here? We're far enough along, for now, we should go ahead and log the user in, but...
			 * TODO: Later, address this mess with a nice db Transaction or something.
			 */

		}

		/*
		 * Finally! Try to log the User in.
		 */

		if (!$this->login($matchedUser, $login->rememberMe))
		{
			/*
			 * We weren't able to log them in. Oh whale.  ¯\_(ツ)_/¯
			 */
			return false;
		}

		/*
		 * Hallelujah! They're logged in. Make a record of this, because from now on, they need to authenticate
		 * natively via Craft.
		 */

		$matchedUserRecord = new MatchedUser([
			'userId' => $matchedUser->id,
			'handlerName' => $handler->name,
			'handlerType' => $handler::TYPE,
			'legacyLoginName' => $login->loginName,
			'legacyUserId' => $legacyUserData['id'],
			'userCreated' => $newUserCreated,
			'passwordSet' => $handler->setPassword,
			'passwordResetRequired' => $handler->requirePasswordReset,
		]);

		$matchedUserRecord->save();

		return true;

	}

	/**
	 * Logs in a User.
	 *
	 * @param User $user
	 * @param bool $rememberMe
	 *
	 * @return bool Success
	 */
	public function login(User $user, $rememberMe = false)
	{

		/*
		 * c.f. UsersController::actionLogin()
		 */

		// Get the session duration
		$generalConfig = Craft::$app->getConfig()->getGeneral();
		if ($rememberMe && $generalConfig->rememberedUserSessionDuration !== 0) {
			$duration = $generalConfig->rememberedUserSessionDuration;
		} else {
			$duration = $generalConfig->userSessionDuration;
		}

		// Try logging them in
		if (!Craft::$app->getUser()->login($user, $duration)) {
			// Unknown error
			return false;
		}

		return true;

	}

}
