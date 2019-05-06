<?php
namespace topshelfcraft\legacylogin\handers;

use Craft;
use craft\config\DbConfig;
use craft\helpers\App;
use topshelfcraft\legacylogin\db\Query;
use yii\base\InvalidConfigException;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
abstract class BaseDbTypeAuthHandler extends BaseAuthHandler {

	/**
	 * @var string $dbDriver
	 */
	public $db;

	/**
	 * @var string $userTable
	 */
	public $userTable;

	/**
	 * @return Query
	 * @throws InvalidConfigException
	 */
	protected function getQuery()
	{

		$connection = null;

		if (!empty($db))
		{
			// TODO: Test
			$config = App::dbConfig((new DbConfig($db)));
			$connection = Craft::createObject($config);
		}

		return new Query([
			'db' => $connection
		]);

	}

}
