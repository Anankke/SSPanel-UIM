<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class AdminApi
{
    /**
     * MID /admin/api/
     *
     * @param ServerRequest $request
     * @param Response $response
     * @param callable $next
     */
    public function __invoke($request, $response, $next): ResponseInterface
    {
    }
}
