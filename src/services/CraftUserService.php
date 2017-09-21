<?php

namespace topshelfcraft\legacylogin\services;

use Craft;
use craft\services\Users;
use topshelfcraft\legacylogin\models\LoginModel;
use craft\config\GeneralConfig;
use craft\web\User as CurrentUser;
use craft\elements\User;

/**
 * Class CraftUserService
 */
class CraftUserService extends BaseService
{
    /** @var Users $usersService */
    protected $usersService;

    /** @var GeneralConfig $generalConfig */
    protected $generalConfig;

    /** @var CurrentUser $currentUser */
    protected $currentUser;

    /**
     * Log user in
     * @param LoginModel $model
     * @param bool $setPassword
     * @param bool $requireReset
     * @return User
     */
    public function logUserInFromLegacy(
        LoginModel $model,
        bool $setPassword = true,
        bool $requireReset = false
    ) : User {
        // First, a little house-cleaning for expired, pending users.
        $this->usersService->purgeExpiredPendingUsers();

        // Check for an existing user
        $user = $this->usersService->getUserByUsernameOrEmail($model->username);

        // If there's no user, we need to create one
        if (! $user) {
            $user = $this->createUser($model);
        }

        // Delay randomly between 0 and 1.5 seconds.
        usleep(random_int(0, 1500000));

        // If the user is not of active status, they cannot be logged in
        if ($user->getStatus() !== $user::STATUS_ACTIVE) {
            return null;
        }

        // Get standard session duration
        $duration = $this->generalConfig->userSessionDuration;

        // If user has opted to "remember me", get remembered session duration
        if ($model->rememberMe &&
            $this->generalConfig->rememberedUserSessionDuration !== 0
        ) {
            $duration = $this->generalConfig->rememberedUserSessionDuration;
        }

        // Log user in
        if (! $this->currentUser->login($user, $duration)) {
            return null;
        }

        // All done
        return $user;
    }

    /**
     * Create user
     * @param LoginModel $model
     */
    private function createUser($model)
    {
        $edition = Craft::$app->getEdition();
        $userComponent = Craft::$app->getUser();
        $currentUser = $userComponent->getIdentity();
        $thisIsPublicRegistration = false;

        var_dump(\Craft::$app->getEdition());
        die;
    }
}
