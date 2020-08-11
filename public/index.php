<?php

/**
 * SSPanel-Uim Public Entrance File
 *
 * @license MIT(https://github.com/Anankke/SSPanel-Uim/blob/dev/LICENSE)
 *          Addition: You shouldn't remove staff page or entrance of that page.
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/.config.php';
require __DIR__ . '/../config/appprofile.php';
require __DIR__ . '/../app/predefine.php';
require __DIR__ . '/../app/envload.php';

// TODO: legacy boot function
use App\Services\Boot;

Boot::setTime();
Boot::bootSentry();
Boot::bootDb();

/** @var Slim\Container $container */
$container = require __DIR__ . '/../app/container.php';
$app = new Slim\App($container);

/** @var closure $middleware */
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

/** @var closure $routes */
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$app->run();
