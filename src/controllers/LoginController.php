<?php

namespace topshelfcraft\legacylogin\controllers;

use Craft;
use craft\web\Controller;

/**
 * Login Controller
 */
class LoginController extends Controller
{
    /**
     * @inheritdoc
     */
    protected $allowAnonymous = [
        'actionLogin'
    ];

    /**
     * Handles log-in via POST request
     * @return null
     * @throws \Exception
     */
    public function actionLogin()
    {
        // Bail if this isn't a POST request
        $this->requirePostRequest();

        // If user is already logged in skip to processing as successful
        if (Craft::$app->getUser()->identity !== null) {
            $this->handleSuccessfulLogin();
        }

        // A little house-cleaning for expired, pending users.
        // (Same as in the UsersController, natch.)
        Craft::$app->getUsers()->purgeExpiredPendingUsers();

        // Okay, let's go

        // Get the request service
        $requestService = Craft::$app->getRequest();

        $loginName = $requestService->post('loginName');
        $password = $requestService->post('password');
        $rememberMe = (bool) $requestService->post('rememberMe');

        // TODO: implement the login service method
        var_dump('hey, we should do some stuff here');
        die;
    }

    /**
     * Handles the post-login process
     * @param string $loginType legacy|craft
     * @param bool $setNotice Whether a flash notice should be set if not AJAX
     */
    private function handleSuccessfulLogin(
        string $loginType = '',
        bool $setNotice = true
    ) {
        // TODO: handle successful login
        var_dump($loginType);
        var_dump($setNotice);
        die;
    }
}
