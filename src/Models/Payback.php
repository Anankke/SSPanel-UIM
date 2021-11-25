<?php

namespace App\Models;

class Payback extends Model
{
    protected $connection = 'default';
    protected $table = 'payback';

    public function user()
    {
        $user = User::where('id', $this->attributes['userid'])->first();
        if ($user == null) {
            Bought::where('id', '=', $this->attributes['id'])->delete();
            return null;
        }

        return $user;
    }

    public function rebate($user_id, $order_amount)
    {
        $configs = Setting::getClass('invite');
        $user = User::where('id', $user_id)->first();
        $gift_user_id = $user->ref_by;
        
        // 判断
        $invite_rebate_mode = $configs['invite_rebate_mode'];
        $rebate_ratio = $configs['rebate_ratio'];
        if ($invite_rebate_mode == 'continued') {
            // 不设限制
            self::executeRebate($user_id, $gift_user_id, $order_amount);
        } elseif ($invite_rebate_mode == 'limit_frequency') {
            // 限制返利次数
            $rebate_frequency = self::where('userid', $user_id)->count();
            if ($rebate_frequency < $configs['rebate_frequency_limit']) {
                self::executeRebate($user_id, $gift_user_id, $order_amount);
            }
        } elseif ($invite_rebate_mode == 'limit_amount') {
            // 限制返利金额
            $total_rebate_amount = self::where('userid', $user_id)->sum('ref_get');
            // 预计返利 (expected_rebate) 是指：订单金额 * 返点比例
            $expected_rebate = $order_amount * $rebate_ratio;
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
        } elseif ($invite_rebate_mode == 'limit_time_range') {
            if (strtotime($user->reg_date) + $configs['rebate_time_range_limit'] * 86400 > time()) {
                self::executeRebate($user_id, $gift_user_id, $order_amount);
            }
        }
    }

    public function executeRebate($user_id, $gift_user_id, $order_amount, $adjust_rebate = null)
    {
        $gift_user = User::where('id', $gift_user_id)->first();
        $rebate_amount = $order_amount * Setting::obtain('rebate_ratio');
        // 返利
        $gift_user->money += $adjust_rebate ?? $rebate_amount;
        $gift_user->save();
        // 记录
        $Payback = new Payback();
        $Payback->total = $order_amount;
        $Payback->userid = $user_id;
        $Payback->ref_by = $gift_user_id;
        $Payback->ref_get = $adjust_rebate ?? $rebate_amount;
        $Payback->datetime = time();
        $Payback->save();
    }
}
