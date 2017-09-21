<?php

namespace topshelfcraft\legacylogin\models;

use craft\base\Model;

/**
 * Class MatchedUserModel
 */
class MatchedUserModel extends Model
{
    /** @var int $id */
    public $id;

    /** @var int $userId */
    public $userId;

    /** @var string $legacyUserType */
    public $legacyUserType;

    /** @var string $legacyUsername */
    public $legacyUsername;

    /** @var string $legacyEmail */
    public $legacyEmail;

    /** @var bool $passwordSet */
    public $passwordSet;

    /** @var int $legacyLoginCount */
    public $legacyLoginCount;
}
