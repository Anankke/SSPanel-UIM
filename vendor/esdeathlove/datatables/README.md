# Datatables library for PHP
[![Latest Stable Version](https://poser.pugx.org/ozdemir/datatables/v/stable)](https://packagist.org/packages/ozdemir/datatables) [![Build Status](https://travis-ci.org/n1crack/datatables.svg?branch=master)](https://travis-ci.org/n1crack/datatables) [![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://github.com/n1crack/datatables/blob/master/LICENCE) [![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/php-datatables/Lobby?utm_source=share-link&utm_medium=link&utm_campaign=share-link) 

PHP Library to handle server-side processing for Datatables, in a fast and simple way. [Live Demo](http://datatables.16mb.com/)

## Features  
1. Easy to use. Generates json using only a few lines of code.
2. Editable columns with a closure function.
3. Supports mysql and sqlite for native php.
4. Works with [laravel](https://github.com/n1crack/datatables-examples/blob/master/other_examples/laravel.php) and [codeigniter3](https://github.com/n1crack/datatables-examples/blob/master/other_examples/codeigniter.php)


## How to install?

Installation via [composer](https://getcomposer.org/) is supported.  

If you haven't started using composer, I highly recommend you to use it.

Put a file named `composer.json` at the root of your project, containing this information: 

    {
        "require": {
           "ozdemir/datatables": "1.*"
        }
    }

And then run: `composer install`

Or just run : `composer require ozdemir/datatables`

Add the autoloader to your project:

```php
    <?php

    require_once 'vendor/autoload.php'
```

You're now ready to begin using the Datatables php library.


## How to use?

A simple ajax example:

```php
    <?php
    require_once 'vendor/autoload.php';

    use Ozdemir\Datatables\Datatables;
    use Ozdemir\Datatables\DB\MySQL;

    $config = [ 'host'     => 'localhost',
                'port'     => '3306',
                'username' => 'homestead',
                'password' => 'secret',
                'database' => 'sakila' ];

    $dt = new Datatables( new MySQL($config) );

    $dt->query("Select film_id, title, description from film");

    echo $dt->generate();
```

#### Methods
This is the list of available public methods.

* query ( $query : string ) `(required)`
* edit ($column:string, Closure:object ) `(optional)`
* generate ( ) `(required)`

#### Example

```php
    <?php
    $dt = new Datatables( new MySQL($config) );

    $dt->query("Select id, name, email, address, plevel from users");

    $dt->edit('id', function($data){
        // return an edit link.
        return "<a href='user.php?id=" . $data['id'] . "'>edit</a>";
    });

    $dt->edit('email', function($data){
        // return mail@mail.com to m***@mail.com
        return preg_replace('/(?<=.).(?=.*@)/u','*', $data['email']);
    });

    $dt->edit('address', function($data){
        // check if user has authorized to see that
        $current_user_plevel = 4;
        if ($current_user_plevel > 2 && $current_user_plevel > $data['plevel']) {
            return $data['address'];
        }

        return 'you are not authorized to view this column';
    });

    echo $dt->generate();
```

## Requirements

DataTables > 1.10  
PHP > 5.3.7  

## License

Copyright (c) 2015 Yusuf ÖZDEMİR, released under [the MIT license](https://github.com/n1crack/Datatables/blob/master/LICENCE)
