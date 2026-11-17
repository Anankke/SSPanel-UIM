<?php

declare(strict_types=1);

use App\Services\Gateway\Nano\Address;
use App\Services\Gateway\Nano\Amount;
use App\Services\Gateway\Nano\Binding;
use App\Services\Gateway\Nano\Blake2b;
use App\Services\Gateway\Nano\ReplayGuard;
use App\Services\Gateway\Nano\Verifier;

// The official Nano genesis account and the all-zero "burn" account are both
// published vectors with known public keys, so they pin the address codec.
const NANO_GENESIS = 'nano_3t6k35gi95xu6tergt6p69ck76ogmitsa8mnijtpxm9fkcm736xtoncuohr3';
const NANO_GENESIS_KEY = 'E89208DD038FBB269987689621D52292AE9C35941A7484756ECCED92A65093BA';
const NANO_BURN = 'nano_1111111111111111111111111111111111111111111111111111hifc8npp';

function nano_block(array $overrides = []): array
{
    $payTo = NANO_GENESIS;
    $priceRaw = Amount::xnoToRaw('0.5');
    $rep = Binding::representativeAccount('TRADE-1', $payTo, $priceRaw);

    return array_replace([
        'confirmed' => 'true',
        'subtype' => 'send',
        'block_account' => NANO_BURN,
        'amount' => $priceRaw,
        'local_timestamp' => (string) (1000000000),
        'contents' => ['representative' => $rep, 'link_as_account' => $payTo],
    ], $overrides);
}

test('BLAKE2b-512 matches the RFC 7693 vector for "abc"', function () {
    expect(bin2hex(Blake2b::hash('abc', 64)))->toBe(
        'ba80a53f981c4d0d6a2797b69f12f6e94c212f14685ac4b74b12bb6fdbffa2d1'
        . '7d87c5392aab792dc252d5de4533cc9518d38aa8dbf1925ab92386edd4009923'
    );
});

test('a Nano address decodes to its published public key', function () {
    expect(Address::publicKey(NANO_GENESIS))->toBe(NANO_GENESIS_KEY);
    expect(Address::publicKey(NANO_BURN))->toBe(str_repeat('0', 64));
});

test('encoding a public key reproduces the address', function () {
    expect(Address::fromPublicKey(NANO_GENESIS_KEY))->toBe(NANO_GENESIS);
    expect(Address::fromPublicKey(str_repeat('0', 64)))->toBe(NANO_BURN);
});

test('a one-character checksum error is rejected', function () {
    $bad = substr(NANO_GENESIS, 0, -1) . (NANO_GENESIS[-1] === 'r' ? 's' : 'r');
    expect(Address::isValid($bad))->toBeFalse();
});

test('raw is derived, never a float', function () {
    expect(Amount::xnoToRaw('0.5'))->toBe('500000000000000000000000000000');
    expect(Amount::xnoToRaw('1'))->toBe('1000000000000000000000000000000');
    expect(Amount::multiplyDecimals('10', '0.08'))->toBe('0.8');
    expect(Amount::equals('0500', '500'))->toBeTrue();
    expect(Amount::atLeast('500', '500'))->toBeTrue();
    expect(Amount::atLeast('499', '500'))->toBeFalse();
    expect(Amount::isRaw('12.3'))->toBeFalse();
});

test('an amount finer than one raw is refused, not truncated', function () {
    expect(fn () => Amount::xnoToRaw('0.' . str_repeat('0', 30) . '1'))->toThrow(InvalidArgumentException::class);
});

test('the binding representative is deterministic and unique per trade number', function () {
    $priceRaw = Amount::xnoToRaw('0.5');
    $a = Binding::representativeAccount('TRADE-1', NANO_GENESIS, $priceRaw);
    $b = Binding::representativeAccount('TRADE-1', NANO_GENESIS, $priceRaw);
    $c = Binding::representativeAccount('TRADE-2', NANO_GENESIS, $priceRaw);
    expect($a)->toBe($b);
    expect(Address::isValid($a))->toBeTrue();
    expect($a === $c)->toBeFalse();
});

test('the verifier accepts a correct, bound, confirmed send', function () {
    $rep = Binding::representativeAccount('TRADE-1', NANO_GENESIS, Amount::xnoToRaw('0.5'));
    $result = Verifier::verify(nano_block(), NANO_GENESIS, Amount::xnoToRaw('0.5'), $rep, 86400, 1000000010);
    expect($result['ok'])->toBeTrue();
    expect($result['amount'])->toBe('500000000000000000000000000000');
});

test('the verifier refuses a send to another account', function () {
    $rep = Binding::representativeAccount('TRADE-1', NANO_GENESIS, Amount::xnoToRaw('0.5'));
    $block = nano_block(['contents' => ['representative' => $rep, 'link_as_account' => NANO_BURN]]);
    expect(Verifier::verify($block, NANO_GENESIS, Amount::xnoToRaw('0.5'), $rep, 86400, 1000000010)['ok'])->toBeFalse();
});

test('the verifier refuses an underpaying send', function () {
    $rep = Binding::representativeAccount('TRADE-1', NANO_GENESIS, Amount::xnoToRaw('0.5'));
    $block = nano_block(['amount' => Amount::xnoToRaw('0.4')]);
    expect(Verifier::verify($block, NANO_GENESIS, Amount::xnoToRaw('0.5'), $rep, 86400, 1000000010)['ok'])->toBeFalse();
});

test('the verifier refuses a send bound to another invoice', function () {
    $wrongRep = Binding::representativeAccount('TRADE-2', NANO_GENESIS, Amount::xnoToRaw('0.5'));
    $expectedRep = Binding::representativeAccount('TRADE-1', NANO_GENESIS, Amount::xnoToRaw('0.5'));
    $block = nano_block(['contents' => ['representative' => $wrongRep, 'link_as_account' => NANO_GENESIS]]);
    expect(Verifier::verify($block, NANO_GENESIS, Amount::xnoToRaw('0.5'), $expectedRep, 86400, 1000000010)['ok'])->toBeFalse();
});

test('the verifier refuses an unconfirmed send', function () {
    $rep = Binding::representativeAccount('TRADE-1', NANO_GENESIS, Amount::xnoToRaw('0.5'));
    $block = nano_block(['confirmed' => 'false']);
    expect(Verifier::verify($block, NANO_GENESIS, Amount::xnoToRaw('0.5'), $rep, 86400, 1000000010)['ok'])->toBeFalse();
});

test('the verifier refuses a send outside the replay window', function () {
    $rep = Binding::representativeAccount('TRADE-1', NANO_GENESIS, Amount::xnoToRaw('0.5'));
    expect(Verifier::verify(nano_block(), NANO_GENESIS, Amount::xnoToRaw('0.5'), $rep, 3600, 2000000000)['ok'])->toBeFalse();
});

test('a settled block hash cannot be claimed twice, even after a restart', function () {
    $file = tempnam(sys_get_temp_dir(), 'nanoclaims');
    $dsn = 'sqlite:' . $file;
    $ddl = 'CREATE TABLE nano_payment_claims (id INTEGER PRIMARY KEY AUTOINCREMENT, '
        . 'block_hash TEXT NOT NULL UNIQUE, tradeno TEXT NOT NULL DEFAULT "", claimed_at INTEGER NOT NULL DEFAULT 0)';

    $pdo = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec($ddl);
    $guard = new ReplayGuard($pdo);
    $hash = str_repeat('A', 64);

    expect($guard->claim($hash, 'TRADE-1'))->toBeTrue();
    expect($guard->claim($hash, 'TRADE-1'))->toBeFalse();

    // Reopen the database to prove the claim survives the process (restart-proof).
    $reopened = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $guardAfterRestart = new ReplayGuard($reopened);
    expect($guardAfterRestart->claim($hash, 'TRADE-2'))->toBeFalse();
    expect($guardAfterRestart->claim(str_repeat('B', 64), 'TRADE-1'))->toBeTrue();

    // A malformed hash is refused rather than stored.
    expect($guardAfterRestart->claim('not-a-hash', 'TRADE-1'))->toBeFalse();

    @unlink($file);
});
