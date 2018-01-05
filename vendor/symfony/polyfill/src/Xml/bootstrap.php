<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Xml as p;

if (!function_exists('utf8_encode')) {
    function utf8_encode($s) { return p\Xml::utf8_encode($s); }
    function utf8_decode($s) { return p\Xml::utf8_decode($s); }
}
