<?php

declare(strict_types=1);

use Slim\Container;
use App\Services\View;

/**
 * Container Builder
 *
 * @return Container
 */

$configuration = [
    'settings' => [
        'debug' => $_ENV['debug'],
        'whoops.editor' => 'sublime',
        'displayErrorDetails' => $_ENV['debug'],
    ]
];
$container = new Container($configuration);

$container['notFoundHandler'] = static function ($c) {
    return static function ($request, $response) use ($c) {
        $view = View::getSmarty();
        return $response->withStatus(404)->write($view->fetch('404.tpl'));
    };
};

$container['notAllowedHandler'] = static function ($c) {
    return static function ($request, $response, $methods) use ($c) {
        $view = View::getSmarty();
        return $response->withStatus(405)->write($view->fetch('405.tpl'));
    };
};

if ($_ENV['debug'] === false) {
    $container['errorHandler'] = function ($c) {
        return function ($request, $response, $exception) use ($c) {
            $view = View::getSmarty();
            $exceptionId = empty($_ENV['sentry_dsn']) ? null : Sentry\captureException($exception);
            return $response->withStatus(500)
                ->write($view->assign('exceptionId', $exceptionId)->fetch('500.tpl'));
        };
    };
    $container['phpErrorHandler'] = function ($c) {
        return function ($request, $response, $exception) use ($c) {
            $view = View::getSmarty();
            $exceptionId = empty($_ENV['sentry_dsn']) ? null : Sentry\captureException($exception);
            return $response->withStatus(500)
                ->write($view->assign('exceptionId', $exceptionId)->fetch('500.tpl'));
        };
    };
}

return $container;
