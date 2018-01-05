<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

use Slim\App;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

$app = new App([
    'settings' => [
        'debug'               => true,      // On/Off whoops error
        'whoops.editor'       => 'sublime',
        'displayErrorDetails' => true,      // Display call stack in orignal slim error when debug is off
    ]
]);

if ($app->getContainer()->settings['debug'] === false) {
    $container['errorHandler'] = function($c) {
        return function($request, $response, $exception) use ($c) {
            $data = [
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => explode("\n", $exception->getTraceAsString()),
            ];

            return $c->get('response')
                    ->withStatus(500)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
        };
    };
}else{
    $app->add(new WhoopsMiddleware($app));
}

// Throw exception, Named route does not exist for name: hello
$app->get('/', function($request, $response, $args) {
	return $this->router->pathFor('hello');
});

// $app->get('/hello', function($request, $response, $args) {
//     $response->write("Hello Slim");
//     return $response;
// })->setName('hello');

$app->run();
