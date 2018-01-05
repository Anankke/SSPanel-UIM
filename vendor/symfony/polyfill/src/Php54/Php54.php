<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Php54;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
final class Php54
{
    public static function hex2bin($data)
    {
        $len = strlen($data);

        if (null === $len) {
            return;
        }
        if ($len % 2) {
            trigger_error('hex2bin(): Hexadecimal input string must have an even length', E_USER_WARNING);

            return false;
        }

        return pack('H*', $data);
    }
}
