<?php

declare(strict_types=1);

namespace App\Services\Gateway\Nano;

use function array_keys;
use function array_values;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
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
        $hashes = [];

        $receivable = $this->call(['action' => 'receivable', 'account' => $account, 'count' => (string) $count]);
        if (is_array($receivable) && is_array($receivable['blocks'] ?? null)) {
            foreach (array_values($receivable['blocks']) as $hash) {
                if (is_string($hash)) {
                    $hashes[$hash] = true;
                }
            }
        }

        $history = $this->call([
            'action' => 'account_history',
            'account' => $account,
            'count' => (string) $count,
        ]);
        if (is_array($history) && is_array($history['history'] ?? null)) {
            foreach ($history['history'] as $entry) {
                if (is_array($entry) && ($entry['type'] ?? null) === 'receive' && is_string($entry['link'] ?? null)) {
                    $hashes[$entry['link']] = true;
                }
            }
        }

        return array_keys($hashes);
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
