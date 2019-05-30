<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\Auth as AuthService;

class Auth
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $user = AuthService::getUser();
        if (!$user->isLogin) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/auth/login');
            return $newResponse;
        }
        $enablePages = array('/user/disable', '/user/backtoadmin', '/user/logout');
        if ($user->enable == 0 && !in_array($_SERVER['REQUEST_URI'], $enablePages)) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/user/disable');
            return $newResponse;
        }
        $response = $next($request, $response);
        return $response;
    }
}
