<?php
namespace TopShelfCraft\LegacyLogin\handlers;

use Craft;
use craft\base\Element;
use craft\db\Query;
use craft\elements\User;
use Throwable;
use TopShelfCraft\LegacyLogin\LegacyLogin;
use TopShelfCraft\LegacyLogin\login\LoginRecord;
use TopShelfCraft\LegacyLogin\login\LoginRequest;
use yii\db\Exception;

/**
 * @author Michael Rog <michael@michaelrog.com>
 */
abstract class BaseHandler
{

	const TYPE = '';

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var bool
	 */
	public $createUser = true;

	/**
	 * @var bool
	 */
	public $updateUser = true;

	/**
	 * @var bool
	 */
	public $requirePasswordReset = false;

	/**
	 * @var int
	 */
	public $maxLogins = 1;

	abstract public function authenticate(LoginRequest $login): bool;

	abstract public function getMatchedUser(LoginRequest $login): ?User;

	/**
	 * @return mixed
	 */
	abstract protected function getLegacyUserId(LoginRequest $login);

	abstract protected function prepUser(User $user, LoginRequest $login): void;

	final protected function getType(): string
	{
		return static::TYPE ?: self::class;
	}

	final public function handle(LoginRequest $login): bool
	{

		if (!$this->authenticate($login))
		{
			return false;
		}

		$transaction = Craft::$app->db->beginTransaction();

		try
		{

			$loginRecord = new LoginRecord([
				'handlerName' => $this->name,
				'handlerType' => $this->getType(),
				'legacyUserId' => $this->getLegacyUserId($login),
				'passwordResetRequired' => $this->requirePasswordReset,
			]);

			$matchedUser = $this->getMatchedUser($login);

			if (!$matchedUser)
			{

				// No matched User means "end of the line" if we're not allowed to create a new User.
				if (!$this->createUser)
				{
					throw new Exception("Legacy user authenticated, but User creation is disabled for this handler.");
				}

				// Handle the login by creating a new User.
				$loginRecord->userCreated = true;
				$newUser = new User();
				$this->prepUser($newUser, $login);

				$this->_saveEverythingAndLoginUser($newUser, $login, $loginRecord);
				$transaction->commit();
				return true;

			}

			// Make sure we haven't reached our legacy login limit.
			if ($this->_getPreviousLegacyLoginsCount($matchedUser) >= $this->maxLogins)
			{
				throw new Exception("This handler has already processed the max number of legacy logins for this User.");
			}

			// Handle the login using the matched User.
			if ($this->updateUser)
			{
				$loginRecord->userUpdated = true;
				$this->prepUser($matchedUser, $login);
			}

			$this->_saveEverythingAndLoginUser($matchedUser, $login, $loginRecord);
			$transaction->commit();
			return true;

		}
		catch(Throwable $e)
		{
			$transaction->rollBack();
			Craft::error($e->getMessage(), LegacyLogin::getInstance()->handle);
			return false;
		}

	}

	private function _getPreviousLegacyLoginsCount(User $user): int
	{
		return (int) (new Query())
			->from(LoginRecord::tableName())
			->where(['userId' => $user->id])
			->andWhere(['handlerType' => $this->getType()])
			->andWhere(['handlerName' => $this->name])
			->count();
	}

	private function _saveEverythingAndLoginUser(User $user, LoginRequest $login, LoginRecord $record)
	{

		if ($this->requirePasswordReset)
		{
			$user->passwordResetRequired = true;
		}

		$user->setScenario(Element::SCENARIO_ESSENTIALS);

		if (!Craft::$app->elements->saveElement($user))
		{
			throw new Exception("Could not save the User.");
		}

		$record->userId = $user->id;

		if (!$record->save())
		{
			throw new Exception("An error occurred when saving the Login Record.");
		}

		LegacyLogin::getInstance()->login->login($user, $login->rememberMe);

	}

}
