<?php

namespace topshelfcraft\legacylogin\services;

use Craft;
use craft\services\Users;
use topshelfcraft\legacylogin\models\LoginModel;
use craft\config\GeneralConfig;
use craft\web\User as CurrentUser;
use craft\elements\User;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

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
     * @throws \Exception
     * @throws \Throwable
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

        // Check if we should set the password
        if ($setPassword) {
            $user->newPassword = $model->password;
        }

        // Check if we should require reset
        if ($requireReset) {
            $user->passwordResetRequired = true;
        }

        // Save the user
        if (! Craft::$app->getElements()->saveElement($user)) {
            return null;
        }

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
     * @return User
     * @throws \Exception
     */
    private function createUser($model) : User
    {
        // Check if this is client edition or other
        if (Craft::$app->getEdition() === Craft::Client) {
            // Make sure there's no Client user yet
            $clientExists = User::find()
                ->client()
                ->status(null)
                ->exists();

            // If there is a client user, we got problems, son.
            if ($clientExists) {
                throw new BadRequestHttpException(
                    'A client account already exists'
                );
            }

            // Create a new user
            $user = new User();

            // Set user as client
            $user->client = true;
        } else {
            // Make sure this is Craft Pro, since that's required for having
            // multiple user accounts
            Craft::$app->requireEdition(Craft::Pro);

            // Make sure public registration is allowed
            if (! Craft::$app->getSystemSettings()->getSetting(
                'users',
                'allowPublicRegistration'
            )) {
                throw new ForbiddenHttpException(
                    'Public registration is not allowed'
                );
            }

            // Create a new user
            $user = new User();
        }

        // Set username and email
        $user->username = $model->username;
        $user->email = $model->email;

        // Return the user model
        return $user;
    }
}
