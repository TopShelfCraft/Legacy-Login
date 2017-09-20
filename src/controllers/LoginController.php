<?php

namespace topshelfcraft\legacylogin\controllers;

use Craft;
use craft\web\Controller;
use topshelfcraft\legacylogin\LegacyLogin;
use topshelfcraft\legacylogin\models\LoginModel;
use \yii\base\Response;

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
     * @return Response|null
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
            // Check for ajax request
            if ($requestService->getIsAjax()) {
                return $this->asJson([
                    'success' => false,
                    'inputErrors' => $model->getErrors()
                ]);
            }

            // Set route variables
            Craft::$app->getUrlManager()->setRouteParams($model->toArray() + [
                'inputErrors' => $model->getErrors(),
            ]);

            // End processing
            return null;
        }

        // Attempt to log the user in
        $responseModel = LegacyLogin::$plugin->getLoginService()->login($model);

        // If the login attempt didn't go as planned...
        if (! $responseModel->success) {
            // Check for ajax request
            if ($requestService->getIsAjax()) {
                return $this->asJson($responseModel->toArray());
            }

            // Set route variables
            Craft::$app->getUrlManager()->setRouteParams(
                $responseModel->toArray()
            );

            // End processing
            return null;
        }

        // Handle the successful login
        return $this->handleSuccessfulLogin($responseModel->type);
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
