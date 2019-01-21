<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\Auth as AuthService;
use App\Services\Config;

class Admin
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        //$response->getBody()->write('BEFORE');
        $user = AuthService::getUser();
        if (!$user->isLogin) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/auth/login');
            return $newResponse;
        }

        if (!$user->isAdmin()) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/user');
            return $newResponse;
        }
        
        $response = $next($request, $response);
        return $response;
    }
}
