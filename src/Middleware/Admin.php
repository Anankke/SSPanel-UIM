<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Auth as AuthService;

final class Admin
{
    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next): \Slim\Http\Response
    {
        $user = AuthService::getUser();
        if (! $user->isLogin) {
            return $response->withStatus(302)->withHeader('Location', '/auth/login');
        }
        if (! $user->is_admin) {
            return $response->withStatus(302)->withHeader('Location', '/user');
        }
        return $next($request, $response);
    }
}
