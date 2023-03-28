<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    public $timestamps = false;
    protected $guarded = [];

    /**
     * 获取表名
     */
    public static function getTableName(): string
    {
        $class = static::class;
        return (new $class())->getTable();
    }
}
