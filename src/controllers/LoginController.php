<?php

namespace topshelfcraft\legacylogin\controllers;

use Craft;
use craft\helpers\UrlHelper;
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
        if (Craft::$app->getUser()->getIdentity() !== null) {
            return $this->handleSuccessfulLogin();
        }

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
     * @return Response
     * @throws \Exception
     */
    private function handleSuccessfulLogin(
        string $loginType = '',
        bool $setNotice = true
    ) : Response {
        // Get the session service
        $sessionService = Craft::$app->getSession();

        // Get the current user
        $currentUser = Craft::$app->getUser()->getIdentity();

        // Find out if they were trying to access a URL beforehand
        $returnUrl = Craft::$app->getUser()->getReturnUrl();

        // Get request service
        $requestService = Craft::$app->getRequest();

        if ($returnUrl === null ||
            $returnUrl === $requestService->getFullPath()
        ) {
            // If this is a CP request and they can access CP, send them
            // wherever postCpLoginRedirect tells us
            if ($requestService->getIsCpRequest() &&
                $currentUser->can('accessCp')
            ) {
                $postCpLoginRedirect = Craft::$app->getConfig()
                    ->getGeneral()
                    ->postCpLoginRedirect;

                $returnUrl = UrlHelper::cpUrl($postCpLoginRedirect);
            }

            if ($returnUrl) {
                $postLoginRedirect = Craft::$app->getConfig()
                    ->getGeneral()
                    ->postLoginRedirect;

                $returnUrl = UrlHelper::siteUrl($postLoginRedirect);
            }
        }

        // Check if we should respond with ajax
        if ($requestService->getIsAjax()) {
            return $this->asJson([
                'success' => true,
                'legacyLoginType' => $loginType,
                'returnUrl' => $returnUrl,
            ]);
        }

        // Check if we should set the notice
        if ($setNotice) {
            // Set the notice
            $sessionService->setNotice(Craft::t('legacy-login', 'Logged in.'));
        }

        // Store login information for use by the next request
        $sessionService->setFlash('legacyLoginSuccess', true);
        $sessionService->setFlash('legacyLoginType', $loginType);

        // Redirect to the correct URL
        return $this->redirect($returnUrl);
    }
}
