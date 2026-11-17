<?php

declare(strict_types=1);

namespace App\Services\Gateway\Nano;

use function ctype_digit;
use function in_array;
use function is_array;
use function is_int;
use function is_string;

/**
 * Decides whether a Nano `block_info` reply settles one paylist row, with no key
 * and no network of its own: the gateway reads the block from the operator's
 * node and hands the reply here. It accepts a confirmed send of at least the
 * exact raw amount, to the configured account, whose representative carries this
 * paylist row's order commitment (binding option C), and that is recent enough
 * that the durable claims ledger still remembers it. Pure: no dependency and no
 * network, so it unit-tests on its own.
 */
final class Verifier
{
    public const OK = '';

    /**
     * @param array<string, mixed> $blockInfo the node's `block_info` reply (json_block=true)
     *
     * @return array{ok: bool, error: string, amount: ?string, payer: ?string}
     */
    public static function verify(
        array $blockInfo,
        string $payTo,
        string $priceRaw,
        string $representativeAccount,
        int $maxAgeSeconds,
        int $now,
    ): array {
        $contents = is_array($blockInfo['contents'] ?? null) ? $blockInfo['contents'] : [];
        $payer = is_string($blockInfo['block_account'] ?? null) ? $blockInfo['block_account'] : null;
        $amountRaw = is_string($blockInfo['amount'] ?? null) ? $blockInfo['amount'] : null;
        $rep = is_string($contents['representative'] ?? null) ? $contents['representative'] : null;
        $dest = is_string($contents['link_as_account'] ?? null) ? $contents['link_as_account'] : null;

        $error = match (true) {
            !in_array($blockInfo['confirmed'] ?? null, [true, 'true'], true) => 'The Nano send block is not confirmed yet.',
            ($blockInfo['subtype'] ?? null) !== 'send' || $payer === null => 'The block is not a Nano send.',
            $amountRaw === null || !Amount::isRaw($amountRaw) => 'The block amount is not a raw integer.',
            !Address::matches($dest, $payTo) => 'The send does not pay the configured Nano account.',
            !Amount::atLeast($amountRaw, $priceRaw) => 'The send amount is less than the invoice amount in raw.',
            !Address::matches($rep, $representativeAccount) => 'The send is not bound to this invoice (representative mismatch).',
            !self::isRecent($blockInfo['local_timestamp'] ?? null, $maxAgeSeconds, $now) => 'The send is outside the replay window.',
            default => self::OK,
        };

        if ($error !== self::OK) {
            return ['ok' => false, 'error' => $error, 'amount' => null, 'payer' => null];
        }

        return ['ok' => true, 'error' => self::OK, 'amount' => Amount::normalizeRaw((string) $amountRaw), 'payer' => $payer];
    }

    private static function isRecent(mixed $timestamp, int $maxAgeSeconds, int $now): bool
    {
        if (!is_string($timestamp) && !is_int($timestamp)) {
            return false;
        }
        $timestamp = (string) $timestamp;
        if (!ctype_digit($timestamp) || $timestamp === '0') {
            return false;
        }

        return $now - (int) $timestamp <= $maxAgeSeconds && $now - (int) $timestamp >= -$maxAgeSeconds;
    }
}
