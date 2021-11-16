<?php

declare(strict_types=1);

namespace App\Helper;

use Hyperf\Logger\Logger;
use App\Factory\LoggerFactory;

/**
 * @method static Logger get($name)
 * @method static void log($message, array $context = array())
 * @method static void emergency($message, array $context = array())
 * @method static void alert($message, array $context = array())
 * @method static void critical($message, array $context = array())
 * @method static void error($message, array $context = array())
 * @method static void warning($message, array $context = array())
 * @method static void notice($message, array $context = array())
 * @method static void info($message, array $context = array())
 * @method static void debug($message, array $context = array())
 */
class Log
{
	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed|\Psr\Log\LoggerInterface
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 * @throws \TypeError
	 */
	public static function __callStatic($name, $arguments)
	{
		$factory = di(LoggerFactory::class);
		if ($name === 'get') {
			return $factory->get(...$arguments);
		}
		$log = $factory->get('default');

		$log->{$name}(...$arguments);
	}
}
