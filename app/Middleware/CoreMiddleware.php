<?php
declare(strict_types=1);

namespace App\Middleware;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Contracts\Arrayable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{

    /**
     * @param ServerRequestInterface $request
     *
     * @return array|mixed|ResponseInterface|string|static
     */
    protected function handleNotFound(ServerRequestInterface $request)
    {
        $data = json_encode([
            'code' => 404,
            'message' => "路由不存在",
        ], JSON_UNESCAPED_UNICODE);

        return $this->response()->withStatus(404)->withHeader('Content-Type', 'application/json')
                                ->withBody(new SwooleStream($data));
    }

    /**
     * @param array                  $methods
     * @param ServerRequestInterface $request
     *
     * @return array|mixed|ResponseInterface|string|static
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request)
    {
        $data = json_encode([
            'code' => 405,
            'message' => "路由不存在",
        ], JSON_UNESCAPED_UNICODE);

        return $this->response()->withStatus(405)->withHeader('Content-Type', 'application/json')
                                ->withBody(new SwooleStream($data));
    }
}