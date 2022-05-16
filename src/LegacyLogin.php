<?php
namespace TopShelfCraft\LegacyLogin;

use Craft;
use craft\console\Application as ConsoleApplication;
use craft\web\Application as WebApplication;
use TopShelfCraft\base\Plugin;
use TopShelfCraft\LegacyLogin\config\Settings;
use TopShelfCraft\LegacyLogin\handlers\Handlers;
use TopShelfCraft\LegacyLogin\login\Login;

/**
 * @author Michael Rog <michael@michaelrog.com>
 *
 * @property Handlers $handlers
 * @property Login $login
 *
 * @method Settings getSettings()
 */
class LegacyLogin extends Plugin
{

	public ?string $changelogUrl = "https://raw.githubusercontent.com/TopShelfCraft/Legacy-Login/master/CHANGELOG.md";
	public bool $hasCpSection = false;
	public bool $hasCpSettings = false;
	public string $schemaVersion = "3.0.0.0";

	public function init(): void
    {

    	$this->setComponents([
			'handlers' => Handlers::class,
			'login' => Login::class,
		]);

        parent::init();

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
