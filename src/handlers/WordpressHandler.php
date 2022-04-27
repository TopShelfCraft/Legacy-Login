<?php
namespace TopShelfCraft\LegacyLogin\handlers;

use Craft;
use craft\db\Query;
use craft\elements\User;
use TopShelfCraft\LegacyLogin\libraries\PasswordHash;
use TopShelfCraft\LegacyLogin\login\LoginRequest;

final class WordpressHandler extends DbTypeHandler
{

	const TYPE = 'WordPress';

	public $table = 'wp_users';

	public function authenticate(LoginRequest $login): bool
	{

		if (empty($login->loginName) || empty($login->password))
		{
			return false;
		}

		$legacyData = $this->_getLegacyData($login->loginName);

		if (!$legacyData || !$legacyData['user_pass'])
		{
			return false;
		}

		$hash = $legacyData['user_pass'];

		// Once upon a time, WP used md5 hashes. Check for that first.
		if (strlen($hash) <= 32)
		{
			if ($hash == md5($login->password))
			{
				return true;
			}
		}

		// The current hash is not md5. Use the WP crypto library to check it.
		$wp_hasher = new PasswordHash(8, true);
		return $wp_hasher->CheckPassword($login->password, $hash);

	}

	public function getMatchedUser(LoginRequest $login): ?User
	{

		$legacyData = $this->_getLegacyData($login->loginName);

		if (!$legacyData)
		{
			return null;
		}

		return Craft::$app->users->getUserByUsernameOrEmail($legacyData['user_email'])
			?? Craft::$app->users->getUserByUsernameOrEmail($legacyData['user_login']);

	}

	protected function getLegacyUserId(LoginRequest $login)
	{
		$legacyData = $this->_getLegacyData($login->loginName);
		return $legacyData['ID'] ?? null;
	}

	protected function prepUser(User $user, LoginRequest $login): void
	{

		$user->newPassword = $login->password;

		$legacyData = $this->_getLegacyData($login->loginName);

		if (!$legacyData)
		{
			return;
		}

		if (Craft::$app->getConfig()->getGeneral()->useEmailAsUsername)
		{
			$user->username = $legacyData['user_email'];
		}
		else
		{
			$user->username = $legacyData['user_login'] ?: $legacyData['user_email'];
		}

		$user->email = $legacyData['user_email'];

	}

	private function _getLegacyData(string $loginName)
	{
		return (new Query())
			->from($this->table)
			->where(['user_login' => $loginName])
			->orWhere(['user_email' => $loginName])
			->one($this->getConnection());
	}

}
