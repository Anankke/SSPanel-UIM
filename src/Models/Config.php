<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use function is_array;
use function json_decode;
use function json_encode;

/**
 * @property int    $id
 * @property string $item
 * @property string $value
 * @property string $class
 * @property string $is_public
 * @property string $type
 * @property string $default
 * @property string $mark
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

        if ($config === null) {
            return '';
        }

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
