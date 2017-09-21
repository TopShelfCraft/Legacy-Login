<?php

namespace topshelfcraft\legacylogin\models\services;

use craft\base\Model;
use topshelfcraft\legacylogin\services\login\BaseLoginService;

/**
 * Class BaseLoginConfigModel
 * @property BaseLoginService $loginService
 */
abstract class BaseLoginConfigModel extends Model
{
    /** @var string TYPE */
    const TYPE = '';

    /** @var bool $configured */
    public $configured = false;

    /**
     * Get login service
     * @return BaseLoginService
     */
    abstract public function getLoginService() : BaseLoginService;
}
