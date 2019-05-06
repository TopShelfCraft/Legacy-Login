<?php
namespace topshelfcraft\legacylogin\models;

use craft\base\Model;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
class MatchedUser extends Model
{

    /**
	 * @var int $id
	 */
    public $id;

    /**
	 * @var int $userId
	 */
    public $userId;

	/**
	 * @var string $handlerName
	 */
	public $handlerName;

	/**
	 * @var string $handlerType
	 */
	public $handlerType;

	/**
	 * @var string $legacyLoginName
	 */
	public $legacyLoginName;

	/**
	 * @var string $legacyUserId
	 */
	public $legacyUserId;

	/**
	 * @var bool $userCreated
	 */
	public $userCreated;

    /**
	 * @var bool $passwordSet
	 */
    public $passwordSet;

	/**
	 * @var bool $resetRequired
	 */
	public $passwordResetRequired;

}
