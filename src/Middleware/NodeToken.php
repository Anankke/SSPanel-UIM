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
use function explode;
use function parse_url;
use function strtolower;
use const PHP_URL_HOST;
use const PHP_URL_PORT;

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
            (! (new RateLimit())->checkRateLimit('webapi_ip', $request->getServerParam('REMOTE_ADDR')) ||
                ! (new RateLimit())->checkRateLimit('webapi_key', $antiXss->xss_clean($key)))
        ) {
            return AppFactory::determineResponseFactory()->createResponse(401)->withJson([
                'ret' => 0,
                'msg' => 'Invalid request.',
            ]);
        }

        $requestHostHeader = $request->getHeaderLine('Host');
        $requestHostParts = explode(':', $requestHostHeader, 2);
        $requestHost = strtolower($requestHostParts[0]);
        $requestPort = $requestHostParts[1] ?? null;
        $webApiUrlHost = strtolower((string) parse_url((string) $_ENV['webAPIUrl'], PHP_URL_HOST));
        $webApiUrlPort = parse_url((string) $_ENV['webAPIUrl'], PHP_URL_PORT);
        $webApiUrlMatches = $webApiUrlHost !== '' &&
            $requestHost === $webApiUrlHost &&
            ($webApiUrlPort === null || (string) $webApiUrlPort === (string) $requestPort);

        if (! $_ENV['webAPI'] ||
            $key !== $_ENV['muKey'] ||
            ! $webApiUrlMatches
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
