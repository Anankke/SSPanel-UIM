<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use InvalidArgumentException;
use RuntimeException;

/** Shared interpretation of sort=11 VLESS settings for every client format. */
final class Vless
{
    public static function enabled(array $config): bool
    {
        return in_array($config['enable_vless'] ?? null, [1, '1', true], true);
    }

    public static function client(array $config, string $server): array
    {
        foreach (['network', 'security', 'flow', 'host', 'fingerprint', 'path', 'servicename', 'pinnedPeerCertSha256'] as $field) {
            if (isset($config[$field]) && ! is_string($config[$field])) {
                throw new InvalidArgumentException('VLESS ' . $field . ' must be a string.');
            }
        }
        if (isset($config['enable_reality']) && ! is_bool($config['enable_reality'])) {
            throw new InvalidArgumentException('VLESS enable_reality must be a JSON boolean.');
        }
        $network = strtolower((string) ($config['network'] ?? 'tcp'));
        if ($network === '') {
            $network = 'tcp';
        }
        if ($network === 'raw') {
            $network = 'tcp';
        } elseif ($network === 'splithttp') {
            $network = 'xhttp';
        } elseif ($network === 'websocket') {
            $network = 'ws';
        }
        if (! in_array($network, ['tcp', 'ws', 'grpc', 'httpupgrade', 'xhttp'], true)) {
            throw new InvalidArgumentException('Unsupported VLESS subscription network.');
        }

        $reality = ($config['enable_reality'] ?? false) === true;
        $security = $reality ? 'reality' : (string) ($config['security'] ?? 'none');
        if ($security === 'xtls') {
            $security = 'tls';
        }
        if (! in_array($security, ['none', 'tls', 'reality'], true)) {
            throw new InvalidArgumentException('Unsupported VLESS security.');
        }
        if (! in_array($config['flow'] ?? '', ['', 'xtls-rprx-vision'], true)) {
            throw new InvalidArgumentException('Unsupported VLESS flow.');
        }
        $port = filter_var(
            $config['offset_port_user'] ?? ($config['offset_port_node'] ?? 443),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 65535]]
        );
        if ($port === false) {
            throw new InvalidArgumentException('VLESS client port must be between 1 and 65535.');
        }
        if ($security === 'reality' && ! $reality) {
            throw new InvalidArgumentException('REALITY requires enable_reality=true in custom_config.');
        }
        if (($config['flow'] ?? '') === 'xtls-rprx-vision' && ($network !== 'tcp' || ! in_array($security, ['tls', 'reality'], true))) {
            throw new InvalidArgumentException('Vision requires TCP with TLS or REALITY.');
        }
        $result = [
            'port' => $port,
            'network' => $network,
            'security' => $security,
            'flow' => (string) ($config['flow'] ?? ''),
            'server_name' => (string) ($config['host'] ?? $server),
            'fingerprint' => (string) ($config['fingerprint'] ?? 'chrome'),
            'allow_insecure' => filter_var($config['allow_insecure'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'certificate_pin' => (string) ($config['pinnedPeerCertSha256'] ?? ''),
            'public_key' => '',
            'short_id' => '',
        ];

        if ($reality) {
            $options = $config['reality-opts'] ?? [];
            if (! is_array($options) || ! is_string($options['private_key'] ?? null)) {
                throw new InvalidArgumentException('REALITY requires an X25519 private key.');
            }
            $names = $options['server_names'] ?? [];
            $ids = $options['short_ids'] ?? [];
            if (! is_array($names) || ! isset($names[0]) || ! is_string($names[0]) || $names[0] === '' ||
                ! is_array($ids) || ! array_key_exists(0, $ids) || ! is_string($ids[0])) {
                throw new InvalidArgumentException('REALITY requires server_names and short_ids in custom_config.');
            }

            $preferredName = (string) ($config['host'] ?? '');
            $result['server_name'] = in_array($preferredName, $names, true) ? $preferredName : $names[0];
            $result['short_id'] = $ids[0];
            if (preg_match('/^(?:[0-9a-fA-F]{2}){0,8}$/', $result['short_id']) !== 1) {
                throw new InvalidArgumentException('REALITY short ID must be even-length hexadecimal, at most 16 characters.');
            }
            $result['public_key'] = self::publicKey((string) ($options['private_key'] ?? ''));
        }

        return $result;
    }

    public static function uri(object $node, object $user, array $config, ?array $client = null): string
    {
        $client ??= self::client($config, (string) $node->server);
        $query = [
            'encryption' => 'none',
            'security' => $client['security'],
            'type' => $client['network'],
        ];
        if ($client['flow'] !== '') {
            $query['flow'] = $client['flow'];
        }
        if ($client['security'] === 'reality') {
            $query['sni'] = $client['server_name'];
            $query['fp'] = $client['fingerprint'];
            $query['pbk'] = $client['public_key'];
            $query['sid'] = $client['short_id'];
        } elseif ($client['security'] === 'tls') {
            $query['sni'] = $client['server_name'];
            if ($client['allow_insecure']) {
                $query['allowInsecure'] = '1';
            }
        }
        if (in_array($client['network'], ['ws', 'httpupgrade', 'xhttp'], true)) {
            $query['host'] = (string) ($config['host'] ?? '');
            $query['path'] = (string) ($config['path'] ?? '/');
            if ($client['network'] === 'xhttp') {
                $query['mode'] = 'auto';
            }
        } elseif ($client['network'] === 'grpc') {
            $query['serviceName'] = (string) ($config['servicename'] ?? '');
        }

        $host = (string) $node->server;
        if (str_contains($host, ':') && ! str_starts_with($host, '[')) {
            $host = '[' . $host . ']';
        }

        return 'vless://' . rawurlencode((string) $user->uuid) . '@' . $host . ':' . $client['port']
            . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986) . '#' . rawurlencode((string) $node->name);
    }

    private static function publicKey(string $privateKey): string
    {
        if (! function_exists('sodium_crypto_scalarmult_base')) {
            throw new RuntimeException('REALITY subscriptions require the PHP sodium extension.');
        }

        $key = base64_decode(strtr($privateKey, '-_', '+/'), true);
        if ($key === false || strlen($key) !== SODIUM_CRYPTO_SCALARMULT_SCALARBYTES) {
            throw new InvalidArgumentException('REALITY private key must be an X25519 base64url key.');
        }

        return rtrim(strtr(base64_encode(sodium_crypto_scalarmult_base($key)), '+/', '-_'), '=');
    }
}
