<?php

namespace App\Services;

use Slim\App;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

/***
 * The slim documents: http://www.slimframework.com/docs/objects/router.html
 */
class Slim
{
    public $app;

    public function newApp()
    {
        // config
        $debug = false;
        if (defined('DEBUG')) {
            $debug = true;
        }

        // Make a Slim App
        $app = new App([
            'settings' => [
                'debug' => $debug,
                'whoops.editor' => 'sublime',
                'displayErrorDetails' => $debug
            ]
        ]);
        $app->add(new WhoopsMiddleware());
        $this->app = $app;
    }

    public function run()
    {
        $this->app->run();
    }
}
