<?php

declare(strict_types=1);

namespace App\Models;

use function time;

final class Payback extends Model
{
    protected $connection = 'default';
    protected $table = 'payback';

    public function user()
    {
        return User::where('id', $this->userid)->first();
    }

    public function refUser()
    {
        return User::where('id', $this->ref_by)->first();
    }

    public static function rebate($user_id, $order_amount): void
    {
        $configs = Setting::getClass('invite');
        $user = User::where('id', $user_id)->first();
        $gift_user_id = $user->ref_by;

        // 判断
        $invite_rebate_mode = (string) $configs['invite_rebate_mode'];
        if ($invite_rebate_mode === 'continued') {
            // 不设限制
            self::executeRebate($user_id, $gift_user_id, $order_amount);
        } elseif ($invite_rebate_mode === 'limit_frequency') {
            // 限制返利次数
            $rebate_frequency = self::where('userid', $user_id)->count();
            if ($rebate_frequency < $configs['rebate_frequency_limit']) {
                self::executeRebate($user_id, $gift_user_id, $order_amount);
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
                    self::executeRebate($user_id, $gift_user_id, $order_amount, $adjust_rebate);
                }
            } else {
                self::executeRebate($user_id, $gift_user_id, $order_amount);
            }
        } elseif ($invite_rebate_mode === 'limit_time_range') {
            if (strtotime($user->reg_date) + $configs['rebate_time_range_limit'] * 86400 > time()) {
                self::executeRebate($user_id, $gift_user_id, $order_amount);
            }
        }
    }

    public static function executeRebate($user_id, $gift_user_id, $order_amount, $adjust_rebate = null): void
    {
        $gift_user = User::where('id', $gift_user_id)->first();
        if ($gift_user !== null) {
            $rebate_amount = $order_amount * Setting::obtain('rebate_ratio');
            // 返利
            $money_before = $gift_user->money;
            $gift_user->money += $adjust_rebate ?? $rebate_amount;
            $gift_user->save();
            // 余额变动记录
            (new UserMoneyLog())->addMoneyLog(
                $gift_user->id,
                (float) $money_before,
                (float) $gift_user->money,
                $adjust_rebate ?? $rebate_amount,
                '邀请用户 #' . $user_id . ' 返利',
            );
            // 记录
            $payback = new Payback();
            $payback->total = $order_amount;
            $payback->userid = $user_id;
            $payback->ref_by = $gift_user_id;
            $payback->ref_get = $adjust_rebate ?? $rebate_amount;
            $payback->datetime = time();
            $payback->save();
        }
    }
}
