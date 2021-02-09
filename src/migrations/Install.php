<?php
namespace TopShelfCraft\LegacyLogin\migrations;

use Craft;
use craft\db\Migration;
use craft\records\User;
use TopShelfCraft\LegacyLogin\login\LoginRecord;

/**
 * @author Michael Rog <michael@michaelrog.com>
 */
class Install extends Migration
{

	public function safeUp(): bool
	{

		if ($this->createTables())
		{
			$this->addForeignKeys();
			Craft::$app->db->schema->refresh();
		}

		return true;
	}

	public function safeDown(): bool
	{

		$this->dropTableIfExists(LoginRecord::tableName());

		return true;

	}

	protected function createTables(): bool
	{

		if (!$this->db->tableExists(LoginRecord::tableName()))
		{

			$this->createTable(LoginRecord::tableName(), [

				'id' => $this->primaryKey(),

				'userId' => $this->integer()->notNull(),
				'handlerName' => $this->string(),
				'handlerType' => $this->string(),
				'legacyUserId' => $this->string(),
				'userCreated' => $this->boolean(),
				'userUpdated' => $this->boolean(),
				'passwordResetRequired' => $this->boolean(),

				'dateCreated' => $this->dateTime()->notNull(),
				'dateUpdated' => $this->dateTime()->notNull(),
				'uid' => $this->uid(),

			]);

			return true;

		}

		return false;

	}

	protected function addForeignKeys(): void
	{

		// Add foreign key to Craft User ID
		$this->addForeignKey(
			null,
			LoginRecord::tableName(),
			['userId'],
			User::tableName(),
			['id'],
			'CASCADE',
			null
		);

	}

}
