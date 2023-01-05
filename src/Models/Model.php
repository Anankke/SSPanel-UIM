<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
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

    /**
     * 获取表数据
     *
     * @return array
     * [
     *  'datas' => \Illuminate\Database\Eloquent\Collection,
     *  'count' => int
     * ]
     */
    public static function getTableDataFromAdmin(\Slim\Http\Request $request, ?callable $callback = null, ?callable $precondition = null): array
    {
        //得到排序的方式
        $order = $request->getParam('order')[0]['dir'];
        //得到排序字段的下标
        $order_column = $request->getParam('order')[0]['column'];
        //根据排序字段的下标得到排序字段
        $order_field = $request->getParam('columns')[$order_column]['data'];
        if ($callback !== null) {
            call_user_func_array($callback, [&$order_field]);
        }
        $limit_start = $request->getParam('start');
        $limit_length = $request->getParam('length');
        $search = $request->getParam('search')['value'];

        $query = self::query();
        if ($precondition !== null) {
            call_user_func($precondition, $query);
        }
        if ($search) {
            $query->where(
                static function ($query) use ($search): void {
                    $query->where('id', 'LIKE binary', "%${search}%");
                    $attributes = Capsule::schema()->getColumnListing(self::getTableName());
                    foreach ($attributes as $s) {
                        if ($s !== 'id') {
                            $query->orwhere($s, 'LIKE binary', "%${search}%");
                        }
                    }
                }
            );
        }
        return [
            'count' => (clone $query)->count(),
            'datas' => $query->orderByRaw($order_field . ' ' . $order)->skip($limit_start)->limit($limit_length)->get(),
        ];
    }
}
