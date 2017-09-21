<?php

namespace topshelfcraft\legacylogin\services\login;

use topshelfcraft\legacylogin\models\LoginModel;
use topshelfcraft\legacylogin\models\services\BaseLoginConfigModel;
use topshelfcraft\legacylogin\services\BaseService;

/**
 * Class BaseLoginService
 */
abstract class BaseLoginService extends BaseService
{
    /** @var BaseLoginConfigModel $config */
    protected $config;

    /**
     * Logs user in
     * @param LoginModel $model
     * @return bool
     */
    abstract public function logIn(LoginModel $model) : bool ;
}
