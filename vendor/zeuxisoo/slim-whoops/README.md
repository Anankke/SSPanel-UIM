# Slim whoops

PHP whoops error on slim framework

## Status

[![Build Status](https://travis-ci.org/zeuxisoo/php-slim-whoops.png?branch=master)](https://travis-ci.org/zeuxisoo/php-slim-whoops)

## Installation

Install the composer

    curl -sS https://getcomposer.org/installer | php

Edit `composer.json`

| Slim | Whoops    | Middleware |
| ---- | --------- | ---------- |
|   1  |  n/a      | 0.1.*      |
|   2  |  1.*      | 0.3.*      |
|   3  |  <= 1.*   | 0.4.*      |
|   3  |  >= 2.*   | 0.5.*      |

For `Slim framework 3`, The `composer.json` will looks like

	{
		"require": {
	        "zeuxisoo/slim-whoops": "0.5.*"
		}
	}

Now, `install` or `update` the dependencies

	php composer.phar install

## Usage

Just need to add the middleware in your slim application.

**Simple way**

In this case, You **must** ensure this line is first added and on top of other middlewares

	$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);
	$app->add(new \Other\MiddlewareA);
	$app->add(new \Other\MiddlewareB);
	
**Better DI**

In this case, You can place this line anywhere no position required

	$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware($app));

## Options

Opening referenced files with your favorite editor or IDE

	$app = new App([
	    'settings' => [
	    	 // Enable whoops
	        'debug'         => true,
	        
	        // Support click to open editor
	        'whoops.editor' => 'sublime',
	        
	        // Display call stack in orignal slim error when debug is off 
	        'displayErrorDetails' => true,
	    ]
	]);

## Important Note

Version `0.3.0`

- The `whoops` library is installed by default base on the [Whoops Framework Integration Document][1]

Version `0.2.0`

- You must to install the `whoops` library manually.

## Testing

Run the test cases

	php vendor/bin/phpunit
	
	


[1]: https://github.com/filp/whoops/blob/master/docs/Framework%20Integration.md#contributing-an-integration-with-a-framework	"Whoops Framework Integration Document"
