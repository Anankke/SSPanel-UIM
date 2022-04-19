<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Auth as AuthService;

final class Auth
{
    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next): \Slim\Http\Response
    {
        $user = AuthService::getUser();
        if (! $user->isLogin) {
            return $response->withStatus(302)->withHeader('Location', '/auth/login');
        }
        $enablePages = ['/user/disable', '/user/backtoadmin', '/user/logout'];
        if ($user->enable === 0 && ! in_array($_SERVER['REQUEST_URI'], $enablePages)) {
            return $response->withStatus(302)->withHeader('Location', '/user/disable');
        }
        return $next($request, $response);
    }
}
