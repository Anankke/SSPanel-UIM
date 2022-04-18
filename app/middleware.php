<?php

declare(strict_types=1);

use Slim\App;

return static function (App $app): void {
    if ($_ENV['debug'] === true) {
        $app->add(new Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware());
    }
};
