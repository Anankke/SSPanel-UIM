<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Auth as AuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;

final class Guest implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = AuthService::getUser();

        if ($user->isLogin) {
            $response = AppFactory::determineResponseFactory()->createResponse(302);
            return $response->withHeader('Location', '/user');
        }

        return $handler->handle($request);
    }
}
