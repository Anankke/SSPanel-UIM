<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;
use function time;

/**
 * @property int   $id       记录ID
 * @property float $total    总金额
 * @property int   $userid   用户ID
 * @property int   $ref_by   推荐人ID
 * @property float $ref_get  推荐人获得金额
 * @property int   $datetime 创建时间
 *
 * @mixin Builder
 */
final class Payback extends Model
{
    protected $connection = 'default';
    protected $table = 'payback';

    public function user(): \Illuminate\Database\Eloquent\Model|User|null
    {
        return (new User())->where('id', $this->userid)->first();
    }

    public function getUserNameAttribute(): string
    {
        return (new User())->where('id', $this->userid)->first() === null ? '已注销' :
            (new User())->where('id', $this->userid)->first()->user_name;
    }

    public function refUser(): \Illuminate\Database\Eloquent\Model|User|null
    {
        return (new User())->where('id', $this->ref_by)->first();
    }

    public function getRefUserNameAttribute(): string
    {
        return (new User())->where('id', $this->ref_by)->first() === null ? '已注销' :
            (new User())->where('id', $this->ref_by)->first()->user_name;
    }

    public function rebate($user_id, $order_amount): void
    {
        $configs = Config::getClass('ref');
        $user = (new User())->where('id', $user_id)->first();
        $gift_user_id = $user->ref_by;
        // 判断
        $invite_rebate_mode = (string) $configs['invite_rebate_mode'];

        if ($invite_rebate_mode === 'continued') {
            // 不设限制
            $this->execute($user_id, $gift_user_id, $order_amount);
        } elseif ($invite_rebate_mode === 'limit_frequency') {
            // 限制返利次数
            $rebate_frequency = self::where('userid', $user_id)->count();

            if ($rebate_frequency < $configs['rebate_frequency_limit']) {
                $this->execute($user_id, $gift_user_id, $order_amount);
            }
        } elseif ($invite_rebate_mode === 'limit_amount') {
            // 限制返利金额
            $total_rebate_amount = self::where('userid', $user_id)->sum('ref_get');
            // 预计返利 (expected_rebate) 是指：订单金额 * 返点比例
            $expected_rebate = $order_amount * $configs['rebate_ratio'];
            // 调整返利 (adjust_rebate) 是指：若历史返利总额在加上此次预计返利金额超过总返利限制，总返利限制与历史返利总额的差值
            if ($total_rebate_amount + $expected_rebate > $configs['rebate_amount_limit']
                && $total_rebate_amount <= $configs['rebate_amount_limit']
            ) {
                $adjust_rebate = $configs['rebate_amount_limit'] - $total_rebate_amount;

                if ($adjust_rebate > 0) {
                    $this->execute($user_id, $gift_user_id, $order_amount, $adjust_rebate);
                }
            } else {
                $this->execute($user_id, $gift_user_id, $order_amount);
            }
        } elseif ($invite_rebate_mode === 'limit_time_range') {
            if (strtotime($user->reg_date) + $configs['rebate_time_range_limit'] * 86400 > time()) {
                $this->execute($user_id, $gift_user_id, $order_amount);
            }
        }
    }

    public function execute($user_id, $gift_user_id, $order_amount, $adjust_rebate = null): void
    {
        $gift_user = (new User())->where('id', $gift_user_id)
            ->where('is_banned', 0)
            ->where('is_shadow_banned', 0)
            ->first();

        if ($gift_user !== null) {
            $rebate_amount = $order_amount * Config::obtain('rebate_ratio');
            // 返利
            $money_before = $gift_user->money;
            $gift_user->money += $adjust_rebate ?? $rebate_amount;
            $gift_user->save();
            // 余额变动记录
            (new UserMoneyLog())->add(
                $gift_user->id,
                (float) $money_before,
                (float) $gift_user->money,
                $adjust_rebate ?? $rebate_amount,
                '邀请用户 #' . $user_id . ' 返利',
            );
            // 添加记录
            $this->total = $order_amount;
            $this->userid = $user_id;
            $this->ref_by = $gift_user_id;
            $this->ref_get = $adjust_rebate ?? $rebate_amount;
            $this->datetime = time();
            $this->save();
        }
    }
}
