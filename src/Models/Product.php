<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id          商品ID
 * @property string $type        类型
 * @property string $name        名称
 * @property float  $price       售价
 * @property string $content     内容
 * @property string $limit       购买限制
 * @property int    $status      销售状态
 * @property int    $create_time 创建时间
 * @property int    $update_time 更新时间
 * @property int    $sale_count  累计销量
 * @property int    $stock       库存
 *
 * @mixin Builder
 */
final class Product extends Model
{
    protected $connection = 'default';
    protected $table = 'product';

    /**
     * 商品状态
     */
    public function status(): string
    {
        return $this->status ? '正常' : '下架';
    }

    /**
     * 商品类型
     */
    public function type(): string
    {
        return match ($this->type) {
            'tabp' => '时间流量包',
            'time' => '时间包',
            'bandwidth' => '流量包',
            default => '其他',
        };
    }

    /**
     * 商品库存
     */
    public function stock(): string|int
    {
        return $this->stock < 0 ? '无限制' : $this->stock;
    }
}
