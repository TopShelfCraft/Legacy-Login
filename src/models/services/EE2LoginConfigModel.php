<?php

namespace topshelfcraft\legacylogin\models\services;

use topshelfcraft\legacylogin\LegacyLogin;
use topshelfcraft\legacylogin\services\login\BaseLoginService;
use topshelfcraft\legacylogin\services\login\EE2LoginService;

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
        $instance = LegacyLogin::$plugin;

        return new EE2LoginService([
            'config' => $this,
            'matchedUserService' => $instance->getMatchedUserService(),
            'queryBuilder' => $this->getQueryBuilder(),
            'craftUserService' => $instance->getCraftUserService(),
        ]);
    }
}
