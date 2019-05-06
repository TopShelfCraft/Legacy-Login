<?php
namespace topshelfcraft\legacylogin\records;

use craft\db\ActiveRecord;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 *
 * @property int $userId
 * @property string $handlerName
 * @property string $handlerType
 * @property string $legacyLoginName
 * @property string $legacyUserId
 * @property bool $userCreated
 * @property bool $passwordSet
 * @property bool $passwordResetRequired
 */
class MatchedUser extends ActiveRecord
{

	const TABLE_NAME = 'legacylogin_matchedusers';

	/*
	 * Static methods
	 */

	/**
	 * @return string
	 */
	public static function tableName()
	{
		return '{{%' . static::TABLE_NAME . '}}';
	}

}
