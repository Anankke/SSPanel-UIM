<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;

use function substr;

final class AuthorizationBearer implements MiddlewareInterface
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // The OAuth 2.0 Authorization Framework: Bearer Token Usage
        // https://www.rfc-editor.org/rfc/rfc6750

        $authHeader = $request->getHeaderLine('Authorization');

        if (
            substr($authHeader, 0, 7) !== 'Bearer ' ||
            substr($authHeader, 8) !== $this->token
        ) {
            /** @var \Slim\Http\Response */
            $response = AppFactory::determineResponseFactory()->createResponse(401);
            return $response->withJson([
                'ret' => 0,
                'data' => 'Authorization failed',
            ]);
        }

        return $handler->handle($request);
    }
}
