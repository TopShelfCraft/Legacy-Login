<?php

namespace topshelfcraft\legacylogin\models\services;

use topshelfcraft\legacylogin\services\login\BaseLoginService;

/**
 * Class EE2LoginConfigModel
 */
class EE2LoginConfigModel extends BaseLoginConfigModel
{
    /** @var string TYPE */
    const TYPE = 'ee2';

    /**
     * @inheritdoc
     */
    public function getLoginService(): BaseLoginService
    {
        // TODO: Implement getLoginService() method.
        var_dump('TODO: Implement getLoginService() method.');
        die;
    }
}
