<?php

namespace topshelfcraft\legacylogin\services;

use craft\base\Component;

/**
 * Class BaseService
 */
abstract class BaseService extends Component
{
    /**
     * BaseService constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        // Run the parent constructor
        parent::__construct();

        // Do our own stuff
        foreach ($config as $key => $val) {
            $this->{$key} = $val;
        }
    }
}
