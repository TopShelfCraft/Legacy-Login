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
        return $this->_runAllMigrations('safeUp');
    }

    /**
     * @inheritdoc
     */
    public function safeDown() : bool
    {
        return $this->_runAllMigrations('safeDown');
    }

    /**
     * @param $method
     * @return bool
     */
    private function _runAllMigrations($method) : bool
    {

    	/*
    	 * Iterate over all the migrations that exist at the time of installation/un-installation.
    	 */

        foreach (new \DirectoryIterator(__DIR__) as $file) {

            if ($file->isDot() || $file->getExtension() !== 'php') {
                continue;
            }

            $fileName = $file->getBasename('.php');

            // Skip the Install file, which we're already in, lest we recurse forever.
            if ($fileName === 'Install') {
                continue;
            }

            $class = '\\topshelfcraft\\legacylogin\\migrations\\' . $fileName;

            if (!(new $class())->{$method}()) {
                return false;
            }

        }

        return true;

    }

}
