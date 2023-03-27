<?php

declare(strict_types=1);

namespace App\Models;

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
        if ($this->stock < 0) {
            return '无限制';
        }
        return $this->stock;
    }
}
