<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id              订单ID
 * @property int    $user_id         提交用户ID
 * @property int    $product_id      商品ID
 * @property string $product_type    商品类型
 * @property string $product_name    商品名称
 * @property string $product_content 商品内容
 * @property string $coupon          订单优惠码
 * @property float  $price           订单金额
 * @property string $status          订单状态
 * @property int    $create_time     创建时间
 * @property int    $update_time     更新时间
 *
 * @mixin Builder
 */
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
