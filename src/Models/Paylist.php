<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id         记录ID
 * @property int    $userid     用户ID
 * @property float  $total      总金额
 * @property int    $status     状态
 * @property int    $invoice_id 账单ID
 * @property string $tradeno    网关识别码
 * @property string $gateway    支付网关
 * @property int    $datetime   创建时间
 *
 * @mixin Builder
 */
final class Paylist extends Model
{
    protected $connection = 'default';
    protected $table = 'paylist';

    /**
     * 网关记录状态
     */
    public function status(): string
    {
        return match ($this->status) {
            0 => '未支付',
            1 => '已支付',
            default => '未知',
        };
    }
}
