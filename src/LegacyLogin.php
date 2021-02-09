<?php
namespace TopShelfCraft\LegacyLogin;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\console\Application as ConsoleApplication;
use craft\web\Application as WebApplication;
use TopShelfCraft\LegacyLogin\config\Settings;
use TopShelfCraft\LegacyLogin\handlers\Handlers;
use TopShelfCraft\LegacyLogin\login\Login;
use topshelfcraft\ranger\Plugin;

/**
 * @author Michael Rog <michael@michaelrog.com>
 *
 * @property Handlers $handlers
 * @property Login $login
 *
 * @method Settings getSettings()
 */
class LegacyLogin extends BasePlugin
{

	public $hasCpSection = false;
	public $hasCpSettings = false;
	public $schemaVersion = '3.0.0.0';

	public function __construct($id, $parent = null, array $config = [])
	{
		$config['components'] = [
			'handlers' => Handlers::class,
			'login' => Login::class,
		];
		parent::__construct($id, $parent, $config);
	}

	public function init(): void
    {

        parent::init();
		Plugin::watch($this);

		Craft::setAlias('@TopShelfCraft/LegacyLogin', __DIR__);

		if (Craft::$app instanceof ConsoleApplication)
		{
			$this->controllerNamespace = 'TopShelfCraft\\LegacyLogin\\controllers\\console';
		}
		if (Craft::$app instanceof WebApplication)
		{
			$this->controllerNamespace = 'TopShelfCraft\\LegacyLogin\\controllers\\web';
		}

    }

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

}
