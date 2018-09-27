<?php
/**
 * 支付宝检测监听 每分钟大概运行10次 间隔为5秒
 * cookie失效会通过email通知
 * 请务必设置好邮箱配置
 * User: chen yun.9in.info
 * Date: 9/12/18
 * Time: 12:33 PM
 */

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Services\Mail;
use App\Services\View;
use App\Services\Auth;

class ChenPay extends AbstractPayment
{
    private $config = [];
    private $listenSum = 5; // 每分钟运行次数
    private $listenInterval = 10; // 运行间隔

    /**
     * 初始化
     */
    public function __construct()
    {
        $data = [];
        foreach (\App\Models\Config::get() as $item) $data[$item->name] = $item->value;
        $this->config = $data;
    }

    /**
     * 获取config值
     * @param bool $name
     * @return array|mixed
     */
    public function getConfig($name = false)
    {
        if ($name) return $this->config[$name];
        else return $this->config;
    }

    /**
     * 获取cookie某key值
     * @param string $name
     * @param bool $cookie
     * @return mixed
     */
    public function getCookieName($name = 'uid', $cookie = false)
    {
        $cookie = explode($name . '=', $cookie ? $cookie : $this->getConfig('AliPay_Cookie'))[1];
        if ($name == 'uid') return explode('"', $cookie)[0];
        else return explode(';', $cookie)[0];
    }

    /**
     * 设置数据库中的config
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setConfig($name, $value)
    {
        \App\Models\Config::where('name', $name)->update(['value' => $value]);
        $this->config[$name] = $value;
        return $value;
    }

    /**
     * 注入HTML
     * @return mixed
     */
    public function getPurchaseHTML()
    {
        return View::getSmarty()->assign("config", $this->config)
            ->assign('QRcodeUrl', $this->config->getConfig('AliPay_QRcode'))
            ->assign('WxQRcodeUrl', $this->config->getConfig('WxPay_QRcode'))
            ->fetch("user/chenPay.tpl");
    }

    /**
     * 获取订单信息
     * @param $request
     * @param $response
     * @param $args
     * @return false|string
     */
    public function getStatus($request, $response, $args)
    {
        $id = $request->getParam('id');
        if (!$id) return json_encode(['ret' => 0, 'msg' => '请输入Id']);
        $order = Paylist::find($id);
        $order->ret = 1;
        return json_encode($order);
    }

    /**
     * 生成订单
     * @param $request
     * @param $response
     * @param $args
     * @return false|string
     */
    public function purchase($request, $response, $args)
    {
        $type = $request->getParam('type');
        $amount = $request->getParam('fee');
        $url = $request->getParam('url');
        if (!is_numeric($amount) || !is_numeric($type)) return json_encode(['ret' => 0, 'msg' => '请输入正确金额']);
        elseif ($amount <= 0) return json_encode(['ret' => 0, 'msg' => '请输入正确金额']);

        $xPosed = \App\Models\Config::where('name', 'Pay_Xposed')->first()->value;
        $user = Auth::getUser();
        if ($xPosed != 1 || Paylist::where('status', 0)->where('datetime', '>', time())->first()) {
            $newOrder = new Paylist();
            $newOrder->userid = $user->id;
            $newOrder->total = $amount;
            $newOrder->datetime = time() + 3 * 60; // 有效时间
            $newOrder->sys_sn = rand(100000, 999999) . $user->id . $newOrder->datetime;
            $newOrder->type = $type;
            if ($xPosed != 1) $newOrder->url = $url;
            $newOrder->save();
            $newOrder->ret = 1;
        } else $newOrder = ['msg' => '正在排队中，请稍后再试！', 'ret' => 0];
        return json_encode($newOrder);
    }

    /**
     * 手动关闭排队机制 & 删除订单
     * @param $request
     * @return false|string
     */
    public function orderDelete($request)
    {
        $id = $request->getParam('id');
        if (!$id) return json_encode(['ret' => 0, 'msg' => '请输入Id']);
        $user = Auth::getUser();
        return json_encode(['ret' => Paylist::where("id", $id)->where('status', 0)->where('userid', $user->id)->delete()]);
    }

    /**
     * xPosed 获取未生成QrCode订单列表
     * @return false|string
     */
    public function getList()
    {
        return json_encode(['data' => Paylist::where('status', 0)->where('url', null)->get()]);
    }

    /**
     * xPosed 从手机获取码设置url
     * @param $request
     * @return false|string
     */
    public function setOrder($request)
    {
        $sn = $request->getParam('sn');
        $url = $request->getParam('url');
        return json_encode(Paylist::where('sys_sn', $sn)->update(['url' => $url]));
    }

    /**
     * 后台支付配置
     * @return mixed
     */
    public function editConfig()
    {
        return View::getSmarty()->assign('payConfig', $this->config)->display('admin/payEdit.tpl');
    }

    /**
     * 后台保存配置
     * @param $request
     * @return false|string
     */
    public function saveConfig($request)
    {
        $this->setConfig('Notice_EMail', $request->getParam('Notice_EMail'));
        $this->setConfig('AliPay_QRcode', $request->getParam('AliPay_QRcode'));
        $this->setConfig('AliPay_Cookie', $request->getParam('AliPay_Cookie'));
        $this->setConfig('WxPay_QRcode', $request->getParam('WxPay_QRcode'));
        $this->setConfig('WxPay_Cookie', $request->getParam('WxPay_Cookie'));
        $this->setConfig('WxPay_Url', $request->getParam('WxPay_Url'));
        $this->setConfig('WxPay_SyncKey', '');
        $this->setConfig('Pay_Price', $request->getParam('Pay_Price'));
        $this->setConfig('AliPay_Status', $request->getParam('AliPay_Status'));
        $this->setConfig('WxPay_Status', $request->getParam('WxPay_Status'));
        return json_encode(['ret' => 1, 'msg' => '编辑成功！']);
    }

    /**
     * get alipay
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAliPay()
    {
        $html = (new \GuzzleHttp\Client())
            ->request('POST', "https://mbillexprod.alipay.com/enterprise/fundAccountDetail.json", ['headers' => [
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

    /**
     * 微信心跳包
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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

    /**
     * get wxpay
     * @return false|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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

    /**
     * 支付宝到账对比
     * @param $json
     * @param $fee
     * @param $time
     * @param $sn
     * @return bool
     */
    public function AliComparison($json, $fee, $time, $sn)
    {
        if (isset($json['result']['detail']) && is_array($json['result']['detail']))
            foreach ($json['result']['detail'] as $item)
                if ($item['signProduct'] == '转账收款码' && $item['accountType'] == '交易' &&
                    strtotime($item['tradeTime']) < $time && $item['tradeAmount'] == $fee) {
                    if (!Paylist::where('tradeno', $item['orderNo'])->first())
                        return $item['orderNo'];
                }
        return false;
    }

    /**
     * 微信到账对比
     * @param $json
     * @param $fee
     * @param $time
     * @param $sn
     * @return bool
     */
    public function WxComparison($json, $fee, $time, $sn)
    {
        if (isset($json['AddMsgList']) && is_array($json['AddMsgList']))
            foreach ($json['AddMsgList'] as $item)
                if (preg_match('/微信支付收款/', $item['FileName'])) {
                    $fees = explode('微信支付收款', $item['FileName']);
                    $fees = explode('元', $fees[1])[0];
                    if ($item['CreateTime'] < $time && $fees == $fee)
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
        return false;
    }

    /**
     * 发送错误email
     * @param int $type
     */
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

    /**
     * 发生成功email
     * @param int $type
     */
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

    /**
     * 检查支付宝
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkAliPayOne()
    {
        $json = json_decode($this->getAliPay(), true);
        if (!$json || isset($json['exception_marking']) || isset($json['target'])) $this->sendMail(1);
        else $this->sendSunMail(1);
        $tradeAll = Paylist::where('status', 0)->where('datetime', '>', time())->orderBy('id', 'desc')->get();
        foreach ($tradeAll as $item) {
            $order = $this->AliComparison($json, $item->total, $item->datetime, $item->sys_sn);
            if ($order) static::postPayment($item->id, 'chenPay支付' . $order);
        }
    }

    /**
     * 检查微信
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkWxPayOne()
    {
        $json = json_decode($this->getWxPay(), true);
        if ($json['BaseResponse']['Ret'] > 0) $this->sendMail(2);
        else $this->sendSunMail(2);
        $tradeAll = Paylist::where('status', 0)->where('datetime', '>', time())->orderBy('id', 'desc')->get();
        foreach ($tradeAll as $item) {
            $order = $this->WxComparison($json, $item->total, $item->datetime, $item->sys_sn);
            if ($order) static::postPayment($item->id, 'chenPay支付' . $order);
        }
    }

    /**
     * 监听支付宝
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function AliPayListen()
    {
        for ($i = 1; $i <= $this->listenSum; $i++) {
            $this->checkAliPayOne();
            if ($i != $this->listenSum) sleep($this->listenInterval);
        }
        Paylist::where('status', 0)->where('datetime', '<', time())->delete();
    }

    /**
     * 监听微信
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function WxPayListen()
    {
        for ($i = 1; $i <= $this->listenSum; $i++) {
            $this->checkWxPayOne();
            if ($i != $this->listenSum) sleep($this->listenInterval);
        }
        Paylist::where('status', 0)->where('datetime', '<', time())->delete();
    }

    public function getReturnHTML($request, $response, $args)
    {
    }

    public function notify($request, $response, $args)
    {
    }

    public function sign()
    {
    }

    public function setMethod($method)
    {
    }

    public function setNotifyUrl()
    {
    }

    public function setReturnUrl()
    {
    }

    public function init()
    {
    }
}
