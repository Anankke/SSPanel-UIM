<?php

declare(strict_types=1);

namespace App\Models;

final class Setting extends Model
{
    protected $connection = 'default';
    protected $table = 'config';

    public static function obtain($item)
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

    public static function getClass($class)
    {
        $configs = [];
        $all_configs = Setting::where('class', $class)->get();

        foreach ($all_configs as $config) {
            $configs[$config->item] = $config->value;
        }

        return $configs;
    }

    public static function getPublicConfig()
    {
        $configs = [];
        $all_configs = Setting::where('is_public', '1')->get();

        foreach ($all_configs as $config) {
            $configs[$config->item] = $config->value;
        }

        return $configs;
    }
}
