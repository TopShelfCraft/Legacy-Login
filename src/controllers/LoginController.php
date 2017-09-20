<?php

namespace topshelfcraft\legacylogin\controllers;

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
     */
    public function actionLogin()
    {
        var_dump('here');
        die;
    }
}
