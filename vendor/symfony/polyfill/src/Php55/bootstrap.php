<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Php55 as p;

if (PHP_VERSION_ID < 50500) {
    if (!function_exists('boolval')) {
        function boolval($val) { return p\Php55::boolval($val); }
    }
    if (!function_exists('json_last_error_msg')) {
        function json_last_error_msg() { return p\Php55::json_last_error_msg(); }
    }
    if (!function_exists('array_column')) {
        function array_column($array, $columnKey, $indexKey = null) { return p\Php55ArrayColumn::array_column($array, $columnKey, $indexKey); }
    }
    if (!function_exists('hash_pbkdf2')) {
        function hash_pbkdf2($algorithm, $password, $salt, $iterations, $length = 0, $rawOutput = false) { return p\Php55::hash_pbkdf2($algorithm, $password, $salt, $iterations, $length, $rawOutput); }
    }
}
