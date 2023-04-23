<?php

declare(strict_types=1);

namespace App\Models;

final class Order extends Model
{
    protected $connection = 'default';
    protected $table = 'order';

    /**
     * 订单状态
     */
    public function status(): string
    {
        return match ($this->status) {
            'pending_payment' => '等待中',
            'pending_activation' => '待激活',
            'activated' => '已激活',
            'expired' => '已过期',
            'cancelled' => '已取消',
            default => '未知',
        };
    }

    /**
     * 订单商品类型
     */
    public function productType(): string
    {
        return match ($this->product_type) {
            'tabp' => '时间流量包',
            'time' => '时间包',
            'bandwidth' => '流量包',
            default => '其他',
        };
    }
}
