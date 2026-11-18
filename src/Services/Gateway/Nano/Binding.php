<?php

declare(strict_types=1);

namespace App\Services\Gateway\Nano;

use function hash;

/**
 * Binds a Nano send block to one paylist row with option C of the xno-binding
 * v1 standard (docs/BINDING-STANDARD.md): the order commitment goes in the send
 * block's representative field.
 *
 *   commitment = SHA256("xno-binding/v1/rep|" + tradeNo + "|" + payTo + "|" + priceRaw)
 *
 * encoded as a `nano_` account. A Nano send has no memo, so a bare block hash
 * proves only that an amount reached an account; the commitment makes the block
 * belong to exactly one paylist row at exactly one price, chosen by the payer
 * when they set their representative. Pure: no dependency and no network, so it
 * unit-tests on its own.
 */
final class Binding
{
    private const DOMAIN = 'xno-binding/v1/rep|';

    public static function commitmentHex(string $tradeNo, string $payTo, string $priceRaw): string
    {
        $priceRaw = Amount::normalizeRaw($priceRaw);

        return hash('sha256', self::DOMAIN . $tradeNo . '|' . $payTo . '|' . $priceRaw);
    }

    /**
     * The representative account a payer must set on the send block for this
     * paylist row. The gateway compares it against the block's representative.
     */
    public static function representativeAccount(string $tradeNo, string $payTo, string $priceRaw): string
    {
        return Address::fromPublicKey(self::commitmentHex($tradeNo, $payTo, $priceRaw));
    }
}
