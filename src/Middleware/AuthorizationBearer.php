<?php

namespace App\Middleware;

use App\Services\Config;

class AuthorizationBearer {
    protected string $token;

    function __construct(string $token) {
        $this->token = $token;
    }

    /**
     * @param \Slim\Http\Request    $request
     * @param \Slim\Http\Response   $response
     * @param callable              $next
     *
     * @return \Slim\Http\Response
     */
    public function __invoke($request, $response, $next) {
        if (!$request->hasHeader('Authorization')) {
            return $response->withStatus(401)->withJson([
                'ret'  => 0,
                'data' => 'Authorization failed',
            ]);
        }

        $authHeader = $request->getHeaderLine('Authorization');

        // Bearer method token verify
        if (strtoupper(substr($authHeader, 0, 6)) != 'BEARER') {
            return $response->withStatus(401)->withJson([
                'ret'  => 0,
                'data' => 'Authorization failed',
            ]);
        }

        $realToken = substr($authHeader, 7);

        if ($realToken != $this->token) {
            return $response->withStatus(401)->withJson([
                'ret'  => 0,
                'data' => 'Authorization failed',
            ]);
        }

        return $next($request, $response);
    }
}
