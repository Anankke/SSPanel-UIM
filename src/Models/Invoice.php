<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\DB;
use App\Services\Wallet;
use Illuminate\Database\Query\Builder;
use function in_array;
use function json_decode;
use function json_encode;
use function time;

/**
 * @property int    $id          账单ID
 * @property string $type        账单类型
 * @property int    $user_id     归属用户ID
 * @property string $order_id    订单ID
 * @property string $content     账单内容
 * @property float  $price       账单金额
 * @property string $status      账单状态
 * @property int    $create_time 创建时间
 * @property int    $update_time 更新时间
 * @property int    $pay_time    支付时间
 *
 * @mixin Builder
 */
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
            'refunded_balance' => '已退款（账户余额）',
            'partially_paid' => '部分支付',
            default => '未知',
        };
    }

    public function type(): string
    {
        return match ($this->type) {
            'product' => '商品',
            'topup' => '充值',
            default => '未知',
        };
    }

    /**
     * 账单当前是否还可以收款
     */
    public function acceptsPayment(): bool
    {
        return in_array($this->status, ['unpaid', 'partially_paid'], true);
    }

    public function refundToBalance(): void
    {
        $id = $this->id;

        DB::connection()->transaction(static function () use ($id): void {
            $invoice = (new Invoice())->lockForUpdate()->find($id);

            if ($invoice === null ||
                ! in_array($invoice->status, ['paid_gateway', 'paid_balance', 'paid_admin'])
            ) {
                return;
            }

            Wallet::credit($invoice->user_id, (string) $invoice->price, '账单 #' . $invoice->id . ' 退款至账户余额');

            $content = json_decode($invoice->content, true);
            $content[] = [
                'content_id' => count($content),
                'name' => '退款至账户余额',
                'price' => '-' . $invoice->price,
            ];

            $invoice->content = json_encode($content);
            $invoice->status = 'refunded_balance';
            $invoice->update_time = time();
            $invoice->save();
        }, 3);
    }
}
