<?php

namespace topshelfcraft\legacylogin\services;

use craft\db\Query;
use topshelfcraft\legacylogin\models\MatchedUserModel;

/**
 * Class MatchedUserService
 */
class MatchedUserService extends BaseService
{
    /** @var Query $queryBuilder */
    protected $queryBuilder;

    /** @var MatchedUserModel $matchedUserModel */
    protected $matchedUserModel;

    /**
     * Get matched user by username or email
     * @param string $userNameOrEmail
     * @param string $type
     * @return MatchedUserModel|null
     */
    public function getMatchedUserByUsernameOrEmail(
        string $userNameOrEmail,
        string $type
    ) {
        // Get a clean instance of the query builder
        $queryBuilder = clone $this->queryBuilder;

        // Set params
        $params =  [
            ':userNameOrEmail' => $userNameOrEmail,
            ':type' => $type,
        ];

        // Query the database
        $matchedUserQuery = $queryBuilder
            ->from('{{%legacyLoginMatchedUserRecords}}')
            ->where('`legacyUsername` = :userNameOrEmail', $params)
            ->orWhere('`legacyEmail` = :userNameOrEmail', $params)
            ->andWhere('`legacyUserType` = :type', $params)
            ->one();

        // Return null if user not found
        if (! $matchedUserQuery) {
            return null;
        }

        // Return the model
        return $this->createModelFromDbArrayValues($matchedUserQuery);
    }

    /**
     * Create model from db array values
     * @param array $dbArrayValues
     * @return MatchedUserModel
     */
    private function createModelFromDbArrayValues($dbArrayValues)
    {
        // Get a new matched user model
        $model = clone $this->matchedUserModel;

        // Iterate over items and cast values
        foreach ($dbArrayValues as $key => $val) {
            // Cast integers
            if (in_array($key, [
                'id',
                'userId',
                'legacyLoginCount',
            ], true)) {
                $val = (int) $val;
            } elseif (in_array($key, [
                'passwordSet'
            ], true)) { // Cast booleans
                $val = $val === '1' || $val === 1;
            }

            $model->{$key} = $val;
        }

        // Return the model
        return $model;
    }
}
