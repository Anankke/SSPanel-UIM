<?php

declare(strict_types=1);

namespace App\Services\Gateway\Nano;

use InvalidArgumentException;
use function array_merge;
use function count;
use function pack;
use function str_pad;
use function str_split;
use function strlen;
use function substr;
use function unpack;

/**
 * Unkeyed BLAKE2b (RFC 7693) at any digest length from 1 to 64 bytes.
 *
 * ext-sodium only produces 16 to 64 byte digests, and a shorter BLAKE2b digest
 * is not a truncation of a longer one: the length is mixed into the initial
 * state. A Nano address checksum is 5 bytes, so it is implemented here on
 * 32-bit halves, so it never depends on integer overflow behaviour. It uses no
 * dependency and no network, so it unit-tests on its own.
 */
final class Blake2b
{
    private const MASK = 0xFFFFFFFF;

    /** @var list<int> high and low halves of the IV, interleaved */
    private const IV = [
        0x6A09E667, 0xF3BCC908, 0xBB67AE85, 0x84CAA73B, 0x3C6EF372, 0xFE94F82B, 0xA54FF53A, 0x5F1D36F1,
        0x510E527F, 0xADE682D1, 0x9B05688C, 0x2B3E6C1F, 0x1F83D9AB, 0xFB41BD6B, 0x5BE0CD19, 0x137E2179,
    ];

    /** @var list<list<int>> */
    private const SIGMA = [
        [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
        [14, 10, 4, 8, 9, 15, 13, 6, 1, 12, 0, 2, 11, 7, 5, 3],
        [11, 8, 12, 0, 5, 2, 15, 13, 10, 14, 3, 6, 7, 1, 9, 4],
        [7, 9, 3, 1, 13, 12, 11, 14, 2, 6, 5, 10, 4, 0, 15, 8],
        [9, 0, 5, 7, 2, 4, 10, 15, 14, 1, 11, 12, 6, 8, 3, 13],
        [2, 12, 6, 10, 0, 11, 8, 3, 4, 13, 7, 5, 15, 14, 1, 9],
        [12, 5, 1, 15, 14, 13, 4, 10, 0, 7, 6, 3, 9, 2, 8, 11],
        [13, 11, 7, 14, 12, 1, 3, 9, 5, 0, 15, 4, 8, 6, 2, 10],
        [6, 15, 14, 9, 11, 3, 0, 8, 12, 2, 13, 7, 1, 4, 10, 5],
        [10, 2, 8, 4, 7, 6, 1, 5, 15, 11, 9, 14, 3, 12, 13, 0],
    ];

    /** @var array<int, int> */
    private array $v = [];

    /** @var array<int, int> */
    private array $m = [];

    public static function hash(string $data, int $length): string
    {
        if ($length < 1 || $length > 64) {
            throw new InvalidArgumentException('BLAKE2b digest length must be between 1 and 64 bytes.');
        }

        $state = new self();
        $h = self::IV;
        $h[1] ^= 0x01010000 ^ $length;

        $blocks = $data === '' ? [''] : str_split($data, 128);
        $last = count($blocks) - 1;
        $counter = 0;

        foreach ($blocks as $index => $block) {
            $counter += strlen($block);
            $h = $state->compress($h, str_pad($block, 128, "\0"), $counter, $index === $last);
        }

        $digest = '';

        for ($word = 0; $word < 8; ++$word) {
            $digest .= pack('V2', $h[2 * $word + 1], $h[2 * $word]);
        }

        return substr($digest, 0, $length);
    }

    /**
     * @param array<int, int> $h
     *
     * @return array<int, int>
     */
    private function compress(array $h, string $block, int $counter, bool $final): array
    {
        $this->v = array_merge($h, self::IV);
        $this->v[25] ^= $counter & self::MASK;
        $this->v[24] ^= ($counter >> 32) & self::MASK;

        if ($final) {
            $this->v[28] ^= self::MASK;
            $this->v[29] ^= self::MASK;
        }

        /** @var array<int, int> $words */
        $words = unpack('V32', $block);

        for ($word = 0; $word < 16; ++$word) {
            $this->m[2 * $word] = $words[2 * $word + 2];
            $this->m[2 * $word + 1] = $words[2 * $word + 1];
        }

        for ($round = 0; $round < 12; ++$round) {
            $s = self::SIGMA[$round % 10];

            $this->mix(0, 4, 8, 12, $s[0], $s[1]);
            $this->mix(1, 5, 9, 13, $s[2], $s[3]);
            $this->mix(2, 6, 10, 14, $s[4], $s[5]);
            $this->mix(3, 7, 11, 15, $s[6], $s[7]);
            $this->mix(0, 5, 10, 15, $s[8], $s[9]);
            $this->mix(1, 6, 11, 12, $s[10], $s[11]);
            $this->mix(2, 7, 8, 13, $s[12], $s[13]);
            $this->mix(3, 4, 9, 14, $s[14], $s[15]);
        }

        for ($i = 0; $i < 16; ++$i) {
            $h[$i] ^= $this->v[$i] ^ $this->v[$i + 16];
        }

        return $h;
    }

    private function mix(int $a, int $b, int $c, int $d, int $x, int $y): void
    {
        $this->add($a, $b, 2 * $x);
        $this->xorRotate($d, $a, 32);
        $this->add($c, $d);
        $this->xorRotate($b, $c, 24);
        $this->add($a, $b, 2 * $y);
        $this->xorRotate($d, $a, 16);
        $this->add($c, $d);
        $this->xorRotate($b, $c, 63);
    }

    /**
     * v[a] += v[b], plus message word m[message] when given.
     */
    private function add(int $a, int $b, ?int $message = null): void
    {
        $low = $this->v[2 * $a + 1] + $this->v[2 * $b + 1];
        $high = $this->v[2 * $a] + $this->v[2 * $b];

        if ($message !== null) {
            $low += $this->m[$message + 1];
            $high += $this->m[$message];
        }

        $this->v[2 * $a] = ($high + ($low >> 32)) & self::MASK;
        $this->v[2 * $a + 1] = $low & self::MASK;
    }

    /**
     * v[a] = rotr64(v[a] ^ v[b], bits).
     */
    private function xorRotate(int $a, int $b, int $bits): void
    {
        $high = $this->v[2 * $a] ^ $this->v[2 * $b];
        $low = $this->v[2 * $a + 1] ^ $this->v[2 * $b + 1];

        if ($bits === 32) {
            [$high, $low] = [$low, $high];
        } elseif ($bits === 63) {
            [$high, $low] = [(($high << 1) | ($low >> 31)) & self::MASK, (($low << 1) | ($high >> 31)) & self::MASK];
        } else {
            [$high, $low] = [
                (($high >> $bits) | ($low << (32 - $bits))) & self::MASK,
                (($low >> $bits) | ($high << (32 - $bits))) & self::MASK,
            ];
        }

        $this->v[2 * $a] = $high;
        $this->v[2 * $a + 1] = $low;
    }
}
