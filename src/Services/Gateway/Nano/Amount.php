<?php

declare(strict_types=1);

namespace App\Services\Gateway\Nano;

use InvalidArgumentException;

use function array_fill;
use function array_reverse;
use function implode;
use function intdiv;
use function ltrim;
use function preg_match;
use function rtrim;
use function str_pad;
use function str_split;
use function strcmp;
use function strlen;
use function substr;
use function trim;

/**
 * Nano amounts in raw, the only integer unit the protocol settles in:
 * 1 XNO = 10^30 raw. Every amount the gateway checks is derived here from the
 * invoice total and the operator's rate, never a restated constant, and every
 * comparison is integer string math, so no amount is ever rounded through a
 * float. Pure: no dependency and no network, so it unit-tests on its own.
 */
final class Amount
{
    public const RAW_DECIMALS = 30;

    /**
     * Raw for a decimal XNO amount ("0.05" -> "50000000000000000000000000000").
     * More than 30 fractional digits is below one raw and is refused rather
     * than silently truncated.
     */
    public static function xnoToRaw(string $xno): string
    {
        [$whole, $fraction] = self::split($xno);
        if (strlen($fraction) > self::RAW_DECIMALS) {
            throw new InvalidArgumentException('A XNO amount cannot be finer than one raw (10^-30 XNO).');
        }

        $digits = $whole . str_pad($fraction, self::RAW_DECIMALS, '0');
        $digits = ltrim($digits, '0');

        return $digits === '' ? '0' : $digits;
    }

    /**
     * Exact product of two non-negative decimal strings, returned as a decimal
     * string with no float anywhere. Used to turn an invoice total and a rate
     * (XNO per one unit of the invoice currency) into the XNO amount owed.
     */
    public static function multiplyDecimals(string $left, string $right): string
    {
        [$leftWhole, $leftFraction] = self::split($left);
        [$rightWhole, $rightFraction] = self::split($right);

        $product = self::multiplyIntegers($leftWhole . $leftFraction, $rightWhole . $rightFraction);
        $scale = strlen($leftFraction) + strlen($rightFraction);

        if ($scale === 0) {
            return $product;
        }

        $product = str_pad($product, $scale + 1, '0', STR_PAD_LEFT);
        $intPart = ltrim(substr($product, 0, -$scale), '0');
        $fracPart = rtrim(substr($product, -$scale), '0');

        $intPart = $intPart === '' ? '0' : $intPart;

        return $fracPart === '' ? $intPart : $intPart . '.' . $fracPart;
    }

    /**
     * True when the integer raw string $paid is >= $required, both validated.
     */
    public static function atLeast(string $paid, string $required): bool
    {
        return self::compareIntegers(self::normalizeRaw($paid), self::normalizeRaw($required)) >= 0;
    }

    public static function equals(string $left, string $right): bool
    {
        return self::normalizeRaw($left) === self::normalizeRaw($right);
    }

    /**
     * A node reports an amount as an integer raw string of up to 39 digits.
     * Anything else (a float, a negative, over 39 digits) is not a raw amount.
     */
    public static function isRaw(string $value): bool
    {
        return preg_match('/^\d{1,39}$/', trim($value)) === 1;
    }

    public static function normalizeRaw(string $value): string
    {
        if (!self::isRaw($value)) {
            throw new InvalidArgumentException('A raw amount is an integer of 1 to 39 digits.');
        }

        $digits = ltrim(trim($value), '0');

        return $digits === '' ? '0' : $digits;
    }

    /**
     * @return array{0: string, 1: string} integer part, fractional part
     */
    private static function split(string $value): array
    {
        if (preg_match('/^(\d+)(?:\.(\d+))?$/', trim($value), $matches) !== 1) {
            throw new InvalidArgumentException('A decimal amount is a non-negative decimal.');
        }

        return [$matches[1], $matches[2] ?? ''];
    }

    private static function multiplyIntegers(string $left, string $right): string
    {
        $left = ltrim($left, '0');
        $right = ltrim($right, '0');
        if ($left === '' || $right === '') {
            return '0';
        }

        $result = array_fill(0, strlen($left) + strlen($right), 0);
        $leftDigits = array_reverse(str_split($left));
        $rightDigits = array_reverse(str_split($right));

        foreach ($leftDigits as $leftPos => $leftDigit) {
            foreach ($rightDigits as $rightPos => $rightDigit) {
                $cell = $leftPos + $rightPos;
                $result[$cell] += (int) $leftDigit * (int) $rightDigit;
                $result[$cell + 1] += intdiv($result[$cell], 10);
                $result[$cell] %= 10;
            }
        }

        $out = ltrim(implode('', array_reverse($result)), '0');

        return $out === '' ? '0' : $out;
    }

    private static function compareIntegers(string $left, string $right): int
    {
        if (strlen($left) !== strlen($right)) {
            return strlen($left) <=> strlen($right);
        }

        return strcmp($left, $right);
    }
}
