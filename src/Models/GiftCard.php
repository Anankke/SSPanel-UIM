<?php

declare(strict_types=1);

namespace App\Models;

final class GiftCard extends Model
{
    protected $connection = 'default';
    protected $table = 'gift_card';

    /**
     * 礼品卡状态
     */
    public function status(): string
    {
        return $this->status ? '已使用' : '未使用';
    }
}
