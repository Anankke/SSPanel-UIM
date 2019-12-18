<?php
declare(strict_types=1);

use Slim\App;

return function (App $app) {
    $app->add(new Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware());
};
