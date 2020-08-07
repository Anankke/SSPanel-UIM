<?php

declare(strict_types=1);

use Slim\App;
use Slim\Exception\{
    MethodNotAllowedException,
    NotFoundException
};
use Slim\Handlers\PhpError;
use Slim\Http\Request;
use Slim\Http\Response;

return static function (App $app) {
    $app->add(function (Request $request, Response $response, $next): Response {
        try {
            $response = $next($request, $response);
        } catch (MethodNotAllowedException | NotFoundException $e) {
            if ($e instanceof MethodNotAllowedException) {
                $code = '405';
            } else {
                $code = '404';
            }
            // permanently redirect
            $response = (new Response(301))->withHeader('Location', '/' . $code);
        } catch (\Throwable $e) {
            // avoid wrong config causes accident debug on
            if ($_ENV['debug'] === true) {
                // show Slim error page with error detail
                $phperror = new PhpError(true);
                $response = $phperror($request, $response, $e);
            } else {
                $response = new Response();
                // avoid redirect loop
                if ($request->getUri() == '/500') {
                    $response = $response->withStatus(500);
                } else {
                    // temporarily redirect
                    $response = (new Response(302))->withHeader('Location', '/500');
                }
            }
            // log format: [`time`] `request uri`: `error message` in `file`:`line`
            // log location: storage/error.log
            file_put_contents(
                BASE_PATH . '/storage/error.log',
                date('[Y-m-d H:i:s]') . ' ' . $request->getUri()->getPath() . ': '
                    . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL,
                FILE_APPEND
            );
        } finally {
            return $response;
        }
    });
};
