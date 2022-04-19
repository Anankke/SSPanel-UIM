<?php

declare(strict_types=1);

use App\Services\View;
use Slim\Container;

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
    ],
];
$container = new Container($configuration);

$container['notFoundHandler'] = static function ($c) {
    return static function ($request, $response) {
        $view = View::getSmarty();
        return $response->withStatus(404)->write($view->fetch('404.tpl'));
    };
};

$container['notAllowedHandler'] = static function ($c) {
    return static function ($request, $response, $methods) {
        $view = View::getSmarty();
        return $response->withStatus(405)->write($view->fetch('405.tpl'));
    };
};

if ($_ENV['debug'] === false) {
    $container['errorHandler'] = static function ($c) {
        return static function ($request, $response, $exception) {
            $view = View::getSmarty();
            $exceptionId = isset($_ENV['sentry_dsn']) ? null : Sentry\captureException($exception);
            return $response->withStatus(500)
                ->write($view->assign('exceptionId', $exceptionId)->fetch('500.tpl'));
        };
    };
    $container['phpErrorHandler'] = static function ($c) {
        return static function ($request, $response, $exception) {
            $view = View::getSmarty();
            $exceptionId = isset($_ENV['sentry_dsn']) ? null : Sentry\captureException($exception);
            return $response->withStatus(500)
                ->write($view->assign('exceptionId', $exceptionId)->fetch('500.tpl'));
        };
    };
}

return $container;
