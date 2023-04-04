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
    ],
];

return new Container($configuration);
