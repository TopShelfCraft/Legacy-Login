<?php
namespace topshelfcraft\legacylogin\db;

use craft\db\Connection;
use yii\db\Command;

class Query extends \craft\db\Query
{

    /**
	 * @var Connection
	 */
    public $db;

    /**
     * @param Connection $db
	 *
     * @return Command
     */
    public function createCommand($db = null) : Command
    {
        return parent::createCommand($db ?: $this->db);
    }

}
