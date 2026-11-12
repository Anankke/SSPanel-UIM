<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;
use function json_decode;
use function property_exists;
use function time;

/**
 * @property int    $id          优惠码ID
 * @property string $code        优惠码
 * @property string $content     优惠码内容
 * @property string $limit       优惠码限制
 * @property int    $use_count   累计使用次数
 * @property int    $create_time 创建时间
 * @property int    $expire_time 过期时间
 *
 * @mixin Builder
 */
final class UserCoupon extends Model
{
    protected $connection = 'default';
    protected $table = 'user_coupon';

    /**
     * 优惠码类型
     */
    public function type(): string
    {
        return match (json_decode($this->content)->type ?? null) {
            'percentage' => '百分比',
            'fixed' => '固定金额',
            default => '未知',
        };
    }

    /**
     * 是否仅限新用户使用
     */
    public function isNewUserOnly(): bool
    {
        $limit = json_decode($this->limit);

        return property_exists($limit, 'new_user') && (int) $limit->new_user === 1;
    }

    /**
     * 优惠码是否允许该用户使用
     */
    public function allowsUser(User $user): bool
    {
        return ! $this->isNewUserOnly() || $user->isNewUser();
    }

    /**
     * 优惠码状态
     */
    public function status(): string
    {
        return $this->expire_time < time() ? '已过期' : '激活';
    }
}
