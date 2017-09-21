<?php

namespace topshelfcraft\legacylogin;

use Craft;
use craft\console\Application as ConsoleApplication;
use craft\base\Plugin;
use topshelfcraft\legacylogin\controllers\LoginController;
use topshelfcraft\legacylogin\models\SettingsModel;
use topshelfcraft\legacylogin\services\LoginService;
use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

/**
 * Class LegacyLogin
 * @property LoginService $loginService
 */
class LegacyLogin extends Plugin
{
    /** @var LegacyLogin $plugin */
    public static $plugin;

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Make sure parent init functionality runs
        parent::init();

        // Save an instance of this plugin for easy reference throughout app
        self::$plugin = $this;

        // Set a variable for whether this is a console request or not
        $isConsole = Craft::$app instanceof ConsoleApplication;

        // Add in our console commands
        if ($isConsole) {
            $this->controllerNamespace = 'topshelfcraft\legacylogin\console\controllers';
        }

        // If not console request we have to map controllers for action requests
        if (! $isConsole) {
            $this->controllerMap = [
                'login' => LoginController::class,
            ];
        }

        // Register actions
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['legacy-login/login/login'] = 'legacy-login/login/login';
            }
        );
    }

    /**
     * Create the settings model
     * @return SettingsModel
     */
    protected function createSettingsModel() : SettingsModel
    {
        // Return the settings model
        return new SettingsModel();
    }

    /**
     * Get login service
     * @return LoginService
     */
    public function getLoginService() : LoginService
    {
        return new LoginService([
            'settings' => $this->getSettings()
        ]);
    }
}
