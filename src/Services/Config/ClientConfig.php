<?php

declare(strict_types=1);

namespace App\Services\Config;

final class ClientConfig
{
    private static ?array $config = null;

    public static function getClients(string $sub, string $name, bool $r2): array
    {
        if (self::$config === null) {
            $file = BASE_PATH . '/config/client_display.json';
            if (! is_readable($file)) {
                throw new \RuntimeException("Client config file not found: {$file}");
            }

            $content = file_get_contents($file);
            if ($content === false) {
                throw new \RuntimeException("Failed to read client config file: {$file}");
            }

            try {
                self::$config = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \RuntimeException('Invalid JSON in client config file: ' . $e->getMessage());
            }
        }

        $result = [];
        foreach (self::$config['clients'] as $client) {
            foreach ($client['platforms'] as $platform => $data) {
                $result[$platform][] = [
                    'name' => $client['name'],
                    'description' => $data['desc'] ?? $client['description'],
                    'format' => $client['format'],
                    'importUrl' => str_replace(
                        ['{sub}', '{name}'],
                        [$sub, rawurlencode($name)],
                        $data['importUrl'] ?? $client['importUrl']
                    ),
                    'downloadUrl' => $data['storeUrl'] ??
                        (isset($data['ext']) ? ($r2 ? '/user' : '') . '/clients/' . ($data['file'] ?? str_replace(' ', '.', $client['name'])) . ".{$data['ext']}" : ''),
                    'isAppStore' => isset($data['storeUrl']),
                ];
            }
        }

        return ['clients' => $result, 'icons' => self::$config['icons']];
    }
}
