<?php
/**
 * Created by 傲慢与偏见.
 * User: Administrator
 * Date: 2017/11/21
 * Time: 8:57
 *
 *       虽然代码开源，但是请别直接照搬好吗？直接照搬也别改的面目全非好吗？改的面目全非也别删除注释好吗？
 *
 *       抄代码之前想想为什么这样写，这样才能有提高。建议你去学学面向对象编程方式。
 *
 *       另外，我看了你的代码，真的写的很烂，乱。
 *
 */

namespace App\Controllers;

use App\Models\Payback;
use App\Models\User;
use App\Models\YftOrder;
use App\Services\Config;
use App\Utils\Telegram;

class YFTPayCallBackController
{
    public function yft_notify($request)
    {
        //价格
        $total_fee = $request->getQueryParams()["total_fee"];//必填
        //易付通返回的订单号
        $yft_order_no = $request->getQueryParams()["trade_no"];
        //面板生成的订单号
        $ss_order_no = $request->getQueryParams()["out_trade_no"];//必填
        //订单说明
        $subject = $request->getQueryParams()["subject"];//必填
        //付款状态
        $trade_status = $request->getQueryParams()["trade_status"];//必填
        //加密验证字符串
        $sign = $request->getQueryParams()["sign"];//必填

        $verifyNotify = YFTPayCallBackController::md5Verify(floatval($total_fee), $ss_order_no, $yft_order_no, $trade_status, $sign);
        if ($verifyNotify) {//验证成功
            if ($trade_status == 'TRADE_SUCCESS') {
                /*
                加入您的入库及判断代码;
                >>>>>>>！！！为了保证数据传达到回调地址，会请求4次。所以必须要先判断订单状态，然后再插入到数据库，这样后面即使请求3次，也不会造成订单重复！！！！<<<<<<<
                判断返回金额与实金额是否想同;
                判断订单当前状态;
                完成以上才视为支付成功
                */
                $orderInfo = new YftOrder();
                $orderInfo = $orderInfo->where("ss_order", "=", $ss_order_no)->first();
                if ($orderInfo == "" || $orderInfo == null) {
                    return "fail";
                }

                if ($orderInfo->price != $total_fee) {
                    return "fail";
                }

                $userInfo = new User();
                $userInfo = $userInfo->where("id", "=", $orderInfo->user_id)->first();

                if (sizeof($orderInfo) != 0 && $orderInfo->state == 0) {
                    $oldMoney = $userInfo->money;
                    $userInfo->money = $total_fee + $oldMoney;
                    //更新用户余额信息
                    $userInfo->save();
                    //更新订单信息
                    $orderInfo->yft_order = $yft_order_no;
                    $orderInfo->state = 1;
                    $orderInfo->save();
                    //充值返利处理 start
                    if ($userInfo->ref_by != "" && $userInfo->ref_by != 0 && $userInfo->ref_by != null && Config::get('code_payback') != 0 && Config::get('code_payback') != null) {
                        $gift_user = User::where("id", "=", $userInfo->ref_by)->first();
                        $gift_user->money = ($gift_user->money + ($total_fee * (Config::get('code_payback') / 100)));
                        $gift_user->save();

                        $Payback = new Payback();
                        $Payback->total = $total_fee;
                        $Payback->userid = $userInfo->id;
                        $Payback->ref_by = $userInfo->ref_by;
                        $Payback->ref_get = $total_fee * (Config::get('code_payback') / 100);
                        $Payback->datetime = time();
                        $Payback->save();
                    }
                    //充值返利处理 end
                    //telegram提醒
                    if (Config::get('enable_donate') == 'true' && Config::get("enable_telegram") == 'true') {
                        if ($userInfo->is_hide == 1) {
                            Telegram::Send("感谢！一位不愿透露姓名的大老爷给我们捐了 " . $total_fee . " 元呢~");
                        } else {
                            Telegram::Send("感谢！" . $userInfo->user_name . " 大老爷给我们捐了 " . $total_fee . " 元呢~");
                        }
                    }
                } else {
                    return "success";
                }
                return "success";
            } else {
                return "fail";
            }
        } else {
            //验证失败
            return "fail";
        }
    }

    /**
     * @param $p1
     * @param $p2
     * @param $p3
     * @param $p4
     * @param $sign 传入要比对的sign
     * @return boolean 返回比对结果
     */
    private static function md5Verify($p1, $p2, $p3, $p4, $sign)
    {
        $preStr = $p1 . $p2 . $p3 . $p4 . "yft";
        $mySign = md5($preStr);
        if ($mySign == $sign) {
            return true;
        } else {
            return false;
        }
    }
}