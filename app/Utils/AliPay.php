<?php
/**
 * 支付宝检测监听 每分钟大概运行10次 间隔为5秒
 * cookie失效会通过email通知
 * 请务必设置好邮箱配置
 * User: chen yun.9in.info
 * Date: 9/12/18
 * Time: 12:33 PM
 */

namespace App\Utils;

use App\Services\Config;
use App\Models\User;
use App\Models\Code;
use App\Models\Paylist;
use App\Models\Payback;
use App\Services\Mail;

class AliPay
{
    static $file = __DIR__ . '../../storage/framework/smarty/cache/aliPayDie.ini';

    public static function getHTML()
    {
        $a = '<a class="btn btn-flat waves-attach" id="urlChangeAliPay" ><span class="icon">check</span>&nbsp;充值</a>';
        if (file_exists(static::$file))
            $a = '<a class="btn btn-flat waves-attach" href="javascript:;"><span class="icon">check</span>&nbsp;暂停使用，稍后再试！</a>';
        return '
                        <div class="form-group pull-left">
                        <p class="modal-title" >本站支持支付宝在线充值</p>
                        <p>输入充值金额：</p>
                        <div class="form-group form-group-label">
                        <label class="floating-label" for="price">充值金额</label>
                        <input type="number" id="AliPayType" class="form-control" name="amount" />
                        </div>' . $a . '</div>
                        <div class="form-group pull-right">
                        <img src="/images/qianbai-4.png" height="205" width="166" />
                        </div>
';
    }

    public static function AliPay_callback($trade, $order)
    {
        if ($trade == null) {//没有符合的订单，或订单已经处理，或订单号为空则判断为未支付
            exit();
        }
        $trade->tradeno = $order;
        $trade->status = 1;
        $trade->save();
        $user = User::find($trade->userid);
        $user->money = $user->money + $trade->total;
//        if ($user->class == 0) {
//            $user->class_expire = date("Y-m-d H:i:s", time());
//            $user->class_expire = date("Y-m-d H:i:s", strtotime($user->class_expire) + 86400);
//            $user->class = 1;
//        }
        $user->save();
        $codeq = new Code();
        $codeq->code = "ChenPay充值" . $order;
        $codeq->isused = 1;
        $codeq->type = -1;
        $codeq->number = $trade->total;
        $codeq->usedatetime = date("Y-m-d H:i:s");
        $codeq->userid = $user->id;
        $codeq->save();
        if ($user->ref_by != "" && $user->ref_by != 0 && $user->ref_by != null) {
            $gift_user = User::where("id", "=", $user->ref_by)->first();
            $gift_user->money = $gift_user->money + ($codeq->number * 0.2);
            $gift_user->save();
            $Payback = new Payback();
            $Payback->total = $trade->total;
            $Payback->userid = $user->id;
            $Payback->ref_by = $user->ref_by;
            $Payback->ref_get = $codeq->number * 0.2;
            $Payback->datetime = time();
            $Payback->save();
        }
    }

    public static function orderDelete($id)
    {
        return Paylist::where("id", '=', $id)->where('status', 0)->delete();
    }

    public static function getCookieName($name = 'uid')
    {
        $cookie = explode($name . '=', Config::get("AliPay_Cookie"))[1];
        if ($name == 'uid') return explode('"', $cookie)[0];
        else return explode(';', $cookie)[0];
    }

    public static function getAliPay()
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->createRequest('POST', "https://mbillexprod.alipay.com/enterprise/tradeListQuery.json", ['headers' => [
            'Accept' => 'application/json, text/javascript',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7',
            'Connection' => 'keep-alive',
            'Content-Length' => '295',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Cookie' => Config::get("AliPay_Cookie"),
            'Host' => 'mbillexprod.alipay.com',
            'Origin' => 'https://mbillexprod.alipay.com',
            'Referer' => 'https://mbillexprod.alipay.com/enterprise/tradeListQuery.htm',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest'
        ], 'body' => 'queryEntrance=1&billUserId=' . static::getCookieName('uid') .
            '&status=SUCCESS&entityFilterType=0&activeTargetSearchItem=tradeNo&tradeFrom=ALL&' .
            'startTime=' . date('Y-m-d') . '+00%3A00%3A00&endTime=' . date('Y-m-d', strtotime('+1 day')) . '+00%3A00%3A00&' .
            'pageSize=20&pageNum=1&sortTarget=gmtCreate&order=descend&sortType=0&' .
            '_input_charset=gbk&ctoken=' . static::getCookieName('ctoken')]);
        return iconv('GBK', 'UTF-8', $client->send($request)->getBody()->getContents());
    }

    public static function newOrder($user, $amount)
    {
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $amount;
        $pl->datetime = time() + 3 * 60;//有效时间
        $pl->save();
        $pl->ret = 1;
        return $pl;
    }

    public static function checkOrder($id)
    {
        $pl = Paylist::find($id);
        $pl->ret = 1;
        return $pl;
    }

    public static function comparison($json, $fee, $time)
    {
        if (isset($json['result']['detail'])) {
            if (is_array($json['result']['detail'])) {
                foreach ($json['result']['detail'] as $item) {
                    if ($item['tradeFrom'] == '外部商户' && $item['direction'] == '卖出' &&
                        strtotime($item['gmtCreate']) < $time && $item['totalAmount'] == $fee) {
                        return $item['outTradeNo'];
                    }
                }
            }
        }
        return false;
    }

    public static function sendMail()
    {
        $time = date('Y-m-d H:i:s');
        Mail::getClient()->send(Config::get('AliPay_EMail'), 'LOG报告监听COOKIE出现问题', "LOG提醒你，COOKIE出现问题，请务必尽快更新COOKIE。<br>LOG记录时间：$time", []);
        if (!file_exists(static::$file)) file_put_contents(static::$file, '1');
    }

    public static function checkAliPayOne()
    {
        $json = static::getAliPay();
        if (!$json) self::sendMail();
        else if (file_exists(static::$file)) unlink(static::$file);
        $tradeAll = Paylist::where('status', 0)->where('datetime', '>', time())->orderBy('id', 'desc')->get();
        foreach ($tradeAll as $item) {
            $order = static::comparison(json_decode($json, true), $item->total, $item->datetime);
            if ($order) {
                if (!Paylist::where('tradeno', $order)->first()) static::AliPay_callback($item, $order);
            }
        }
        Paylist::where('status', 0)->where('datetime', '<', time())->delete();
    }

    public static function checkAliPay()
    {
        for ($i = 1; $i <= 10; $i++) {
            self::checkAliPayOne();
            sleep(5);
        }
    }
}
