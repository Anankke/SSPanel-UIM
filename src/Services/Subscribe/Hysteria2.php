<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Services\Subscribe;
use function http_build_query;
use function json_decode;
use function rawurlencode;
use const PHP_EOL;
use const PHP_QUERY_RFC3986;

final class Hysteria2 extends Base
{
    public function getContent($user): string
    {
        $links = '';

        foreach (Subscribe::getUserNodes($user) as $node) {
            if ((int) $node->sort !== 15) {
                continue;
            }

            $links .= self::buildUri($node, $user) . PHP_EOL;
        }

        return $links;
    }

    public static function config($node): array
    {
        $custom = json_decode($node->custom_config, true) ?: [];
        $hysteria = $custom['hysteria2'] ?? [];
        $finalMask = $hysteria['finalmask'] ?? [];
        $quic = $finalMask['quicParams'] ?? [];
        $udpMasks = $finalMask['udp'] ?? [];
        $nativeHop = [];
        foreach ($udpMasks as $mask) {
            if (($mask['type'] ?? '') === 'udphop') {
                $nativeHop = $mask['settings'] ?? [];
            }
        }
        $hop = self::portHopping($custom);
        $ports = $hop !== null
            ? ($hop['enabled'] ?? ($hop['enable'] ?? false) ? ($hop['ports'] ?? '') : '')
            : ($nativeHop['remotePorts'] ?? ($quic['udpHop']['ports'] ?? ''));
        if (is_array($ports)) {
            $ports = implode(',', $ports);
        }
        $salamander = '';

        foreach ($udpMasks as $mask) {
            if (($mask['type'] ?? '') === 'salamander') {
                $salamander = (string) ($mask['settings']['password'] ?? '');
                break;
            }
        }

        return [
            'server' => $node->server,
            'port' => (int) ($custom['offset_port_user'] ?? ($custom['offset_port_node'] ?? 443)),
            'sni' => (string) ($custom['host'] ?? $node->server),
            'insecure' => filter_var($custom['allow_insecure'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'salamander' => $salamander,
            'congestion' => (string) ($quic['congestion'] ?? ''),
            'up' => $quic['brutalUp'] ?? null,
            'down' => $quic['brutalDown'] ?? null,
            'up_mbps' => self::bandwidthMbps($quic['brutalUp'] ?? null),
            'down_mbps' => self::bandwidthMbps($quic['brutalDown'] ?? null),
            'ports' => (string) $ports,
            'hop_enabled' => $hop !== null ? (bool) ($hop['enabled'] ?? ($hop['enable'] ?? false)) : ($nativeHop !== [] || $ports !== ''),
            'hop_interval' => self::intervalSeconds($hop['interval'] ?? ($nativeHop['interval'] ?? ($quic['udpHop']['interval'] ?? null))),
            'hop_interval_override' => $hop['interval'] ?? null,
        ];
    }

    /** Select one whole deployment object; explicit disable overrides legacy enable. */
    public static function portHopping(array $custom): mixed
    {
        $top = $custom['portHopping'] ?? $custom['hysteria2']['portHopping'] ?? null;
        if ($top !== null) {
            return $top;
        }
        $legacy = $custom['hysteria2']['finalmask']['quicParams']['udpHop'] ?? null;
        if (is_array($legacy) && ! isset($legacy['enabled']) && ! isset($legacy['enable'])) {
            $legacy['enabled'] = ($legacy['ports'] ?? '') !== '';
        }
        return $legacy;
    }

    public static function xrayFinalMask(array $hysteria, array $config): ?array
    {
        $mask = $hysteria['finalmask'] ?? [];
        $legacy = $mask['quicParams']['udpHop'] ?? [];
        unset($mask['quicParams']['udpHop']);
        $udp = [];
        $nativeHop = null;
        foreach ($mask['udp'] ?? [] as $item) {
            if (($item['type'] ?? '') === 'udphop') {
                $nativeHop = $item['settings'] ?? [];
            } else {
                $udp[] = $item;
            }
        }
        $explicit = array_key_exists('portHopping', $hysteria);
        $enabled = $config['hop_enabled'] ?? ($explicit ? ($hysteria['portHopping']['enabled'] ?? false) : ($nativeHop !== null || $config['ports'] !== ''));
        if ($enabled) {
            $settings = $nativeHop ?? ['mode' => 'intervalremote', 'interval' => $legacy['interval'] ?? 30];
            if ($config['hop_interval_override'] ?? null) {
                $settings['interval'] = $config['hop_interval_override'];
            }
            if ($config['ports'] !== '') {
                $settings['remotePorts'] = $config['ports'];
            }
            // The core applies masks in reverse order; udphop must wrap the raw socket first.
            $udp[] = ['type' => 'udphop', 'settings' => $settings];
        }
        if ($udp !== []) {
            $mask['udp'] = $udp;
        } else {
            unset($mask['udp']);
        }
        if (isset($mask['quicParams']) && $mask['quicParams'] === []) {
            unset($mask['quicParams']);
        }
        return $mask ?: null;
    }

    public static function buildUri($node, $user): string
    {
        $config = self::config($node);
        $query = ['sni' => $config['sni']];

        if ($config['insecure']) {
            $query['insecure'] = '1';
        }
        if ($config['salamander'] !== '') {
            $query['obfs'] = 'salamander';
            $query['obfs-password'] = $config['salamander'];
        }
        if ($config['ports'] !== '') {
            $query['mport'] = $config['ports'];
        }

        return 'hysteria2://' . rawurlencode($user->uuid) . '@' . self::formatHost($config['server']) . ':' . $config['port']
            . '/?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986) . '#' . rawurlencode($node->name);
    }

    private static function formatHost(string $host): string
    {
        return str_contains($host, ':') && $host[0] !== '[' ? '[' . $host . ']' : $host;
    }

    private static function bandwidthMbps(mixed $bandwidth): ?float
    {
        if ($bandwidth === null || $bandwidth === '') {
            return null;
        }
        if (is_int($bandwidth) || is_float($bandwidth)) {
            return (float) $bandwidth / 1000000;
        }
        if (preg_match('/^\s*([0-9]+(?:\.[0-9]+)?)\s*([kmgt]?)b(?:ps)?\s*$/i', (string) $bandwidth, $match) !== 1) {
            return null;
        }
        $multiplier = match (strtolower($match[2])) {
            'k' => 1 / 1024,
            'm' => 1,
            'g' => 1024,
            't' => 1024 * 1024,
            default => 1 / (1024 * 1024),
        };

        return (float) $match[1] * $multiplier;
    }

    private static function intervalSeconds(mixed $interval): int|float|null
    {
        if (is_array($interval)) {
            $interval = $interval['from'] ?? $interval['to'] ?? null;
        }

        return is_numeric($interval) ? (float) $interval : null;
    }
}
