<?php

namespace topshelfcraft\legacylogin\services\login;

use craft\db\Query;
use topshelfcraft\legacylogin\models\LoginModel;
use topshelfcraft\legacylogin\models\MatchedUserModel;
use topshelfcraft\legacylogin\models\services\BaseLoginConfigModel;
use topshelfcraft\legacylogin\services\BaseService;
use topshelfcraft\legacylogin\services\CraftUserService;
use topshelfcraft\legacylogin\services\MatchedUserService;

/**
 * Class BaseLoginService
 */
abstract class BaseLoginService extends BaseService
{
    /** @var BaseLoginConfigModel $config */
    protected $config;

    /** @var MatchedUserService $matchedUserService */
    protected $matchedUserService;

    /** @var Query $queryBuilder */
    protected $queryBuilder;

    /** @var CraftUserService $craftUserService */
    protected $craftUserService;

    /**
     * Logs user in
     * @param LoginModel $model
     * @return bool
     */
    abstract public function logIn(LoginModel $model) : bool;

    /**
     * Check if user is over the max legacy log in threshold
     * @param MatchedUserModel $matchedUserModel
     * @return bool
     */
    protected function overMaxLegacyLoginThreshold($matchedUserModel) : bool
    {
        // Check if we have a matched user model
        if (! $matchedUserModel) {
            return false;
        }

        // Check if maxLegacyLogIns has been set
        if (! $max = $this->config->maxLegacyLogIns) {
            return false;
        }

        // Check if we're over threshold
        return $matchedUserModel->legacyLoginCount >= $max;
    }
}
