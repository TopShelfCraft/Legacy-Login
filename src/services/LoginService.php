<?php

namespace topshelfcraft\legacylogin\services;

use topshelfcraft\legacylogin\models\LoginModel;

/**
 * Class LoginService
 */
class LoginService extends BaseService
{
    /**
     * Log in
     * @param LoginModel $model
     * @return string|bool
     */
    public function login(LoginModel $model)
    {
        return false;
    }
}
