<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id         配置ID
 * @property string $item       配置项
 * @property string $value      配置值
 * @property string $class      配置类别
 * @property string $is_public  是否为公共参数
 * @property string $default    默认值
 * @property string $mark       备注
 *
 * @mixin Builder
 */
final class Config extends Model
{
    protected $connection = 'default';
    protected $table = 'config';

    public static function obtain($item): bool|int|string
    {
        $config = (new Config())->where('item', $item)->first();

        return match ($config->type) {
            'bool' => (bool) $config->value,
            'int' => (int) $config->value,
            default => (string) $config->value,
        };
    }

    public static function getClass($class): array
    {
        $configs = [];
        $all_configs = (new Config())->where('class', $class)->get();

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

    public static function getItemListByClass($class): array
    {
        $items = [];
        $all_configs = (new Config())->where('class', $class)->get();

        foreach ($all_configs as $config) {
            $items[] = $config->item;
        }

        return $items;
    }

    public static function getPublicConfig(): array
    {
        $configs = [];
        $all_configs = (new Config())->where('is_public', '1')->get();

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

    public static function set($item, $value): bool
    {
        $config = (new Config())->where('item', $item)->first();

        if ($config->tpye === 'array') {
            $config->value = json_encode($value);
        } else {
            $config->value = $value;
        }

        return $config->save();
    }
}
