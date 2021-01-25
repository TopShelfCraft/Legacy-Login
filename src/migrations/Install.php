<?php
namespace topshelfcraft\legacylogin\migrations;

use craft\db\Migration;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
class Install extends Migration
{

	/**
	 * @inheritdoc
	 */
	public function safeUp() : bool
	{

		if ($this->createTables())
		{
			$this->addForeignKeys();
			Craft::$app->db->schema->refresh();
		}

		return true;

	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() : bool
	{

		$this->dropTableIfExists(MatchedUser::tableName());

		return true;

	}

	/*
	 * Protected methods
	 */

	/**
	 * @return bool
	 */
	protected function createTables()
	{

		// Matched Users table

		if (!$this->db->tableExists(MatchedUser::tableName())) {

			$this->createTable(MatchedUser::tableName(), [

				'id' => $this->primaryKey(),

				'userId' => $this->integer()->notNull(),

				'handlerName' => $this->string(),
				'handlerType' => $this->string(),

				'legacyLoginName' => $this->string(),
				'legacyUserId' => $this->string(),

				'userCreated' => $this->boolean(),
				'passwordSet' => $this->boolean(),
				'passwordResetRequired' => $this->boolean(),

				'dateCreated' => $this->dateTime()->notNull(),
				'dateUpdated' => $this->dateTime()->notNull(),
				'uid' => $this->uid(),

			]);

			return true;

		}

		return false;

	}

	/**
	 * @return void
	 */
	protected function addForeignKeys()
	{

		// Link Matched User userId to Craft User id

		$this->addForeignKey(
			null,
			MatchedUser::tableName(),
			['userId'],
			User::tableName(),
			['id'],
			'CASCADE',
			null
		);

	}

}
