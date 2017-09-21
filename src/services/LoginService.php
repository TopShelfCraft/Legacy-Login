<?php

namespace topshelfcraft\legacylogin\services;

use topshelfcraft\legacylogin\models\LoginModel;
use topshelfcraft\legacylogin\models\LoginResponseModel;
use topshelfcraft\legacylogin\models\SettingsModel;

/**
 * Class LoginService
 */
class LoginService extends BaseService
{
    /** @var SettingsModel $settings */
    protected $settings;

    /**
     * Log in
     * @param LoginModel $model
     * @return LoginResponseModel
     */
    public function login(LoginModel $model) : LoginResponseModel
    {
        // Iterate through service configs and attempt to log the user in
        foreach ($this->settings->getServiceConfig() as $serviceModel) {
            // If the service model is not configured, we should move on to the next one
            if (! $serviceModel->configured) {
                continue;
            }

            // If this service's login succeeds we can return the model response
            if ($serviceModel->getLoginService()->logIn($model)) {
                return new LoginResponseModel([
                    'success' => true,
                    'type' => $serviceModel::TYPE,
                ]);
            }
        }

        // Return unsuccessful model response
        return new LoginResponseModel([
            'error' => 'We were not able to login you in.',
        ]);
    }
}
