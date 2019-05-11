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
class EE2AuthHandler extends BaseDbTypeAuthHandler
{

	/**
	 * @var string TYPE
	 */
	const TYPE = 'EE2';

	/**
	 * @var array HASH_ALGORITHMS
	 */
	const HASH_ALGORITHMS = [
		32 => 'md5',
		40 => 'sha1',
		64 => 'sha256',
		128 => 'sha512',
	];


	/*
	 * Public functions
	 */

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

		// Make sure user was not Banned or Pending
		if (empty($userData['group_id'] || in_array($userData['group_id'], [2, 4])))
		{
			return false;
		}

		// Generate an EE2-style hashed pair
		$hashedPair = $this->_generateHashedPair(
			$login->password,
			$userData['salt'],
			strlen($userData['password'])
		);

		/*
		 * If the hash generation was aborted, or if the password isn't a match... YOU SHALL NOT PASS!
		 * Otherwise... Welcome in, old friend.
		 */
		return ($hashedPair !== false && $userData['password'] === $hashedPair['password']);

	}

	/**
	 * Returns the User who matches the given legacy user data, or `null` if no such User exists yet.
	 *
	 * @param mixed $userData
	 *
	 * @return User|null
	 */
	public function getMatchedUser($userData = null): ?User
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

		return $user;

	}

	/*
	 * Private functions
	 */

	/**
	 * Generate EE2-style hashed pair.
	 *
	 * @param string $password
	 * @param string $salt
	 * @param int $hashByteSize
	 *
	 * @return array|bool
	 */
	private function _generateHashedPair(string $password, string $salt, int $hashByteSize)
	{

		/*
		 * EE2 requires a password, and artificially limits the password length to avoid hash collisions
		 */
		if (!$password || strlen($password) > 250) {
			return false;
		}

		/*
		 * Unspecified or unrecognized hash length? Don't bother.
		 */
		if ($hashByteSize === false || !isset(self::HASH_ALGORITHMS[$hashByteSize]))
		{
			return false;
		}

		// Generate the hash, and send back the pair.
		return [
			'salt' => $salt,
			'password' => hash(self::HASH_ALGORITHMS[$hashByteSize], $salt . $password),
		];

	}

}
