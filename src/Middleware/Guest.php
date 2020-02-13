<?php

namespace App\Middleware;

use App\Services\Auth as AuthService;

class Guest
{
    /**
     * @param \Slim\Http\Request    $request
     * @param \Slim\Http\Response   $response
     * @param callable              $next
     *
     * @return \Slim\Http\Response
     */
    public function __invoke($request, $response, $next)
    {
        $user = AuthService::getUser();
        if ($user->isLogin) {
            return $response->withStatus(302)->withHeader('Location', '/user');
        }
        return $next($request, $response);
    }
}
