<?php
namespace Craft;

/**
 * LegacyLogin_Ee2Helper
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLogin_Ee2Helper extends LegacyLogin_AuthenticationHelper
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

        // Validate legacyData and password
        if (empty($loginName) || empty($password)) return false;

        LegacyLoginPlugin::log("Attempting to login {$loginName} as an EE2 legacy user...");

        // If we have already matched a Craft user to this legacy user, too bad.
        // (They're responsible for authenticating to their native account now.)
        $matchedUser = static::getMatchedUser($loginName);
        if (!empty($matchedUser))
        {
            LegacyLoginPlugin::log("{$loginName} has already been matched as a legacy user. Authentication aborted.");
            return false;
        }

        // Grab the legacy user data, if it exists.
        // If not, we have no user to authenticate, so bail.
        $legacyData = static::_getLegacyUserData($loginName);
        if (empty($legacyData))
        {
            LegacyLoginPlugin::log("No legacy user data could be found for {$loginName}.");
            return false;
        }

        // See if the legacy user authenticates...
        $userIsLegit = static::authenticate($legacyData, $password);
        if ($userIsLegit !== true)
        {
            // TODO: Set the userSession error message...?
            LegacyLoginPlugin::log("{$loginName} tried unsuccessfully to authenticate as a legacy user.");
            return false;
        }

        // The legacy user is legit. Make/fetch a new matched user.
        $matchedUser = static::makeMatchedUser($legacyData, $password);
        if (!$matchedUser instanceof LegacyLogin_MatchedUserModel)
        {
            LegacyLoginPlugin::log("{$loginName} was authenticated as a legacy user, but there was an error creating the new Matched User.", LogLevel::Warning);
            return false;
        };

        // Try to log in as the matched user's Craft identity.
        $success = craft()->userSession->loginByUserId($matchedUser->getCraftUser()->id, $rememberMe, true);
        if ($success)
        {
            LegacyLoginPlugin::log("{$loginName} logged in as an EE2 legacy user.", LogLevel::Info, true);
        }
        else
        {
            LegacyLoginPlugin::log("{$loginName} was authenticated as an EE2 legacy user, but could not be logged in.", LogLevel::Error);
        }
        return $success;

    }

    /**
     * @param string $loginName
     *
     * @return LegacyLogin_MatchedUserModel|null
     */
    public static function getMatchedUser($loginName = null)
    {

        // Validate loginName
        if (empty($loginName)) return null;

        // See if matched user data exists
        $matchedUserRecord = LegacyLogin_MatchedUserRecord::model()->find(array(
            'condition' => '(legacyUsername=:usernameOrEmail OR legacyEmail=:usernameOrEmail) AND legacyUserType=:legacyUserType',
            'params' => array(
                ':usernameOrEmail' => $loginName,
                ':legacyUserType' => LegacyLoginPlugin::EE2LegacyUserType
            ),
        ));

        if ($matchedUserRecord)
        {
            // TODO: Make sure there's only one match...
            return LegacyLogin_MatchedUserModel::populateModel($matchedUserRecord);
        }

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
        if (empty($loginName)) return null;

        // See if matched user data exists
        $legacyUserRecord = LegacyLogin_Ee2UserDataRecord::model()->find(array(
            'condition' => 'username=:loginName OR email=:loginName',
            'params' => array(
                ':loginName' => $loginName,
            ),
        ));

        if ($legacyUserRecord)
        {
            // TODO: Make sure there's only one match...
            return $legacyUserRecord;
        }

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

        // Validate legacyData and password
        if (empty($legacyData) || empty($password)) return false;

        // Make sure the user wasn't Banned or Pending
        if (empty($legacyData->group_id) || in_array($legacyData->group_id, [2,4])) return false;

        $legacySalt = $legacyData->salt;
        $legacyPassword = $legacyData->password;

        // Generate an EE2-style hashed pair
        $hashByteSize = strlen($legacyPassword);
        $hashedPair = static::_generateEe2HashedPair($password, $legacySalt, $hashByteSize);

        // If the hash generation was aborted, or if the password isn't a match... YOU SHALL NOT PASS!
        if ($hashedPair == false || $legacyPassword !== $hashedPair['password'])
        {
            return false;
        }

        // Otherwise, welcome in, old friend.
        return true;

    }

    /**
     * Hashing algorithms EE might be using, keyed by their respective hash lengths.
     *
     * @var array
     */
    private static $_hashAlgorithms = array(
        128		=> 'sha512',
        64		=> 'sha256',
        40		=> 'sha1',
        32		=> 'md5'
    );

    /**
     * Generate an EE2-style password hash.
     *
     * @param $password
     * @param bool $salt
     * @param bool $hashByteSize
     *
     * @return array|bool
     */
    private static function _generateEe2HashedPair($password, $salt = false, $hashByteSize = false)
    {

        // EE2 requires a password, and artificially limits the password length to avoid hash collisions
        if (!$password OR strlen($password) > 250)
        {
            return false;
        }

        // No hash function specified, or unrecognized hash length? Don't bother.
        if ($hashByteSize === false || !isset(static::$_hashAlgorithms[$hashByteSize]))
        {
            return false;
        }

        // Generate the hash, and send back the pair.
        return array(
            'salt'		=> $salt,
            'password'	=> hash(static::$_hashAlgorithms[$hashByteSize], $salt.$password),
        );

    }

    /**
     * @param mixed $legacyData
     * @param string $password
     *
     * @return LegacyLogin_MatchedUserModel|false
     */
    public static function makeMatchedUser($legacyData = null, $password = null)
    {

        // Validate legacyData and password
        if (empty($legacyData) || empty($password)) return false;

        // Grab settings from Craft config
        $setPassword = craft()->config->get('setPassword', 'legacylogin');
        $requirePasswordReset = craft()->config->get('requirePasswordReset', 'legacylogin');
        $matchBy = craft()->config->get('matchBy', 'legacylogin');

        // Try to find a matching Craft user: by username, email, or both, according to config
        if (empty($craftUser) && in_array($matchBy, ['email','both']))
        {
            // See if a matching Craft user exists, by email.
            $craftUser = craft()->users->getUserByUsernameOrEmail($legacyData->email);
        }
        if (empty($craftUser) && in_array($matchBy, ['username','both']))
        {
            // See if a matching Craft user exists, by username.
            $craftUser = craft()->users->getUserByUsernameOrEmail($legacyData->username);
        }

        // Start a transaction
        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;

        // If we still don't have a Craft user, make one.

        if (empty($craftUser))
        {

            $craftUser = new UserModel();
            $craftUser->username  = $legacyData->username;
            $craftUser->email     = $legacyData->email;

            $success = craft()->users->saveUser($craftUser);

            if (!$success)
            {
                LegacyLoginPlugin::log("Couldn't save the user {$craftUser->username}.", LogLevel::Error);

            }
            else{
                craft()->userGroups->assignUserToDefaultGroup($craftUser);
            }
        }
        else {
            $success = true;
        }

        // Set the new password and/or password reset flag

        if ($success)
        {

            if ($setPassword)
            {
                LegacyLoginPlugin::log("Password was set from legacy data for {$legacyData->email}.", LogLevel::Info, true);
                $craftUser->newPassword = $password;
            }
            $success = craft()->users->saveUser($craftUser);

            if ($requirePasswordReset)
            {
                LegacyLoginPlugin::log("{$legacyData->email} will need to reset their password.", LogLevel::Info, true);
                $craftUser->passwordResetRequired = $requirePasswordReset;
            }
            $success = $success && craft()->users->saveUser($craftUser);

            if (!$success)
            {
                LegacyLoginPlugin::log("There was an error processing new password profile for {$craftUser->username}.", LogLevel::Error);

            }

        }

        // Make a new matched user, with Craft user attached

        if ($success)
        {

            $matchedUser = new LegacyLogin_MatchedUserRecord();
            $matchedUser->craftUserId = $craftUser->id;
            $matchedUser->legacyUserType = LegacyLoginPlugin::EE2LegacyUserType;
            $matchedUser->legacyRecordId = $legacyData->id;
            $matchedUser->legacyUserId = $legacyData->member_id;
            $matchedUser->legacyUsername = $legacyData->username;;
            $matchedUser->legacyEmail = $legacyData->email;
            $matchedUser->passwordSet = $setPassword;
            $success = $matchedUser->save();

            if (!$success)
            {
                LegacyLoginPlugin::log("There was an error saving the new Matched User for {$craftUser->username}.", LogLevel::Error);

            }

        }

        if ($success)
        {

            if ($transaction !== null)
            {
                $transaction->commit();
            }

            return LegacyLogin_MatchedUserModel::populateModel($matchedUser);

        }
        else
        {

            if ($transaction !== null)
            {
                $transaction->rollback();
            }

            return false;

        }

    }

}
