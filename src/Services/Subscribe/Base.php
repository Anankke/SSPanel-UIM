<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

abstract class Base
{
    abstract public function getContent($user): string;

    /** Isolate invalid VLESS nodes without logging credentials or private keys. */
    protected function vlessClient(object $node, mixed $config): ?array
    {
        if (! is_array($config)) {
            return null;
        }

        try {
            return Vless::client($config, (string) $node->server);
        } catch (\InvalidArgumentException | \RuntimeException $error) {
            error_log('Skipped invalid VLESS subscription node id=' . (int) $node->id);

            return null;
        }
    }
}
