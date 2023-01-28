<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\Node;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NodeToken implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $key = $request->getQueryParams()['key'] ?? null;
        if ($key === null) {
            // 未提供 key
            return $response->withJson([
                'ret' => 0,
                'data' => 'Invalid request.',
            ]);
        }

        if ($key !== $_ENV['muKey']) {
            // key 不存在
            return $response->withJson([
                'ret' => 0,
                'data' => 'Invalid request.',
            ]);
        }

        if ($_ENV['WebAPI'] === false) {
            // 主站不提供 WebAPI
            return $response->withJson([
                'ret' => 0,
                'data' => 'Invalid request.',
            ]);
        }

        if ($_ENV['checkNodeIp'] === true) {
            $ip = $request->getServerParam('REMOTE_ADDR');
            if ($ip !== '127.0.0.1') {
                if (! Node::where('node_ip', 'LIKE', "${ip}%")->exists()) {
                    return $response->withJson([
                        'ret' => 0,
                        'data' => 'Invalid request IP.',
                    ]);
                }
            }
        }

        return $next($request, $response);
    }
}
