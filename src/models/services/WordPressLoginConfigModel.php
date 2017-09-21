<?php

namespace topshelfcraft\legacylogin\models\services;

use topshelfcraft\legacylogin\services\login\BaseLoginService;

/**
 * Class WordPressLoginConfigModel
 */
class WordPressLoginConfigModel extends BaseLoginConfigModel
{
    /** @var string TYPE */
    const TYPE = 'wordPress';

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
