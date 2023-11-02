<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id          礼品卡ID
 * @property string $card        卡号
 * @property int    $balance     余额
 * @property int    $create_time 创建时间
 * @property int    $status      使用状态
 * @property int    $use_time    使用时间
 * @property int    $use_user    使用用户
 *
 * @mixin Builder
 */
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
