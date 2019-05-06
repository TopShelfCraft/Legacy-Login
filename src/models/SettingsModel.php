<?php
namespace topshelfcraft\legacylogin\models;

use craft\base\Model;
use topshelfcraft\legacylogin\LegacyLogin;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
class SettingsModel extends Model
{

    /**
	 * @var array $handlers
	 */
    public $handlers = [];

	/**
	 * @var array $_instantiatedHandlers
	 */
    private $_instantiatedHandlers;

	/**
	 * @param array $config
	 *
	 * @return array
	 */
    public function getHandlers($config = null)
	{

		if ($config === null && isset($this->_instantiatedHandlers))
		{
			return $this->_instantiatedHandlers;
		}

		$_config = is_null($config) ? $this->handlers : $config;

		$handlers = LegacyLogin::$plugin->handlers->getConfiguredHandlers($_config);

		if ($config === null)
		{
			$this->_instantiatedHandlers = $handlers;
		}

		return $handlers;

	}

}
