<?php

declare(strict_types=1);

namespace App\Services\Gateway\Nano;

use PDO;
use PDOException;

use function preg_match;
use function strtoupper;
use function time;
use function trim;

/**
 * The durable, restart-proof half of replay refusal. The binding already ties a
 * send block to one paylist row through its representative; this makes sure a
 * block that already settled a row can never settle again, even across a
 * restart or a crash between the node read and the credit.
 *
 * It writes the 64-hex block hash into `nano_payment_claims`, whose `block_hash`
 * column is UNIQUE, before the gateway hands the trade number to the atomic
 * Base::postPayment(). The claim lives in the same database as the paylist row,
 * so it survives a restart; an in-memory set would not. claim() returns false
 * when the hash is already there, which the gateway reads as a replay and
 * refuses. The only dependency is a PDO handle, so it tests against an on-disk
 * SQLite database (reopened to prove the claim survives the process).
 */
final class ReplayGuard
{
    public function __construct(private readonly PDO $pdo, private readonly string $table = 'nano_payment_claims')
    {
    }

    /**
     * Record the first settlement of this block hash for this trade number.
     * Returns true when the hash is newly claimed, false when it was already
     * claimed (a replay). A hash that is not 64 hex characters is refused as
     * malformed so a bad value never becomes a claim.
     */
    public function claim(string $blockHash, string $tradeNo): bool
    {
        $blockHash = strtoupper(trim($blockHash));
        if (preg_match('/^[0-9A-F]{64}$/', $blockHash) !== 1) {
            return false;
        }

        try {
            $insert = $this->pdo->prepare(
                "INSERT INTO {$this->table} (block_hash, tradeno, claimed_at) VALUES (:hash, :trade, :at)"
            );

            // Fail closed: a handle in silent or warning error mode reports a
            // duplicate (or any other failed insert) as false instead of
            // throwing, and that must never read as a fresh claim.
            return $insert !== false
                && $insert->execute([':hash' => $blockHash, ':trade' => $tradeNo, ':at' => time()]) === true;
        } catch (PDOException $exception) {
            // A unique-constraint violation means the hash is already claimed.
            if ($this->exists($blockHash)) {
                return false;
            }

            throw $exception;
        }
    }

    private function exists(string $blockHash): bool
    {
        $select = $this->pdo->prepare("SELECT 1 FROM {$this->table} WHERE block_hash = :hash LIMIT 1");
        $select->execute([':hash' => $blockHash]);

        return $select->fetchColumn() !== false;
    }
}
