<?php

namespace topshelfcraft\legacylogin\services;

use topshelfcraft\legacylogin\models\LoginModel;
use topshelfcraft\legacylogin\models\LoginResponseModel;

/**
 * Class LoginService
 */
class LoginService extends BaseService
{
    /**
     * Log in
     * @param LoginModel $model
     * @return LoginResponseModel
     */
    public function login(LoginModel $model) : LoginResponseModel
    {
        return new LoginResponseModel();
    }
}
