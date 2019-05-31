<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\Auth as AuthService;

class Guest
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $user = AuthService::getUser();
        if ($user->isLogin) {
            return $response->withStatus(302)->withHeader('Location', '/user');
        }
        $response = $next($request, $response);
        return $response;
    }
}
