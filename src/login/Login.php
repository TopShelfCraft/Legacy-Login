<?php
namespace TopShelfCraft\LegacyLogin\login;

use Craft;
use craft\elements\User;
use Exception;
use TopShelfCraft\LegacyLogin\LegacyLogin;

final class Login
{

	/**
	 * @internal
	 */
	public function authenticateNative(LoginRequest $login, User $user = null): bool
	{

		if (!$login->loginName || !$login->password)
		{
			return false;
		}

		if (!$user || $user->password === null) {
			/*
			 * Imitate the delay from $user->authenticate(), to thwart timing-based attacks.
			 * c.f. UsersController::actionLogin()
			 */
			Craft::$app->getSecurity()->validatePassword('p@ss1w0rd', '$2y$13$nj9aiBeb7RfEfYP3Cum6Revyu14QelGGxwcnFUKXIrQUitSodEPRi');
			return false;
		}

		if (!$user->authenticate($login->password))
		{
			return false;
		}

		return true;

	}

	/**
	 * @internal
	 */
	public function login(User $user, bool $rememberMe)
	{

		$generalConfig = Craft::$app->getConfig()->getGeneral();

		$duration = $generalConfig->userSessionDuration;

		if ($rememberMe && $generalConfig->rememberedUserSessionDuration !== 0)
		{
			$duration = $generalConfig->rememberedUserSessionDuration;
		}

		if (!Craft::$app->getUser()->login($user, $duration)) {
			throw new Exception("An error prevented the User from being logged in.");
		}

	}

}
