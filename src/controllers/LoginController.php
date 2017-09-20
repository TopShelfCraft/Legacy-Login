<?php

namespace topshelfcraft\legacylogin\controllers;

use Craft;
use craft\web\Controller;
use topshelfcraft\legacylogin\LegacyLogin;
use topshelfcraft\legacylogin\models\LoginModel;

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
            return $this->handleSuccessfulLogin();
        }

        // A little house-cleaning for expired, pending users.
        // (Same as in the UsersController, natch.)
        Craft::$app->getUsers()->purgeExpiredPendingUsers();

        // Okay, let's go

        // Get the request service
        $requestService = Craft::$app->getRequest();

        // Get posted inputs and assign them to the model
        $model = new LoginModel([
            'username' => $requestService->post('username'),
            'password' => $requestService->post('password'),
            'rememberMe' => (bool) $requestService->post('rememberMe'),
        ]);

        // Check if the model validates
        if (! $model->validate()) {
            // Set route variables
            Craft::$app->getUrlManager()->setRouteParams($model->toArray() + [
                'inputErrors' => $model->getErrors(),
            ]);

            // End processing
            return null;
        }

        // Attempt to log the user in
        if (! $type = LegacyLogin::$plugin->getLoginService()->login($model)) {
            // TODO: implement the login service method
            var_dump('hey, we should do error stuff here');
            die;
        }

        // Handle the successful login
        return $this->handleSuccessfulLogin($type);
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
