<?php

namespace topshelfcraft\legacylogin\services\login;

use topshelfcraft\legacylogin\models\LoginModel;

/**
 * Class EE2LoginService
 */
class EE2LoginService extends BaseLoginService
{
    const HASH_ALGORITHMS = [
        32 => 'md5',
        40 => 'sha1',
        64 => 'sha256',
        128 => 'sha512',
    ];

    /**
     * @inheritdoc
     * @throws \Exception
     * @throws \Throwable
     */
    public function logIn(LoginModel $model): bool
    {
        // Make sure a user table is set
        if (! $this->config->userTable) {
            return false;
        }

        // Get matched user
        $matchedUser = $this->matchedUserService->getMatchedUserByUsernameOrEmail(
            $model->username,
            $this->config::TYPE
        );

        // Check if we're over the max legacy log in threshold
        if ($this->overMaxLegacyLoginThreshold($matchedUser)) {
            return false;
        }

        // Get user row, if no user row, login was unsuccessful
        if (! $userRow = $this->getUserRowFromUsernameOrEmail(
            $model->username
        )) {
            return false;
        }

        // Check if the user authenticates
        if (! $this->authenticate($userRow, $model->password)) {
            return false;
        }

        // Add username and email from legacy
        $model->username = $userRow['username'];
        $model->email = $userRow['email'];

        // Create a new Craft user
        $craftUser = $this->craftUserService->logUserInFromLegacy(
            $model,
            $this->config->setPasswordFromLegacyPassword,
            $this->config->requirePasswordReset
        );

        // If there's no matched user, create a new one
        if (! $matchedUser) {
            $matchedUser = $this->matchedUserService->makeNewModel();
        }

        // Populate the model
        $matchedUser->userId = $craftUser->id;
        $matchedUser->legacyUserType = $this->config::TYPE;
        $matchedUser->legacyUsername = $userRow['username'];
        $matchedUser->legacyEmail = $userRow['email'];
        $matchedUser->passwordSet = $this->config->setPasswordFromLegacyPassword;
        ++$matchedUser->legacyLoginCount;

        // Save the matched user
        $this->matchedUserService->saveMatchedUser($matchedUser);

        // We're done
        return true;
    }

    /**
     * Get user row
     * @param string $name
     * @return array
     */
    private function getUserRowFromUsernameOrEmail(string $name) : array
    {
        // Get clean instance of query builder
        $queryBuilder = clone $this->queryBuilder;

        // Set params
        $params = [
            ':userNameOrEmail' => $name,
        ];

        // Get the user table
        $userTable = $this->config->userTable;

        // Query for the row
        $userRow = $queryBuilder->from("{{%$userTable}}")
            ->where('`username` = :userNameOrEmail', $params)
            ->orWhere('`email` = :userNameOrEmail', $params)
            ->one();

        return $userRow;
    }

    /**
     * Authenticate
     * @param array $userRow
     * @param string $password
     * @return bool
     */
    private function authenticate(array $userRow, string $password) : bool
    {
        // Make sure user was not banned or pending
        if (empty($userRow['group_id'] || in_array(
            $userRow['group_id'],
            [
                2,
                4
            ],
            false
        ))) {
            return false;
        }

        // Get legacy items
        $legacySalt = $userRow['salt'];
        $legacyPassword = $userRow['password'];

        // Generate an EE2-style hashed pair
        $hashByteSize = strlen($legacyPassword);
        $hashedPair = $this->generateEe2HashedPair(
            $password,
            $legacySalt,
            $hashByteSize
        );

        // If the hash generation was aborted,
        // or if the password isn't a match... YOU SHALL NOT PASS!
        // Otherwise, welcome in, old friend.
        return ! $hashedPair === false &&
            $legacyPassword === $hashedPair['password'];
    }

    /**
     * Generate EE2-style hashed pair
     * @param string $password
     * @param string $salt
     * @param int $hashByteSize
     * @return array|bool
     */
    private function generateEe2HashedPair(
        string $password,
        string $salt,
        int $hashByteSize
    ) {
        // EE2 requires a password, and artificially limits the password length
        // to avoid hash collisions
        if (! $password || strlen($password) > 250) {
            return false;
        }

        // No hash function specified, or unrecognized hash length?
        // Don't bother.
        if ($hashByteSize === false ||
            ! isset(self::HASH_ALGORITHMS[$hashByteSize])
        ) {
            return false;
        }

        // Generate the hash, and send back the pair.
        return [
            'salt' => $salt,
            'password' => hash(
                self::HASH_ALGORITHMS[$hashByteSize],
                $salt . $password
            ),
        ];
    }
}
