<?php

declare(strict_types=1);

namespace App\Middleware;

final class AuthorizationBearer
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next): \Slim\Http\Response
    {
        if (! $request->hasHeader('Authorization')) {
            return $response->withStatus(401)->withJson([
                'ret' => 0,
                'data' => 'Authorization failed',
            ]);
        }

        $authHeader = $request->getHeaderLine('Authorization');

        // Bearer method token verify
        if (strtoupper(substr($authHeader, 0, 6)) !== 'BEARER') {
            return $response->withStatus(401)->withJson([
                'ret' => 0,
                'data' => 'Authorization failed',
            ]);
        }

        $realToken = substr($authHeader, 7);

        if ($realToken !== $this->token) {
            return $response->withStatus(401)->withJson([
                'ret' => 0,
                'data' => 'Authorization failed',
            ]);
        }

        return $next($request, $response);
    }
}
