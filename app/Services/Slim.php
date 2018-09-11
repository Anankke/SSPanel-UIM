<?php

namespace App\Services;

use Slim\App;
use Slim\Container;
use App\Controllers;
use App\Middleware\Auth;
use App\Middleware\Guest;
use App\Middleware\Admin;
use App\Middleware\Api;
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
        if (defined("DEBUG")) {
            $debug = true;
        }

        // Make a Slim App
        $app = new App([
            'settings' => [
                'debug'         => $debug,
                'whoops.editor' => 'sublime',
                'displayErrorDetails' => $debug
            ]
        ]);
        $app->add(new WhoopsMiddleware);
        $this->app = $app;
    }

    public function run()
    {
        $this->app->run();
    }
}
