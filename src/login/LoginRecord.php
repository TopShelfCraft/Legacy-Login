<?php
namespace TopShelfCraft\LegacyLogin\login;

use Craft;
use craft\db\ActiveRecord;
use craft\elements\User;

/**
 * @author Michael Rog <michael@michaelrog.com>
 *
 * @property int $userId
 * @property string $handlerName
 * @property string $handlerType
 * @property string $legacyUserId
 * @property bool $userCreated
 * @property bool $userUpdated
 * @property bool $passwordResetRequired
 */
class LoginRecord extends ActiveRecord
{

	const TABLE_NAME = 'legacylogin_logins';

	public function getUser(): ?User
	{
		return $this->userId ? Craft::$app->users->getUserById($this->userId) : null;
	}

	public static function tableName(): string
	{
		return '{{%' . static::TABLE_NAME . '}}';
	}

}
