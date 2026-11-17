<?php

declare(strict_types=1);

namespace App\Services\Gateway\Nano;

use InvalidArgumentException;
use function bin2hex;
use function bindec;
use function decbin;
use function hash_equals;
use function hex2bin;
use function ord;
use function preg_match;
use function str_pad;
use function str_repeat;
use function str_split;
use function strpos;
use function strrev;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;

/**
 * Nano account addresses: `nano_` (or the legacy `xrb_`) followed by the
 * 256-bit public key and a 40-bit BLAKE2b checksum, both in Nano's own base32
 * alphabet.
 *
 * The checksum is what catches a mistyped recipient, so it is always verified.
 * Pure: no dependency and no network, so it unit-tests on its own.
 */
final class Address
{
    private const ALPHABET = '13456789abcdefghijkmnopqrstuwxyz';

    public static function isValid(string $address): bool
    {
        return self::publicKey($address) !== null;
    }

    /**
     * The account's public key as uppercase hex, or null when the address is
     * malformed or its checksum does not match.
     */
    public static function publicKey(string $address): ?string
    {
        if (preg_match('/^(?:nano|xrb)_([13][13-9a-km-uw-z]{59})$/', trim($address), $matches) !== 1) {
            return null;
        }

        $bits = '';
        foreach (str_split($matches[1]) as $character) {
            $bits .= str_pad(decbin((int) strpos(self::ALPHABET, $character)), 5, '0', STR_PAD_LEFT);
        }

        // 52 characters carry 260 bits: four zero bits of padding, then the key.
        $key = self::bytes(substr($bits, 4, 256));
        $checksum = self::bytes(substr($bits, 260, 40));

        if (!hash_equals(strrev(Blake2b::hash($key, 5)), $checksum)) {
            return null;
        }

        return strtoupper(bin2hex($key));
    }

    /**
     * Encode a 32-byte public key (uppercase or lowercase hex) as a `nano_`
     * account with its checksum. The inverse of publicKey(); used to turn an
     * order commitment into the representative account a payer sets.
     */
    public static function fromPublicKey(string $publicKeyHex): string
    {
        if (preg_match('/^[0-9a-fA-F]{64}$/', $publicKeyHex) !== 1) {
            throw new InvalidArgumentException('A Nano public key is 32 bytes of hex.');
        }

        $key = hex2bin(strtolower($publicKeyHex));
        $checksum = strrev(Blake2b::hash($key, 5));

        $bits = str_repeat('0', 4);
        foreach (str_split($key) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }
        foreach (str_split($checksum) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }

        $account = '';
        foreach (str_split($bits, 5) as $chunk) {
            $account .= self::ALPHABET[(int) bindec($chunk)];
        }

        return 'nano_' . $account;
    }

    /**
     * Compare two addresses by the key they encode, so the `xrb_` and `nano_`
     * forms of one account match.
     */
    public static function matches(?string $a, ?string $b): bool
    {
        if ($a === null || $b === null) {
            return false;
        }

        $key = self::publicKey($a);

        return $key !== null && $key === self::publicKey($b);
    }

    private static function bytes(string $bits): string
    {
        $bytes = '';
        foreach (str_split($bits, 8) as $byte) {
            $bytes .= chr((int) bindec($byte));
        }

        return $bytes;
    }
}
