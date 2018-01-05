<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Php54 as p;

if (PHP_VERSION_ID < 50400) {
    if (!function_exists('trait_exists')) {
        function trait_exists($class, $autoload = true) { return $autoload && class_exists($class, $autoload) && false; }
    }
    if (!function_exists('class_uses')) {
        function class_uses($class, $autoload = true)
        {
            if (is_object($class) || class_exists($class, $autoload) || interface_exists($class, false)) {
                return array();
            }

            return false;
        }
    }
    if (!function_exists('hex2bin')) {
        function hex2bin($data) { return p\Php54::hex2bin($data); }
    }
    if (!function_exists('session_register_shutdown')) {
        function session_register_shutdown() { register_shutdown_function('session_write_close'); }
    }
}
