<?php
namespace topshelfcraft\legacylogin\handers;

use craft\base\Component;
use craft\elements\User;
use topshelfcraft\legacylogin\models\Login;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
abstract class BaseAuthHandler extends Component
{

	/**
	 * @var string TYPE
	 */
	const TYPE = '';


	/*
	 * Public properties
	 */

	/**
	 * @var string $handle
	 */
	public $name = '';

	/**
	 * @var bool $createNewUser
	 */
	public $createNewUser = true;

	/**
	 * @var bool $setPassword
	 */
	public $setPassword = true;

	/**
	 * @var bool $requirePasswordReset
	 */
	public $requirePasswordReset = false;


	/*
	 * Public functions
	 */

	/**
	 * Returns the legacy user data for the given Login attempt, or `null` if no matching legacy user is found.
	 *
	 * @param Login $login
	 *
	 * @return mixed
	 */
	abstract public function getLegacyUserData(Login $login);

	/**
	 * Returns whether the handler is able to authenticate a legacy user with the given user data and password.
	 *
	 * @param mixed $userData
	 * @param Login $login
	 *
	 * @return bool
	 */
	abstract public function _authenticate($userData, Login $login): bool;

	/**
	 * Returns the User who matches the given legacy user data, or `null` if no such User exists yet.
	 *
	 * @param mixed $userData
	 *
	 * @return User|null
	 */
	abstract public function getMatchedUser($userData = null): ?User;

	/**
	 * @param mixed $userData
	 *
	 * @return User
	 */
	abstract public function prepNewUser($userData = null): User;

}
