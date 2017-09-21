<?php

namespace topshelfcraft\legacylogin\models\services;

use Craft;
use topshelfcraft\legacylogin\services\login\BaseLoginService;
use topshelfcraft\legacylogin\services\login\CraftLoginService;

/**
 * Class CraftLoginConfigModel
 */
class CraftLoginConfigModel extends BaseLoginConfigModel
{
    /** @var string TYPE */
    const TYPE = 'craft';

    /**
     * @inheritdoc
     */
    public function getLoginService() : BaseLoginService
    {
        return new CraftLoginService([
            'config' => $this,
            'usersService' => Craft::$app->getUsers(),
            'generalConfig' => Craft::$app->getConfig()->getGeneral(),
            'currentUser' => Craft::$app->getUser(),
        ]);
    }
}
