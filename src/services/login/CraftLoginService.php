<?php

namespace topshelfcraft\legacylogin\services\login;

use topshelfcraft\legacylogin\models\LoginModel;
use craft\services\Users;
use craft\config\GeneralConfig;
use craft\web\User;

/**
 * Class CraftLoginService
 */
class CraftLoginService extends BaseLoginService
{
    /** @var Users $usersService */
    protected $usersService;

    /** @var GeneralConfig $generalConfig */
    protected $generalConfig;

    /** @var User $currentUser */
    protected $currentUser;

    /**
     * @inheritdoc
     */
    public function logIn(LoginModel $model) : bool
    {
        // Get the user
        $user = $this->usersService->getUserByUsernameOrEmail($model->username);

        // If no user, return false
        if (! $user) {
            return false;
        }

        // Authenticate the user
        if (! $user->authenticate($model->password)) {
            return false;
        }

        // Get standard session duration
        $duration = $this->generalConfig->userSessionDuration;

        // If user has opted to "remember me", get rememberd session duration
        if ($model->rememberMe &&
            $this->generalConfig->rememberedUserSessionDuration !== 0
        ) {
            $duration = $this->generalConfig->rememberedUserSessionDuration;
        }

        // Log user in
        return $this->currentUser->login($user, $duration);
    }
}
