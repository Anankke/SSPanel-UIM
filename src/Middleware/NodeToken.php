<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\Node;
use App\Services\Config;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class NodeToken
{
    /**
     * MID /mod_mu/
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $key = $request->getQueryParam('key');
        if ($key === null) {
            // 未提供 key
            return $response->withJson([
                'ret' => 0,
                'data' => 'Your key is null.',
            ]);
        }

        if (!in_array($key, Config::getMuKey())) {
            // key 不存在
            return $response->withJson([
                'ret' => 0,
                'data' => 'Token is invalid.',
            ]);
        }

        if ($_ENV['WebAPI'] === false) {
            // 主站不提供 WebAPI
            return $response->withJson([
                'ret' => 0,
                'data' => 'WebAPI is disabled.',
            ]);
        }

        if ($_ENV['checkNodeIp'] === true) {
            $ip = $request->getServerParam('REMOTE_ADDR');
            if ($ip !== '127.0.0.1') {
                if (! Node::where('node_ip', 'LIKE', "${ip}%")->exists()) {
                    return $response->withJson([
                        'ret' => 0,
                        'data' => "IP is invalid. Now, your IP address is ${ip}",
                    ]);
                }
            }
        }

        return $next($request, $response);
    }
}
