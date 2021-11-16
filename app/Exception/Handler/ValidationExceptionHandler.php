<?php
declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\BusinessErrorCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
	public function handle(Throwable $throwable, ResponseInterface $response)
	{
		$this->stopPropagation();
		$body = $throwable->validator->errors()->first();

		$data = json_encode([
			                    'code' => BusinessErrorCode::PARAMS_VALIDATE_FAIL,
			                    'message' => $body ?: $throwable->getMessage(),
			                    'data' => (object)[],
		                    ], JSON_UNESCAPED_UNICODE);

		return $response->withStatus(200)
		                ->withHeader('Content-Type', 'application/json')
		                ->withBody(new SwooleStream($data));
	}

	public function isValid(Throwable $throwable): bool
	{
		return $throwable instanceof ValidationException;
	}
}
