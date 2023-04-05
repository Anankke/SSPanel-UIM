<?php

/**
 * SSPanel-Uim Public Entrance File
 *
 * @license MIT(https://github.com/Anankke/SSPanel-Uim/blob/dev/LICENSE)
 *          Addition: You shouldn't remove staff page or entrance of that page.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/.config.php';
require_once __DIR__ . '/../config/appprofile.php';
require_once __DIR__ . '/../app/predefine.php';

use App\Middleware\ErrorHandler;
use App\Services\Boot;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Slim\Factory\AppFactory;
use Slim\Http\Factory\DecoratedResponseFactory;

Boot::setTime();
Boot::bootSentry();
Boot::bootDb();

$guzzle_factory = new HttpFactory();
$response_factory = new DecoratedResponseFactory($guzzle_factory, $guzzle_factory);
$app = AppFactory::create($response_factory);

$app->add(new ErrorHandler());

$routes = require_once __DIR__ . '/../app/routes.php';
$routes($app);

$request = ServerRequest::fromGlobals();
$request = new Slim\Http\ServerRequest($request);
$app->run($request);
