<?php
namespace Craft;

/**
 * LegacyLogin_AuthenticationHelper
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLogin_AuthenticationHelper
{

	/**
	 * Attempts to log in an unmatched legacy user.
	 * (A new Craft User is created if necessary.)
	 *
	 * @param string $loginName
	 * @param string $password
	 * @param bool $rememberMe
	 *
	 * @return bool
	 */
	public static function login($loginName = null, $password = null, $rememberMe = false)
	{

		// If we have already matched a Craft user to this legacy user, too bad.
		// (They're responsible for authenticating to their native account now.)

		// Grab the legacy user data, if it exists.
		// If not, we have no user to authenticate, so bail.

		// See if the legacy user authenticates...
		// If so, make/fetch a new matched user.

		// Try to log in as the matched user's Craft identity.
		// Return success.

		return false;

	}

	/**
	 * @param string $loginName
	 *
	 * @return LegacyLogin_MatchedUserModel|null
	 */
	public static function getMatchedUser($loginName = null)
	{

		// Validate loginName

		// See if matched user data exists.
		// If so, return the matched user model.

		return null;
	}

	/**
	 * @param string $loginName
	 *
	 * @return BaseRecord|null
	 */
	private static function _getLegacyUserData($loginName = null)
	{

		// Validate loginName

		// See if matched user data exists.
		// If so, return the legacy user data record.

		return null;

	}

	/**
	 * @param BaseRecord $legacyData
	 * @param string $password
	 *
	 * @return bool
	 */
	public static function authenticate($legacyData = null, $password = null)
	{

		// Validate loginName and password.
		// Check authentication.

		return false;

	}

	/**
	 * @param mixed $legacyData
	 * @param string $password
	 *
	 * @return LegacyLogin_MatchedUserModel|false
	 */
	public function makeMatchedUser($legacyData = null, $password = null)
	{

		// Grab settings from Craft config.

		// See if a matching Craft user already exists.

		// Start a transaction.

		// If we don't have a Craft user, make one.
		// Set the new password and/or password reset flag.
		// Make a new matched user, with Craft user attached.

		// If successful, commit the transaction and return the new matched user model.
		// Otherwise, roll back the transaction and return false.

		return false;

	}

}