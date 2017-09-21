<?php

namespace topshelfcraft\legacylogin\models\services;
use topshelfcraft\legacylogin\services\login\BaseLoginService;

/**
 * Class BigCommerceLoginConfigModel
 */
class BigCommerceLoginConfigModel extends BaseLoginConfigModel
{
    /** @var string TYPE */
    const TYPE = 'bigCommerce';

    /**
     * @inheritdoc
     */
    public function getLoginService() : BaseLoginService
    {
        // TODO: Implement getLoginService() method.
        var_dump('TODO: Implement getLoginService() method.');
        die;
    }
}
