<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

abstract class Base
{
    abstract public function getContent($user): string;

    /** Isolate invalid VLESS nodes without logging credentials or private keys. */
    protected function vlessClient(object $node, array $config, bool $xray = false): ?array
    {
        try {
            $client = Vless::client($config, (string) $node->server);
            if ($xray && $client['security'] === 'tls' && $client['allow_insecure'] && $client['certificate_pin'] === '') {
                throw new \InvalidArgumentException('Xray TLS requires a certificate pin.');
            }
            return $client;
        } catch (\InvalidArgumentException | \RuntimeException $error) {
            error_log('Skipped invalid VLESS subscription node id=' . (int) $node->id);
            return null;
        }
    }
}
