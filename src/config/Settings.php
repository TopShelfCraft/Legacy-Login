<?php
namespace TopShelfCraft\LegacyLogin\config;

use craft\base\Model;
use TopShelfCraft\LegacyLogin\handlers\BaseHandler;
use TopShelfCraft\LegacyLogin\LegacyLogin;

/**
 * @author Michael Rog <michael@michaelrog.com>
 */
class Settings extends Model
{

    /**
	 * @var array
	 */
    public $handlers = [];

	/**
	 * @var BaseHandler[]
	 */
    private $_instantiatedHandlers;

	/**
	 * @return BaseHandler[]
	 */
    public function getHandlers(): array
	{
		if (!isset($this->_instantiatedHandlers))
		{
			$this->_instantiatedHandlers = LegacyLogin::getInstance()->handlers->getConfiguredHandlers($this->handlers);
		}
		return $this->_instantiatedHandlers;
	}

}
