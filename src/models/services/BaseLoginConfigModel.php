<?php

namespace topshelfcraft\legacylogin\models\services;

use Craft;
use craft\db\Connection;
use topshelfcraft\legacylogin\db\Query;
use craft\base\Model;
use topshelfcraft\legacylogin\services\login\BaseLoginService;

/**
 * Class BaseLoginConfigModel
 * @property BaseLoginService $loginService
 */
abstract class BaseLoginConfigModel extends Model
{
    /** @var string TYPE */
    const TYPE = '';

    /** @var bool $configured */
    public $configured = false;

    /** @var int $maxLegacyLogIns */
    public $maxLegacyLogIns;

    /** @var string $dbServer */
    public $dbServer;

    /** @var string $dbPort */
    public $dbPort;

    /** @var string $dbUser */
    public $dbUser;

    /** @var string $dbUser */
    public $dbPassword;

    /** @var string $dbDatabase */
    public $dbDatabase;

    /** @var string $dbTablePrefix */
    public $dbTablePrefix;

    /** @var string $userTable */
    public $userTable;

    /** @var bool $setPasswordFromLegacyPassword */
    public $setPasswordFromLegacyPassword = true;

    /** @var bool $requirePasswordReset */
    public $requirePasswordReset = false;

    /**
     * Get login service
     * @return BaseLoginService
     */
    abstract public function getLoginService() : BaseLoginService;

    /**
     * Get query builder
     * @return Query
     */
    protected function getQueryBuilder() : Query
    {
        // Get connection
        $connection = clone Craft::$app->getDb();
        $connection->username = $this->dbUser ?: $connection->username;
        $connection->password = $this->dbPassword ?: $connection->password;
        $connection->tablePrefix = $this->dbTablePrefix ?: $connection->tablePrefix;

        $dsn = explode(';', $connection->dsn);

        foreach ($dsn as &$val) {
            $array = explode('=', $val);

            if ($array[0] === 'mysql:host') {
                $array[1] = $this->dbServer ?: $array[1];
            } elseif ($array[0] === 'dbname') {
                $array[1] = $this->dbDatabase ?: $array[1];
            } elseif ($array[0] === 'port') {
                $array[1] = $this->dbPort ?: $array[1];
            }

            $val = implode('=', $array);
        }

        unset($val);

        $connection->dsn = implode(';', $dsn);

        // Return the query builder with our connection
        return new Query([
            'db' => $connection
        ]);
    }
}
