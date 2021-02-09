<?php
namespace TopShelfCraft\LegacyLogin\handlers;

use Craft;
use craft\config\DbConfig;
use craft\db\Connection;
use craft\helpers\App;

/**
 * @author Michael Rog <michael@michaelrog.com>
 */
abstract class DbTypeHandler extends BaseHandler
{

	/**
	 * @var array The db config
	 */
	public $db;

	/**
	 * @var string
	 */
	public $table;

	/**
	 * @var Connection The db connection
	 */
	private $_connection;

	protected function getConnection(): Connection
	{

		if (!isset($this->_connection))
		{

			$configuredConnection = null;

			if (isset($this->db))
			{
				$config = App::dbConfig((new DbConfig($this->db)));
				$configuredConnection = Craft::createObject($config);
			}

			$this->_connection = $configuredConnection ?? Craft::$app->getDb();

		}

		return $this->_connection;

	}

}
