<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\ServerRequest;
use Slim\Http\Response;

final class UserApiToken
{
    /**
     * MID /user/api
     *
     * @param ServerRequest $request
     * @param Response $response
     * @param callable $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
    }
}
