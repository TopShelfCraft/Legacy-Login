<?php
namespace topshelfcraft\legacylogin\services;

use Throwable;
use craft\base\Component;
use topshelfcraft\legacylogin\handers\BaseAuthHandler;
use topshelfcraft\legacylogin\handers\Craft2AuthHandler;
use yii\base\InvalidArgumentException;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Legacy-Login
 * @since 3.0.0
 */
class Handlers extends Component
{

	/**
	 * @var array HANDLER_CLASSES
	 */
	const HANDLER_CLASSES = [
		'bigcommerce' => '',
		'craft2' => Craft2AuthHandler::class,
		'craft3' => '',
		'ee2' => '',
		'wellspring' => '',
		'wordpress' => '',
	];

	/**
	 * @param null $config
	 *
	 * @return array
	 */
	public function getConfiguredHandlers($config = null): array
	{

		$handlers = [];

		foreach ($config as $name => $options)
		{
			$type = $options['type'] ?? null;
			unset($options['type']);
			$options = ['name' => $name] + $options;
			$handlers[] = $this->getHandler($type, $options);
		}

		return $handlers;

	}

	/**
	 * @param $type
	 * @param $options
	 *
	 * @return BaseAuthHandler
	 * @throws InvalidArgumentException
	 */
	public function getHandler($type, $options): BaseAuthHandler
	{

		if (empty($type))
		{
			throw new InvalidArgumentException('Handler type must be defined.');
		}

		$class = isset(self::HANDLER_CLASSES[strtolower($type)])
			? self::HANDLER_CLASSES[strtolower($type)]
			: $type;

		try
		{
			$handler = new $class($options);
		}
		catch(Throwable $t)
		{
			throw new InvalidArgumentException('Handler class could not be instantiated: ' . $t->getMessage());
		}

		if (!($handler instanceof BaseAuthHandler))
		{
			throw new InvalidArgumentException('Handler class must extend BaseAuthHandler.');
		}

		return $handler;

	}

}
