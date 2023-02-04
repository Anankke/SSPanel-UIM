<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\CallableResolver;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

final class ErrorHandler implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (HttpNotFoundException | HttpMethodNotAllowedException $e) {
            // 404 or 405 throwed by router
            $smarty = View::getSmarty();
            $code = $e->getCode();
            $response->getBody()->write($smarty->fetch("{$code}.tpl"));
            $response = $response->withStatus($code);
        } catch (Throwable $e) {
            $response_factory = AppFactory::determineResponseFactory();
            if ($_ENV['debug'] ?? false === true) {
                $callable_resolver = new CallableResolver(null);
                $error_handler = new SlimErrorHandler($callable_resolver, $response_factory);
                $response = $error_handler($request, $e, true, true, false);
            } else {
                $response = $response_factory->createResponse(500);
                $smarty = View::getSmarty();
                $response->getBody()->write($smarty->fetch('500.tpl'));
            }
        }
        return $response;
    }
}
