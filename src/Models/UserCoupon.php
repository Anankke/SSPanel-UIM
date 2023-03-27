<?php

declare(strict_types=1);

namespace App\Models;

use function json_decode;

final class UserCoupon extends Model
{
    protected $connection = 'default';
    protected $table = 'user_coupon';

    /**
     * 优惠码类型
     */
    public function type(): string
    {
        $content = json_decode($this->content);

        return match ($content->type) {
            'percentage' => '百分比',
            'fixed' => '固定金额',
            default => '未知',
        };
    }

    /**
     * 优惠码状态
     */
    public function status(): string
    {
        if ($this->expire_time < time()) {
            return '已过期';
        }
        return '激活';
    }
}
