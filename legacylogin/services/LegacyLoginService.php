<?php
namespace Craft;

/**
 * LegacyLoginService provides an API for managing user authentication via legacy systems.
 *
 * An instance of UserSessionService is globally accessible in Craft via
 * {@link WebApp::userSession `craft()->legacyLogin`}.
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLoginService extends \CWebUser
{
    // Public Methods
    // =========================================================================

    // User Identity/Authentication
    // -------------------------------------------------------------------------

    /**
     * Logs a user in, using the local Craft user-base if possible, or falling back to legacy user-base data.
     *
     * @param string $username   The user’s username.
     * @param string $password   The user’s submitted password.
     * @param bool   $rememberMe Whether the user should be remembered.
     *
     * @throws Exception
     * @return bool Whether the user was logged in successfully.
     */
    public function login($username, $password, $rememberMe = false)
    {

        // First, try logging in a native User.

        $nativeSuccess = craft()->userSession->login($username, $password, $rememberMe);
        if ($nativeSuccess === true) return LegacyLoginPlugin::NativeUserType;

        // Okay, we'll try to match and validate a legacy user...
        // First, validate the provided username/password.

        $usernameModel = new UsernameModel(['username' => $username]);
        $passwordModel = new PasswordModel(['password' => $password]);

        if (!$usernameModel->validate() || !$passwordModel->validate())
        {
            LegacyLoginPlugin::log($username.' tried to log in unsuccessfully, but there was a validation issue with the username or password.', LogLevel::Warning);
            return false;
        }

        // Okay, we have a valid username and password... Can we authenticate a legacy user?

        $allowedServices = craft()->config->get('allowedServices', 'legacylogin');

        // Bail if we're mis-configured

        if (!is_array($allowedServices)) return false;

        // Try each service in sequence...

        foreach($allowedServices as $service)
        {
            switch ($service)
            {

                case LegacyLoginPlugin::BigCommerceLegacyUserType:
                    if (LegacyLogin_BigCommerceHelper::login($username, $password, $rememberMe))
                        return LegacyLoginPlugin::BigCommerceLegacyUserType;
                    break;
                case LegacyLoginPlugin::EE2LegacyUserType:
                    if (LegacyLogin_Ee2Helper::login($username, $password, $rememberMe))
                        return LegacyLoginPlugin::EE2LegacyUserType;
                    break;
                case LegacyLoginPlugin::WellspringLegacyUserType:
                    if (LegacyLogin_WellspringHelper::login($username, $password, $rememberMe))
                        return LegacyLoginPlugin::WellspringLegacyUserType;
                    break;
                case LegacyLoginPlugin::WordPressLegacyUserType:
                    if (LegacyLogin_WordPressHelper::login($username, $password, $rememberMe))
                        return LegacyLoginPlugin::WordPressLegacyUserType;
                    break;
            }
        }

        // Alas, it just wasn't meant to be.

        LegacyLoginPlugin::log($username.' could not be authenticated as a legacy user.', LogLevel::Warning);
        return false;

    }

}
