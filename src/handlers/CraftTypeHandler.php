<?php
namespace TopShelfCraft\LegacyLogin\handlers;

use Craft;
use craft\db\Query;
use craft\elements\User;
use TopShelfCraft\LegacyLogin\login\LoginRequest;

abstract class CraftTypeHandler extends DbTypeHandler
{

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

		// Make sure they're not Pending or Suspended
		if ((bool) $legacyData['pending'] || (bool) $legacyData['suspended'])
		{
			return false;
		}

		return Craft::$app->getSecurity()->validatePassword($login->password, $legacyData['password']);

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
		return $legacyData['id'] ?? null;
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
		$user->firstName = $legacyData['firstName'];
		$user->lastName = $legacyData['lastName'];

	}

	private function _getLegacyData(string $loginName): ?array
	{

		return (new Query())
			->from($this->table)
			->where(['username' => $loginName])
			->orWhere(['email' => $loginName])
			->one($this->getConnection());

	}

}
