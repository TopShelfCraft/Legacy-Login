<?php

namespace topshelfcraft\legacylogin\models\services;

use topshelfcraft\legacylogin\services\login\BaseLoginService;

/**
 * Class WellspringLoginConfigModel
 */
class WellspringLoginConfigModel extends BaseLoginConfigModel
{
    /** @var string TYPE */
    const TYPE = 'wellspring';

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
