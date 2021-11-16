<?php
declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\BusinessException;
use App\Helper\Log;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;


class BusinessExceptionHandler extends ExceptionHandler
{
	/**
	 * @param Throwable $throwable
	 * @param ResponseInterface $response
	 * @return ResponseInterface|static
	 */
	public function handle(Throwable $throwable, ResponseInterface $response)
	{
		// 判断传入的异常是否是该处理器希望处理的异常
		if ($throwable instanceof BusinessException) {
			Log::warning($throwable->getMessage(), ['trace' => $throwable->getTraceAsString()]);
			$this->stopPropagation();
			// 传入的异常是我们希望捕获的 BusinessException，我们格式化为 JSON 格式并输出到用户端
			$data = json_encode([
				                    'code' => $throwable->getCode(),
				                    'message' => $throwable->getMessage(),
				                    'data' => (object)[]
			                    ], JSON_UNESCAPED_UNICODE);


			return $response->withStatus(200)
			                ->withHeader('Content-Type', 'application/json')
			                ->withBody(new SwooleStream($data));
		}

		return $response;
	}

	/**
	 * @param Throwable $throwable
	 * @return bool
	 */
	public function isValid(Throwable $throwable): bool
	{
		return true;
	}
}