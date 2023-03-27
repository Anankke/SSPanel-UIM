<?php

declare(strict_types=1);

namespace App\Models;

final class Invoice extends Model
{
    protected $connection = 'default';
    protected $table = 'invoice';

    /**
     * 账单状态
     */
    public function status(): string
    {
        return match ($this->status) {
            'unpaid' => '未支付',
            'paid_gateway' => '已支付（支付网关）',
            'paid_balance' => '已支付（账户余额）',
            'paid_admin' => '已支付（管理员）',
            'cancelled' => '已取消',
            default => '未知',
        };
    }
}
