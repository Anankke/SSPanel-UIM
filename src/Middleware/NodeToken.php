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
        $query = $request->getQueryParams();
        $key = $query['key'] ?? $query['muKey'] ?? null;

        if ($key === null) {
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'msg' => 'Invalid request.',
            ]);
        }

        $antiXss = new AntiXSS();

        if ($_ENV['enable_rate_limit'] &&
            (! (new RateLimit())->checkRateLimit('webapi_ip', $request->getServerParam('REMOTE_ADDR')) ||
                ! (new RateLimit())->checkRateLimit('webapi_key', $antiXss->xss_clean($key)))
        ) {
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'msg' => 'Invalid request.',
            ]);
        }

        $configuredUrl = parse_url(rtrim($_ENV['webAPIUrl'], '/'));
        $requestHost = strtolower($request->getUri()->getHost());
        $hostHeader = parse_url('http://' . $request->getHeaderLine('Host'));
        if ($requestHost === '') {
            $requestHost = strtolower((string) ($hostHeader['host'] ?? ''));
        }
        $expectedHost = strtolower((string) ($configuredUrl['host'] ?? ''));
        $expectedPort = $configuredUrl['port'] ?? null;
        $requestPort = $request->getUri()->getPort() ?? ($hostHeader['port'] ?? null);

        if (! $_ENV['webAPI'] ||
            ! hash_equals((string) $_ENV['muKey'], (string) $key) ||
            $expectedHost === '' ||
            $requestHost !== $expectedHost ||
            ($expectedPort !== null && $requestPort !== $expectedPort)
        ) {
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'msg' => 'Invalid request.',
            ]);
        }

        if ($_ENV['checkNodeIp']) {
            $ip = $request->getServerParam('REMOTE_ADDR');

            if ($ip !== '127.0.0.1' && $ip !== '::1' && $ip !== '0:0:0:0:0:0:0:1' &&
                ! (new Node())->where('ipv4', $ip)->orWhere('ipv6', $ip)->exists()
            ) {
                return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                    'ret' => 0,
                    'msg' => 'Invalid request IP.',
                ]);
            }
        }

        return $handler->handle($request);
    }
}
