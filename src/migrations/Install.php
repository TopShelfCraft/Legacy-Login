<?php

namespace topshelfcraft\legacylogin\migrations;

use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() : bool
    {
        return $this->iterateAndRun('safeUp');
    }

    /**
     * @inheritdoc
     */
    public function safeDown() : bool
    {
        return $this->iterateAndRun('safeDown');
    }

    /**
     * @param $method
     * @return bool
     */
    private function iterateAndRun($method) : bool
    {
        foreach (new \DirectoryIterator(__DIR__) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $fileName = $fileInfo->getBasename('.php');

            if ($fileName === 'Install') {
                continue;
            }

            $class = '\\topshelfcraft\\legacylogin\\migrations\\';
            $class .= $fileInfo->getBasename('.php');

            if (! (new $class())->{$method}()) {
                return false;
            }
        }

        return true;
    }
}
