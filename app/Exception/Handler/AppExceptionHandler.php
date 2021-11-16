<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Helper\Log;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
	/**
	 * @var StdoutLoggerInterface
	 */
	protected $logger;

	public function __construct(StdoutLoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function handle(Throwable $throwable, ResponseInterface $response)
	{
		Log::error($throwable->getMessage(), ['trace' => $throwable->getTraceAsString()]);

		$this->stopPropagation();
		$data = json_encode([
			                    'code' => ErrorCode::SERVER_ERROR,
			                    'message' => $throwable->getMessage() . $throwable->getTraceAsString(),
			                    'data' => (object)[],
		                    ], JSON_UNESCAPED_UNICODE);

		$this->stopPropagation();
		return $response->withStatus(200)
		                ->withHeader('Content-Type', 'application/json')
		                ->withBody(new SwooleStream($data));
	}

	public function isValid(Throwable $throwable): bool
	{
		return true;
	}
}
