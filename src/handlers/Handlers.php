<?php
namespace TopShelfCraft\LegacyLogin\handlers;

use Craft;
use Exception;
use Throwable;

/**
 * @author Michael Rog <michael@michaelrog.com>
 */
final class Handlers
{

	/**
	 * @var array Map of Handler type handles to FQCNs
	 */
	const HANDLER_CLASSES = [
		'bigcommerce' => '',
		'craft2' => Craft2Handler::class,
		'craft3' => Craft3Handler::class,
		'craft4' => Craft4Handler::class,
		'ee2' => EE2Handler::class,
		'wellspring' => '',
		'wordpress' => WordpressHandler::class,
	];

	/**
	 * @return BaseHandler[]
	 */
	public function getConfiguredHandlers(array $config): array
	{

		$handlers = [];

		foreach ($config as $name => $options)
		{
			$type = $options['type'] ?? null;
			unset($options['type']);
			$options = $options + ['name' => $name];
			$handlers[] = $this->getHandler($type, $options);
		}

		return $handlers;

	}

	public function getHandler(string $type, $options): BaseHandler
	{

		if (empty($type))
		{
			throw new Exception('Handler type must be defined.');
		}

		$class = self::HANDLER_CLASSES[strtolower($type)] ?? $type;

		try
		{

			$handler = new $class();
			Craft::configure($handler, $options);

			if (!($handler instanceof BaseHandler))
			{
				throw new Exception('Handler class must extend BaseHandler.');
			}

		}
		catch(Throwable $t)
		{
			throw new Exception('Handler could not be configured: ' . $t->getMessage());
		}

		return $handler;

	}

}
