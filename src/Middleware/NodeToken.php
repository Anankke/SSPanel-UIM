<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\Node;
use App\Services\RateLimit;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RedisException;
use Slim\Factory\AppFactory;
use voku\helper\AntiXSS;

final class NodeToken implements MiddlewareInterface
{
    /**
     * @throws RedisException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $key = $request->getQueryParams()['key'] ?? null;

        if ($key === null) {
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'msg' => 'Invalid request.',
            ]);
        }

        $antiXss = new AntiXSS();

        if ($_ENV['enable_rate_limit'] &&
            (! RateLimit::checkIPLimit($request->getServerParam('REMOTE_ADDR')) ||
                ! RateLimit::checkWebAPILimit($antiXss->xss_clean($key)))
        ) {
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'msg' => 'Invalid request.',
            ]);
        }

        if (! $_ENV['webAPI'] ||
            $key !== $_ENV['muKey'] ||
            'https://' . $request->getHeaderLine('Host') !== $_ENV['webAPIUrl']
        ) {
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'msg' => 'Invalid request.',
            ]);
        }

        if ($_ENV['checkNodeIp']) {
            $ip = $request->getServerParam('REMOTE_ADDR');

            if ($ip !== '127.0.0.1' && ! (new Node())->where('node_ip', $ip)->exists()) {
                return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                    'ret' => 0,
                    'msg' => 'Invalid request IP.',
                ]);
            }
        }

        return $handler->handle($request);
    }
}
