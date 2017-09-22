<?php

namespace topshelfcraft\legacylogin\migrations;

use craft\db\Migration;

/**
 * m170921_170153_CreateMatchedUserRecordTable migration.
 */
class m170921_170153_CreateMatchedUserRecordTable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() : bool
    {
        if ($this->db->tableExists('{{%legacyLoginMatchedUserRecords}}')) {
            return true;
        }

        $this->createTable('{{%legacyLoginMatchedUserRecords}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'legacyUserType' => $this->string()->notNull(),
            'legacyUsername' => $this->string()->notNull(),
            'legacyEmail' => $this->string()->notNull(),
            'passwordSet' => $this->boolean(),
            'legacyLoginCount' => $this->integer()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey(
            null,
            '{{%legacyLoginMatchedUserRecords}}',
            ['userId'],
            '{{%users}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown() : bool
    {
        $this->dropTableIfExists('{{%legacyLoginMatchedUserRecords}}');

        return true;
    }
}
