<?php
namespace topshelfcraft\legacylogin;

use craft\base\Plugin as BasePlugin;
use topshelfcraft\legacylogin\models\SettingsModel;
use topshelfcraft\legacylogin\services\Handlers;
use topshelfcraft\legacylogin\services\Login;
use topshelfcraft\ranger\Plugin;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 *
 * @property Handlers $handlers
 * @property Login $login
 */
class LegacyLogin extends BasePlugin
{

    /**
	 * @var LegacyLogin $plugin
	 */
    public static $plugin;

	/**
	 * Initializes the plugin.
	 */
    public function init()
    {

        parent::init();
        self::$plugin = $this;
		Plugin::watch($this);

    }

    /**
     * @return SettingsModel
     */
    protected function createSettingsModel() : SettingsModel
    {
        return new SettingsModel();
    }

}
