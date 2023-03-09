<?php

declare(strict_types=1);

namespace App\Models;

final class Setting extends Model
{
    protected $connection = 'default';
    protected $table = 'config';

    public static function obtain($item): bool|int|string
    {
        $config = self::where('item', '=', $item)->first();

        if ($config->type === 'bool') {
            return (bool) $config->value;
        }
        if ($config->type === 'int') {
            return (int) $config->value;
        }

        return (string) $config->value;
    }

    public static function getClass($class): array
    {
        $configs = [];
        $all_configs = Setting::where('class', $class)->get();

        foreach ($all_configs as $config) {
            if ($config->type === 'bool') {
                $configs[$config->item] = (bool) $config->value;
            } elseif ($config->type === 'int') {
                $configs[$config->item] = (int) $config->value;
            } else {
                $configs[$config->item] = (string) $config->value;
            }
        }

        return $configs;
    }

    public static function getPublicConfig(): array
    {
        $configs = [];
        $all_configs = Setting::where('is_public', '1')->get();

        foreach ($all_configs as $config) {
            if ($config->type === 'bool') {
                $configs[$config->item] = (bool) $config->value;
            } elseif ($config->type === 'int') {
                $configs[$config->item] = (int) $config->value;
            } else {
                $configs[$config->item] = (string) $config->value;
            }
        }

        return $configs;
    }
}
