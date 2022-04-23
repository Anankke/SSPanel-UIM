<?php
namespace App\Models;

class Payback extends Model
{
    protected $connection = 'default';
    protected $table = 'payback';

    public function user()
    {
        $user = User::where('id', $this->attributes['userid'])->first();
        return $user;
    }

    public function getDatetimeAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getAssociatedOrderAttribute($value)
    {
        return ($value == null) ? 'null' : $value;
    }

    public function getFraudDetectAttribute($value)
    {
        return ($value == '0') ? '通过' : '欺诈';
    }

    public static function fraudDetection($user)
    {
        $initiator_f = [];
        $initiator = Fingerprint::where('user_id', $user->id)
            ->select('fingerprint')
            ->distinct()
            ->get();
        $initiator = json_encode($initiator);
        $initiator = json_decode($initiator, true); // 直接对去重结果使用 toArray() 方法会报错
        foreach ($initiator as $key => $value) {
            array_push($initiator_f, $value['fingerprint']);
        }

        $receiver_f = [];
        $receiver = Fingerprint::where('user_id', $user->ref_by)
            ->select('fingerprint')
            ->distinct()
            ->get();
        $receiver = json_encode($receiver);
        $receiver = json_decode($receiver, true);
        foreach ($receiver as $key => $value) {
            array_push($receiver_f, $value['fingerprint']);
        }

        //var_dump($initiator_f);
        //var_dump($receiver_f);
        //var_dump($initiator);
        //var_dump($receiver);

        $intersection = array_intersect($initiator_f, $receiver_f);
        return (count($intersection) > 0) ? false : true; // false为没有通过检测
    }

    public function rebate($user_id, $order_amount, $order_no)
    {
        $configs = Setting::getClass('invite');
        $user = User::where('id', $user_id)->first();
        $gift_user_id = $user->ref_by;

        $invite_rebate_mode = $configs['invite_rebate_mode'];
        $rebate_ratio = $configs['rebate_ratio'];
        // 返利风控
        if ($_ENV['rebate_risk_control']) {
            if (!self::fraudDetection($user)) {
                return self::executeRebate($order_no, $user_id, $gift_user_id, $order_amount, null, true);
            }
        }
        if ($invite_rebate_mode == 'continued') {
            // 不设限制
            self::executeRebate($order_no, $user_id, $gift_user_id, $order_amount);
        } elseif ($invite_rebate_mode == 'limit_frequency') {
            // 限制返利次数
            $rebate_frequency = self::where('userid', $user_id)->count();
            if ($rebate_frequency < $configs['rebate_frequency_limit']) {
                self::executeRebate($order_no, $user_id, $gift_user_id, $order_amount);
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
                    self::executeRebate($order_no, $user_id, $gift_user_id, $order_amount, $adjust_rebate);
                }
            } else {
                self::executeRebate($order_no, $user_id, $gift_user_id, $order_amount);
            }
        } elseif ($invite_rebate_mode == 'limit_time_range') {
            if (strtotime($user->reg_date) + $configs['rebate_time_range_limit'] * 86400 > time()) {
                self::executeRebate($order_no, $user_id, $gift_user_id, $order_amount);
            }
        }
    }

    public function executeRebate($order_no, $user_id, $gift_user_id, $order_amount, $adjust_rebate = null, $fraud = false)
    {
        // 查询
        $gift_user = User::where('id', $gift_user_id)->first();
        $rebate_amount = $order_amount * Setting::obtain('rebate_ratio');
        // 返利
        if (!$fraud) {
            $gift_user->money += $adjust_rebate ?? $rebate_amount;
            $gift_user->save();
        }
        // 记录
        $payback_log = new Payback();
        $payback_log->total = $order_amount;
        $payback_log->userid = $user_id;
        $payback_log->ref_by = $gift_user_id;
        $payback_log->ref_get = $adjust_rebate ?? $rebate_amount;
        $payback_log->fraud_detect = ($fraud) ? '1' : '0'; // 0为通过; 1为欺诈
        $payback_log->associated_order = $order_no;
        $payback_log->datetime = time();
        $payback_log->save();
    }
}
