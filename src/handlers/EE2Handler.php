<?php
namespace TopShelfCraft\LegacyLogin\handlers;

use Craft;
use craft\db\Query;
use craft\elements\User;
use TopShelfCraft\LegacyLogin\login\LoginRequest;

final class EE2Handler extends DbTypeHandler
{

	const TYPE = 'EE2';

	/**
	 * @var array A map of hash lengths to algorithms
	 */
	const HASH_ALGORITHMS = [
		32 => 'md5',
		40 => 'sha1',
		64 => 'sha256',
		128 => 'sha512',
	];

	public function authenticate(LoginRequest $login): bool
	{

		if (!$login->loginName || !$login->password)
		{
			return false;
		}

		$legacyData = $this->_getLegacyData($login->loginName);

		if (!$legacyData || !$legacyData['password'])
		{
			return false;
		}

		// Make sure user was not Banned or Pending
		if (empty($legacyData['group_id'] || in_array($legacyData['group_id'], [2, 4])))
		{
			return false;
		}

		// Generate an EE2-style hashed pair
		$hashedPair = $this->_generateHashedPair(
			$login->password,
			$legacyData['salt'],
			strlen($legacyData['password'])
		);

		/*
		 * If the hash generation was aborted, or if the password isn't a match... YOU SHALL NOT PASS!
		 * Otherwise... Welcome in, old friend.
		 */
		return ($hashedPair !== false && $legacyData['password'] === $hashedPair['password']);

	}

	public function getMatchedUser(LoginRequest $login): ?User
	{

		$legacyData = $this->_getLegacyData($login->loginName);

		if (!$legacyData)
		{
			return null;
		}

		return Craft::$app->users->getUserByUsernameOrEmail($legacyData['email'])
			?? Craft::$app->users->getUserByUsernameOrEmail($legacyData['username']);

	}

	protected function getLegacyUserId(LoginRequest $login)
	{
		$legacyData = $this->_getLegacyData($login->loginName);
		return $legacyData['member_id'] ?? null;
	}

	protected function prepUser(User $user, LoginRequest $login): void
	{

		$legacyData = $this->_getLegacyData($login->loginName);

		if (!$legacyData)
		{
			return;
		}

		if (Craft::$app->getConfig()->getGeneral()->useEmailAsUsername)
		{
			$user->username = $legacyData['email'];
		}
		else
		{
			$user->username = $legacyData['username'] ?: $legacyData['email'];
		}

		$user->email = $legacyData['email'];

	}

	/**
	 * Generate EE2-style hashed pair.
	 *
	 * @return array|false
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

	private function _getLegacyData(string $loginName)
	{
		return (new Query())
			->from($this->table)
			->where(['username' => $loginName])
			->orWhere(['email' => $loginName])
			->one($this->getConnection());
	}

}
