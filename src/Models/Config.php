<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use function is_array;
use function json_decode;
use function json_encode;

/**
 * @property int    $id         配置ID
 * @property string $item       配置项
 * @property string $value      配置值
 * @property string $class      配置类别
 * @property string $is_public  是否为公共参数
 * @property string $type       配置值类型
 * @property string $default    默认值
 * @property string $mark       备注
 *
 * @mixin Builder
 */
final class Config extends Model
{
    protected $connection = 'default';
    protected $table = 'config';

    public static function obtain($item): bool|int|array|string
    {
        $config = (new Config())->where('item', $item)->first();

        return match ($config->type) {
            'bool' => (bool) $config->value,
            'int' => (int) $config->value,
            'array' => json_decode($config->value),
            default => (string) $config->value,
        };
    }

    public static function getClass($class): array
    {
        $configs = [];
        $all_configs = (new Config())->where('class', $class)->get();

        foreach ($all_configs as $config) {
            $configs[$config->item] = match ($config->type) {
                'bool' => (bool) $config->value,
                'int' => (int) $config->value,
                'array' => json_decode($config->value),
                default => (string) $config->value,
            };
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
            $configs[$config->item] = match ($config->type) {
                'bool' => (bool) $config->value,
                'int' => (int) $config->value,
                'array' => json_decode($config->value),
                default => (string) $config->value,
            };
        }

        return $configs;
    }

    public static function set(string $item, mixed $value): bool
    {
        $value = is_array($value) ? json_encode($value) : $value;

        try {
            (new Config())->where('item', $item)->update(['value' => $value]);
        } catch (QueryException $e) {
            return false;
        }

        return true;
    }
}
