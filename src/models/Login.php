<?php
namespace topshelfcraft\legacylogin\models;

use craft\base\Model;
use craft\elements\User;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
class Login extends Model
{

    /**
	 * @var string $loginName
	 */
    public $loginName = '';

    /**
	 * @var string $password
	 */
    public $password = '';

    /**
	 * @var bool $rememberMe
	 */
    public $rememberMe = false;

	/**
	 * @var User $user
	 */
	public $user;

	/**
	 * @var string The error code, if the login has failed.
	 */
	public $authError;


    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [[ 'loginName', 'password'], 'required'],
        ];
    }

}
