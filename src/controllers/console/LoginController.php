<?php
namespace TopShelfCraft\LegacyLogin\controllers\console;

use Craft;
use craft\console\Controller;
use craft\helpers\Console;
use TopShelfCraft\LegacyLogin\LegacyLogin;
use TopShelfCraft\LegacyLogin\login\LoginRequest;
use yii\console\ExitCode;

/**
 * Legacy Login functions
 *
 * @author Michael Rog <michael@michaelrog.com>
 */
class LoginController extends Controller
{

	public function actionTestLogin($username): int
	{

		$password = $this->prompt('Password:');
		$rememberMe = filter_var($this->prompt('Remember me?'), FILTER_VALIDATE_BOOLEAN);
		Console::stdout(PHP_EOL);

		$login = new LoginRequest($username, $password, $rememberMe);

		Console::stdout("Native user authentication... ");
		$user = Craft::$app->users->getUserByUsernameOrEmail($login->loginName);
		$native = LegacyLogin::getInstance()->login->authenticateNative($login, $user);
		Console::stdout(($native ? '✅' : '🚫') . PHP_EOL);

		$settings = LegacyLogin::getInstance()->getSettings();

		foreach ($settings->getHandlers() as $handler)
		{
			Console::stdout("{$handler->name}... ");
			Console::stdout(($handler->authenticate($login) ? '✅' : '🚫'). PHP_EOL);
		}

		Console::stdout(PHP_EOL);

		return ExitCode::OK;

	}

}
