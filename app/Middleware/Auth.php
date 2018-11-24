<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\Auth as AuthService;
use App\Services\Config;

use App\Services\Jwt;

class Auth
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $user = AuthService::getUser();
        if (!$user->isLogin) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/auth/login');
            return $newResponse;
        }
        if ($user->enable == 0 && $_SERVER["REQUEST_URI"] != "/user/disable") {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/user/disable');
            return $newResponse;
        }
        $response = $next($request, $response);
        return $response;
    }
}
