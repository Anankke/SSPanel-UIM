<?php

declare(strict_types=1);

namespace App\Services\Gateway\Nano;

use function array_filter;
use function array_keys;
use function array_values;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function in_array;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;

/**
 * The one place that talks to the network: a read-only JSON-RPC client for the
 * Nano node URL the operator configures. It holds no key and no seed and only
 * ever reads (block_info, receivable, account_history), so a leaked node URL
 * can move no money. The checking is all done by the pure Verifier; this class
 * only fetches the block the Verifier is handed.
 */
final class NodeClient
{
    public function __construct(
        private readonly string $nodeUrl,
        private readonly int $timeoutSeconds = 8,
    ) {
    }

    /**
     * The node's `block_info` reply for one hash, or null when the node does not
     * know the block or answers with an error.
     *
     * @return array<string, mixed>|null
     */
    public function blockInfo(string $hash): ?array
    {
        $reply = $this->call(['action' => 'block_info', 'json_block' => 'true', 'hash' => $hash]);

        if ($reply === null || isset($reply['error']) || !isset($reply['contents'])) {
            return null;
        }

        return $reply;
    }

    /**
     * Candidate send-block hashes that may pay the configured account: both the
     * sends still receivable by it and the sources of the sends it has already
     * pocketed. The gateway runs each through block_info + Verifier; a hash that
     * does not match this invoice simply fails there.
     *
     * @return list<string>
     */
    public function candidateSendHashes(string $account, int $count = 25): array
    {
        $receivable = $this->call(['action' => 'receivable', 'account' => $account, 'count' => (string) $count]);
        // `raw` makes the node include each block's `link`, which for a
        // receive is the hash of the send it pocketed; without it the entry
        // carries no source hash at all.
        $history = $this->call([
            'action' => 'account_history',
            'account' => $account,
            'count' => (string) $count,
            'raw' => 'true',
        ]);

        $hashes = [];
        foreach ([...self::receivableHashes($receivable), ...self::receivedSourceHashes($history)] as $hash) {
            $hashes[$hash] = true;
        }

        return array_keys($hashes);
    }

    /**
     * The send hashes listed in a `receivable` reply.
     *
     * @param array<string, mixed>|null $reply
     *
     * @return list<string>
     */
    public static function receivableHashes(?array $reply): array
    {
        $blocks = $reply['blocks'] ?? null;
        if (!is_array($blocks)) {
            return [];
        }

        return array_values(array_filter(array_values($blocks), 'is_string'));
    }

    /**
     * The source send hashes of the receive (and open) blocks in a raw
     * `account_history` reply. A raw state block reports `type: state` with
     * the receive in `subtype`; a non-raw entry reports `type: receive`.
     *
     * @param array<string, mixed>|null $reply
     *
     * @return list<string>
     */
    public static function receivedSourceHashes(?array $reply): array
    {
        $history = $reply['history'] ?? null;
        if (!is_array($history)) {
            return [];
        }

        $hashes = [];
        foreach ($history as $entry) {
            if (self::isReceiveEntry($entry) && is_string($entry['link'] ?? null)) {
                $hashes[] = $entry['link'];
            }
        }

        return $hashes;
    }

    private static function isReceiveEntry(mixed $entry): bool
    {
        if (!is_array($entry)) {
            return false;
        }

        $kind = ($entry['type'] ?? null) === 'state' ? ($entry['subtype'] ?? null) : ($entry['type'] ?? null);

        return in_array($kind, ['receive', 'open'], true);
    }

    /**
     * @param array<string, string> $payload
     *
     * @return array<string, mixed>|null
     */
    private function call(array $payload): ?array
    {
        $handle = curl_init();
        curl_setopt_array($handle, [
            CURLOPT_URL => $this->nodeUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
        ]);

        $body = curl_exec($handle);
        curl_close($handle);

        if (!is_string($body) || $body === '') {
            return null;
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : null;
    }
}
