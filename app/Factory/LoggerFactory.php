<?php

declare(strict_types=1);

namespace App\Factory;

use Hyperf\Logger\Logger;
use Hyperf\Logger\LoggerFactory as HyperfLoggerFactory;
use Hyperf\Utils\Context;
use Psr\Log\LoggerInterface;

/**
 * 日志工厂类.
 *
 * Class LoggerFactory
 */
class LoggerFactory extends HyperfLoggerFactory
{
	/**
	 * @param string $name
	 * @param string $group
	 *
	 * @return LoggerInterface
	 */
	public function get($name = 'hyperf', $group = 'default'): LoggerInterface
	{
		if (isset($this->loggers[$name]) && $this->loggers[$name] instanceof Logger)
		{
			return $this->loggers[$name];
		}

		$logger = $this->make($name, $group);
		$logger->pushProcessor(function ($record) {
			$record['extra']['host'] = gethostname();

			$record['extra']['trace_id'] = Context::get('trace_id');

			return $record;
		});

		return $this->loggers[$name] = $logger;
	}
}
