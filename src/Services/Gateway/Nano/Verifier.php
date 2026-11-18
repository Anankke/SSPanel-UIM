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
    public const NOT_CONFIRMED = 'The Nano send block is not confirmed yet.';
    public const NOT_A_SEND = 'The block is not a Nano send.';
    public const NOT_RAW = 'The block amount is not a raw integer.';
    public const WRONG_ACCOUNT = 'The send does not pay the configured Nano account.';
    public const UNDERPAID = 'The send amount is less than the invoice amount in raw.';
    public const NOT_BOUND = 'The send is not bound to this invoice (representative mismatch).';
    public const TOO_OLD = 'The send is outside the replay window.';

    /**
     * @param array<string, mixed> $blockInfo the node's `block_info` reply (json_block=true)
     *
     * @return array{ok: bool, error: string, amount: ?string, payer: ?string}
     */
    public static function verify(
        array $blockInfo,
        string $payTo,
        string $priceRaw,
        string $boundRep,
        int $maxAgeSeconds,
        int $now,
    ): array {
        $contents = is_array($blockInfo['contents'] ?? null) ? $blockInfo['contents'] : [];
        $payer = is_string($blockInfo['block_account'] ?? null) ? $blockInfo['block_account'] : null;
        $amountRaw = is_string($blockInfo['amount'] ?? null) ? $blockInfo['amount'] : null;
        $rep = is_string($contents['representative'] ?? null) ? $contents['representative'] : null;
        $dest = is_string($contents['link_as_account'] ?? null) ? $contents['link_as_account'] : null;
        $timestamp = $blockInfo['local_timestamp'] ?? null;

        $error = match (true) {
            !in_array($blockInfo['confirmed'] ?? null, [true, 'true'], true) => self::NOT_CONFIRMED,
            ($blockInfo['subtype'] ?? null) !== 'send' || $payer === null => self::NOT_A_SEND,
            $amountRaw === null || !Amount::isRaw($amountRaw) => self::NOT_RAW,
            !Address::matches($dest, $payTo) => self::WRONG_ACCOUNT,
            !Amount::atLeast($amountRaw, $priceRaw) => self::UNDERPAID,
            !Address::matches($rep, $boundRep) => self::NOT_BOUND,
            !self::isRecent($timestamp, $maxAgeSeconds, $now) => self::TOO_OLD,
            default => self::OK,
        };

        if ($error !== self::OK) {
            return ['ok' => false, 'error' => $error, 'amount' => null, 'payer' => null];
        }

        return [
            'ok' => true,
            'error' => self::OK,
            'amount' => Amount::normalizeRaw((string) $amountRaw),
            'payer' => $payer,
        ];
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
