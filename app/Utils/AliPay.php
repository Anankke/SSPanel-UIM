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
    private $config = [];

    public function __construct()
    {
        $data = [];
        foreach (\App\Models\Config::get() as $item) $data[$item->name] = $item->value;
        $this->config = $data;
    }

    public function getHTML()
    {
        $a = '';
        if (!$this->getConfig('AliPay_Status') == 0)
            $a .= '<a class="btn btn-flat waves-attach" id="urlChangeAliPay" type="1" ><img src="/images/alipay.jpg" width="45"></a>';
        if (!$this->getConfig('WxPay_Status') == 0)
            $a .= '<a class="btn btn-flat waves-attach" id="urlChangeAliPay2" type="2"><img src="/images/weixin.jpg" width="45"></a>';
        $html = '<div class="form-group pull-left">
                        <p class="modal-title" >本站支持支付宝/微信在线充值</p>';
        if (preg_match('/\|/', $this->getConfig('Pay_Price'))) {
            $data = explode('|', $this->getConfig('Pay_Price'));
            $html .= '<p>选择充值金额：</p><div class="form-group form-group-label btnBox">';
            foreach ($data as $key => $item)
                $html .= '<a class="btn btn-price ' . ($key == 0 ? 'active' : '') . '" price="' . $item . '" type="' . $key . '">' . $item . '元</a>';
            $html .= '<input type="hidden" id="AliPayType" class="form-control" name="amount" />';
        } else $html .= '<p>输入充值金额：</p><div class="form-group form-group-label btnBox"><label class="floating-label" for="price">充值金额</label>
                        <input type="number" id="AliPayType" class="form-control" name="amount" />';
        $html .= '</div>' . $a . '</div>
                        <div class="form-group pull-right">
                        <img src="/images/qianbai-2.png" height="205" />
                        </div>';
        return $html;
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

    public static function orderDelete($id, $user)
    {
        return Paylist::where("id", $id)->where('status', 0)->where('userid', $user)->delete();
    }

    public static function getList()
    {
        return Paylist::where('status', 0)->where('url', null)->get();
    }

    public static function setOrder($sn, $url)
    {
        return Paylist::where('sys_sn', $sn)->update(['url' => $url]);
    }

    public function getCookieName($name = 'uid', $cookie = false)
    {
        $cookie = explode($name . '=', $cookie ? $cookie : $this->getConfig('AliPay_Cookie'))[1];
        if ($name == 'uid') return explode('"', $cookie)[0];
        else return explode(';', $cookie)[0];
    }

    public function getAliPay()
    {
//        $request = $client->createRequest('POST', "https://mbillexprod.alipay.com/enterprise/tradeListQuery.json", ['headers' => [
//            'Accept' => 'application/json, text/javascript',
//            'Accept-Encoding' => 'gzip, deflate, br',
//            'Accept-Language' => 'en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7',
//            'Connection' => 'keep-alive',
//            'Content-Length' => '295',
//            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
//            'Cookie' => self::getConfig('AliPay_Cookie'),
//            'Host' => 'mbillexprod.alipay.com',
//            'Origin' => 'https://mbillexprod.alipay.com',
//            'Referer' => 'https://mbillexprod.alipay.com/enterprise/tradeListQuery.htm',
//            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
//            'X-Requested-With' => 'XMLHttpRequest'
//        ], 'body' => 'queryEntrance=1&billUserId=' . $this->getCookieName('uid') .
//            '&status=SUCCESS&entityFilterType=0&activeTargetSearchItem=tradeNo&tradeFrom=ALL&' .
//            'startTime=' . date('Y-m-d') . '+00%3A00%3A00&endTime=' . date('Y-m-d', strtotime('+1 day')) . '+00%3A00%3A00&' .
//            'pageSize=20&pageNum=1&sortTarget=gmtCreate&order=descend&sortType=0&' .
//            '_input_charset=gbk&ctoken=' . $this->getCookieName('ctoken')]);

        $html = (new \GuzzleHttp\Client())
            ->request('POST', "https://mbillexprod.alipay.com/enterprise/fundAccountDetail.json",  ['headers' => [
                'Accept' => 'application/json, text/javascript',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7',
                'Connection' => 'keep-alive',
                'Content-Length' => '295',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Cookie' => $this->getConfig('AliPay_Cookie'),
                'Host' => 'mbillexprod.alipay.com',
                'Origin' => 'https://mbillexprod.alipay.com',
                'Referer' => 'https://mbillexprod.alipay.com/enterprise/fundAccountDetail.htm',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
                'X-Requested-With' => 'XMLHttpRequest'
            ], 'body' => 'queryEntrance=1&billUserId=' . $this->getCookieName('uid') .
                '&showType=1&type=&precisionQueryKey=tradeNo&' .
                'startDateInput=' . date('Y-m-d', strtotime('-1 day')) . '+00%3A00%3A00&endDateInput=' . date('Y-m-d') . '+23%3A59%3A59&' .
                'pageSize=20&pageNum=1&sortTarget=tradeTime&order=descend&sortType=0&' .
                '_input_charset=gbk&ctoken=' . $this->getCookieName('ctoken')])
            ->getBody();
        return iconv('GBK', 'UTF-8', $html->getContents());
    }

    public function getWxSyncKey()
    {
        $html = (new \GuzzleHttp\Client())
            ->request('POST', "https://" . $this->getConfig('WxPay_Url') . "/cgi-bin/mmwebwx-bin/webwxinit?r=695888609",
                ['headers' => [
                    'Accept' => 'application/json, text/javascript',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Accept-Language' => 'en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7',
                    'Connection' => 'keep-alive',
                    'Content-Length' => '295',
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'Cookie' => $this->getConfig('WxPay_Cookie'),
                    'Host' => $this->getConfig('WxPay_Url'),
                    'Origin' => 'https://' . $this->getConfig('WxPay_Url'),
                    'Referer' => 'https://' . $this->getConfig('WxPay_Url') . '/',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
                ], 'body' => '{"BaseRequest":{"Uin":' . $this->getCookieName('wxuin', $this->getConfig('WxPay_Cookie')) .
                    ',"Sid":"' . $this->getCookieName('wxsid', $this->getConfig('WxPay_Cookie')) . '","Skey":' .
                    '"","DeviceID":"e453731506754000"}}'
                ])
            ->getBody();
        $data = json_decode($html->getContents(), true);
        return $data;
    }

    public function getWxPay()
    {
        if (!$this->getConfig('WxPay_SyncKey') || preg_match('/"Count"\:0/', $this->getConfig('WxPay_SyncKey'))) {
            $syncJson = $this->getWxSyncKey();
            if ($syncJson['BaseResponse']['Ret'] > 0) return json_encode($syncJson, true);
            $sync = json_encode($syncJson['SyncKey']);
        } else $sync = $this->getConfig('WxPay_SyncKey');
        $html = (new \GuzzleHttp\Client())
            ->request('POST', "https://" . $this->getConfig('WxPay_Url') . "/cgi-bin/mmwebwx-bin/webwxsync?sid=" .
                $this->getCookieName('wxsid', $this->getConfig('WxPay_Cookie')) . "&skey=",
                ['headers' => [
                    'Accept' => 'application/json, text/javascript',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Accept-Language' => 'en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7',
                    'Connection' => 'keep-alive',
                    'Content-Length' => '295',
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'Cookie' => $this->getConfig('WxPay_Cookie'),
                    'Host' => $this->getConfig('WxPay_Url'),
                    'Origin' => 'https://' . $this->getConfig('WxPay_Url'),
                    'Referer' => 'https://' . $this->getConfig('WxPay_Url') . '/',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
                ], 'body' => '{"BaseRequest":{"Uin":' . $this->getCookieName('wxuin', $this->getConfig('WxPay_Cookie')) .
                    ',"Sid":"' . $this->getCookieName('wxsid', $this->getConfig('WxPay_Cookie')) . '","Skey":"' .
                    '","DeviceID":"e453731506754000"},"SyncKey":' . $sync .
                    ',"rr":' . rand(100000000, 999999999) . '}'
                ])
            ->getBody();
        $data = $html->getContents();
        $this->setConfig('WxPay_SyncKey', json_encode(json_decode($data, true)['SyncKey']));
        return $data;
    }

    public static function newOrder($user, $amount, $type, $url)
    {
        $xposed = \App\Models\Config::where('name', 'Pay_Xposed')->first()->value;
        if ($xposed == 1 || !Paylist::where('status', 0)->where('datetime', '>', time())->first()) {
            $pl = new Paylist();
            $pl->userid = $user->id;
            $pl->total = $amount;
            $pl->datetime = time() + 3 * 60;// 有效时间
            $pl->sys_sn = rand(100000, 999999) . $user->id . $pl->datetime;
            $pl->type = $type;
            if ($xposed != 1) $pl->url = $url;
            $pl->save();
            $pl->ret = 1;
        } else {
            $pl = [
                'msg' => '正在排队中，请稍后再试！',
                'ret' => 0
            ];
        }
        return $pl;
    }

    public static function checkOrder($id)
    {
        $pl = Paylist::find($id);
        $pl->ret = 1;
        return $pl;
    }

    public function AliComparison($json, $fee, $time, $sn)
    {
        if (isset($json['result']['detail'])) {
            if (is_array($json['result']['detail'])) {
                foreach ($json['result']['detail'] as $item) {
//                    if ($item['tradeFrom'] == '外部商户' && $item['direction'] == '卖出' &&
//                        strtotime($item['gmtCreate']) < $time && $item['totalAmount'] == $fee) {
//                        return $item['outTradeNo'];
//                    }
                    if ($item['signProduct'] == '转账收款码' && $item['accountType'] == '交易' &&
                        strtotime($item['tradeTime']) < $time && $item['tradeAmount'] == $fee) {
                        if (!Paylist::where('tradeno', $item['orderNo'])->first())
                            return $item['orderNo'];
                    }
                }
            }
        }
        return false;
    }

    public function getConfig($name = false)
    {
        if ($name) return $this->config[$name];
        else return $this->config;
    }

    public function setConfig($name, $value)
    {
        \App\Models\Config::where('name', $name)->update(['value' => $value]);
        $this->config[$name] = $value;
        return $value;
    }

    public function WxComparison($json, $fee, $time, $sn)
    {
        if (isset($json['AddMsgList'])) {
            if (is_array($json['AddMsgList'])) {
                foreach ($json['AddMsgList'] as $item) {
//                    收款方备注：
                    if (preg_match('/微信支付收款/', $item['FileName'])) {
                        $fees = explode('微信支付收款', $item['FileName']);
                        $fees = explode('元', $fees[1])[0];
                        if ($item['CreateTime'] < $time && $fees == $fee) {
                            if ($this->getConfig('Pay_Xposed') == 1) {
                                $wxsn = explode('收款方备注：', $item['Content']);
                                $wxsn = explode('<br/>', $wxsn[1])[0];
                                if ($sn == $wxsn && !Paylist::where('tradeno', $item['MsgId'])->first())
                                    return $item['MsgId'];
                            } else {
                                if (!Paylist::where('tradeno', $item['MsgId'])->first())
                                    return $item['MsgId'];
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public function sendMail($type = 1)
    {
        $time = date('Y-m-d H:i:s');
        if ($this->getConfig('AliPay_Status') == 1 && $type == 1) {
            $name = '支付宝';
            $this->setConfig('AliPay_Status', 0);
            Mail::getClient()->send($this->getConfig('Notice_EMail'), 'LOG报告监听' . $name . 'COOKIE出现问题',
                "LOG提醒你，{$name}COOKIE出现问题，请务必尽快更新COOKIE。<br>LOG记录时间：$time", []);
        }
        if ($this->getConfig('WxPay_Status') == 1 && $type == 2) {
            $name = '微信';
            $this->setConfig('WxPay_Status', 0);
            Mail::getClient()->send($this->getConfig('Notice_EMail'), 'LOG报告监听' . $name . 'COOKIE出现问题',
                "LOG提醒你，{$name}COOKIE出现问题，请务必尽快更新COOKIE。<br>LOG记录时间：$time", []);
        }
    }

    public function sendSunMail($type = 1)
    {
        $time = date('Y-m-d H:i:s');
        if ($this->getConfig('AliPay_Status') == 0 && $type == 1) {
            $name = '支付宝';
            $this->setConfig('AliPay_Status', 1);
            Mail::getClient()->send($this->getConfig('Notice_EMail'), 'LOG报告监听' . $name . 'COOKIE成功运行',
                "LOG提醒你，{$name}COOKIE成功运行。<br>LOG记录时间：$time", []);
        }
        if ($this->getConfig('WxPay_Status') == 0 && $type == 2) {
            $name = '微信';
            $this->setConfig('WxPay_Status', 1);
            Mail::getClient()->send($this->getConfig('Notice_EMail'), 'LOG报告监听' . $name . 'COOKIE成功运行',
                "LOG提醒你，{$name}COOKIE成功运行。<br>LOG记录时间：$time", []);
        }
    }

    public function checkAliPayOne()
    {
        $json = json_decode($this->getAliPay(), true);
        if (!$json || isset($json['exception_marking']) || isset($json['target'])) $this->sendMail(1);
        else $this->sendSunMail(1);
        $tradeAll = Paylist::where('status', 0)->where('datetime', '>', time())->orderBy('id', 'desc')->get();
        foreach ($tradeAll as $item) {
            $order = $this->AliComparison($json, $item->total, $item->datetime, $item->sys_sn);
            if ($order) static::AliPay_callback($item, $order);
        }
    }

    public function checkWxPayOne()
    {
        $json = json_decode($this->getWxPay(), true);
        if ($json['BaseResponse']['Ret'] > 0) $this->sendMail(2);
        else $this->sendSunMail(2);
        $tradeAll = Paylist::where('status', 0)->where('datetime', '>', time())->orderBy('id', 'desc')->get();
        foreach ($tradeAll as $item) {
            $order = $this->WxComparison($json, $item->total, $item->datetime, $item->sys_sn);
            if ($order) static::AliPay_callback($item, $order);
        }
    }

    public function checkAliPay()
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->checkAliPayOne();
            if ($i != 5) sleep(10);
        }
        Paylist::where('status', 0)->where('datetime', '<', time())->delete();
    }

    public function checkWxPay()
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->checkWxPayOne();
            if ($i != 5) sleep(10);
        }
        Paylist::where('status', 0)->where('datetime', '<', time())->delete();
    }
}
