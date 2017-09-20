<?php

namespace topshelfcraft\legacylogin;

use Craft;
use craft\console\Application as ConsoleApplication;
use craft\base\Plugin;

/**
 * Class LegacyLogin
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

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'topshelfcraft\legacylogin\console\controllers';
        }
    }
}
