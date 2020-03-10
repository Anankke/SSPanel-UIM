<?php

declare(strict_types=1);

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
    ]
];
$container = new Container($configuration);

$container['notFoundHandler'] = static function ($c) {
    return static function ($request, $response) use ($c) {
        return $response->withAddedHeader('Location', '/404');
    };
};

$container['notAllowedHandler'] = static function ($c) {
    return static function ($request, $response, $methods) use ($c) {
        return $response->withAddedHeader('Location', '/405');
    };
};

if ($_ENV['debug'] === false) {
    $container['errorHandler'] = static function ($c) {
        return static function ($request, $response, $exception) use ($c) {
            return $response->withAddedHeader('Location', '/500');
        };
    };
}

return $container;
