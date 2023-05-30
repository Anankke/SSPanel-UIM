<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\Node;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;

final class NodeToken implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $key = $request->getQueryParams()['key'] ?? null;

        if ($key === null) {
            // 未提供 key
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'data' => 'Invalid request.',
            ]);
        }

        if ($key !== $_ENV['muKey']) {
            // key 不存在
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'data' => 'Invalid request.',
            ]);
        }

        if ($_ENV['WebAPI'] === false) {
            // 主站不提供 WebAPI
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'data' => 'Invalid request.',
            ]);
        }

        if ($_ENV['checkNodeIp']) {
            $ip = $request->getServerParam('REMOTE_ADDR');
            if ($ip !== '127.0.0.1' && ! Node::where('node_ip', 'LIKE', "{$ip}%")->exists()) {
                return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                    'ret' => 0,
                    'data' => 'Invalid request IP.',
                ]);
            }
        }

        return $handler->handle($request);
    }
}
