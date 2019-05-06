<?php
namespace topshelfcraft\legacylogin\handers;

use Craft;
use craft\elements\User;
use topshelfcraft\legacylogin\models\Login;
use yii\base\InvalidConfigException;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
class Craft2AuthHandler extends BaseDbTypeAuthHandler
{

	/**
	 * @var string TYPE
	 */
	const TYPE = 'Craft2';


	/**
	 * Returns the legacy user data for the given Login attempt, or `null` if no matching legacy user is found.
	 *
	 * @param Login $login
	 *
	 * @return mixed
	 * @throws InvalidConfigException
	 */
	public function getLegacyUserData(Login $login)
	{

		$params = [
			':loginName' => $login->loginName
		];

		$legacyUserData = $this->getQuery()
			->from('{{'.$this->userTable.'}}')
			->where('`username` = :loginName', $params)
			->orWhere('`email` = :loginName', $params)
			->one();

		return $legacyUserData;

	}

	/**
	 * Returns whether the handler is able to authenticate a legacy user with the given user data and password.
	 *
	 * @param mixed $userData
	 * @param Login $login
	 *
	 * @return bool
	 */
	public function _authenticate($userData, Login $login): bool
	{

		if ((bool) $userData['pending'])
		{
			return false;
		}

		return Craft::$app->getSecurity()->validatePassword($login->password, $userData['password']);

	}

	/**
	 * Returns the User who matches the given legacy user data, or `null` if no such User exists yet.
	 *
	 * @param mixed $userData
	 *
	 * @return User|null
	 */
	public function getMatchedUser($userData = null): User
	{

		return Craft::$app->getUsers()->getUserByUsernameOrEmail($userData['email'])
			?? Craft::$app->getUsers()->getUserByUsernameOrEmail($userData['username']);

	}

	/**
	 * @param mixed $userData
	 *
	 * @return User
	 */
	public function prepNewUser($userData = null): User
	{

		$user = new User();

		if (Craft::$app->getConfig()->getGeneral()->useEmailAsUsername) {
			$user->username = $userData['email'];
		} else {
			$user->username = $userData['username'] ?: $userData['email'];
		}

		$user->firstName = $userData['firstName'];
		$user->lastName = $userData['lastName'];

		$user->locked = (bool) $userData['locked'];
		$user->suspended = (bool) $userData['suspended'];

		return $user;

	}

}
