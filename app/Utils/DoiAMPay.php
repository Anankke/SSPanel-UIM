<?php

namespace App\Utils;
/**
 * Made By Leo
 * 黛米付接口
 * 2.1.4版本
 * 适用于 V1 API  黛米付面板V1
 * copyright all rights reserved
 */

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\User;
use App\Models\Code;
use App\Models\Paylist;
use App\Models\Payback;

class DoiAMPay
{


    public static function render()
    {
        return View::getSmarty()->assign('enabled', Config::get('doiampay')['enabled'])->fetch('user/doiam.tpl');
    }

    public function handle($request, $response, $args)
    {
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        if (Config::get('doiampay')['enabled'][$type] == 0) {
            return json_encode(['errcode' => -1, 'errmsg' => '非法的支付方式.']);
        }
        if ($price <= 0) {
            return json_encode(['errcode' => -1, 'errmsg' => '非法的金额.']);
        }
        $user = Auth::getUser();
        $settings = Config::get('doiampay')['mchdata'][$type];
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->save();
        $data = [
            'trade' => $pl->id,
            'price' => $price,
            'phone' => $settings['phone'],
            'mchid' => $settings['mchid'],
            'subject' => Config::get('appName') . '充值' . $price . '元',
            'body' => Config::get('appName') . '充值' . $price . '元',
            'type' => 'Mod',
        ];
        $data = DoiAM::sign($data, $settings['token']);
        $ret = DoiAM::post('https://api.daimiyun.cn/v2/' . $type . '/create', $data);
        $result = json_decode($ret, true);
        if ($result and $result['errcode'] == 0) {
            $result['pid'] = $pl->id;
            return json_Encode($result);
        }

        return json_encode([
            'errcode' => -1,
            'errmsg' => '接口调用失败!' . $ret,
        ]);
    }

    public function status($request, $response, $args)
    {
        return json_encode(Paylist::find($_POST['pid']));
    }

    public function handle_return($request, $response, $args)
    {
        $money = $_GET['money'];
        echo "您已经成功支付 $money 元,正在跳转..";
        echo <<<HTML
<script>
    location.href="/user/code";
</script>
HTML;
    }

    public function handle_callback($request, $response, $args)
    {
        $order_data = $_POST;
        $status = $order_data['status'];         //获取传递过来的交易状态
        $invoiceid = $order_data['out_trade_no'];     //订单号
        $transid = $order_data['trade_no'];       //转账交易号
        $amount = $order_data['money'];          //获取递过来的总价格
        if (!DoiAM::checksign($_POST, Config::get('doiampay')['mchdata'][$args['type']]['token'])) {
            return json_encode(array('errcode' => 2333));
        }
        if ($status == 'success') {
            $p = Paylist::find($invoiceid);
            if ($p->status == 1) {
                return json_encode(['errcode' => 0]);
            }
            $p->status = 1;
            $p->save();
            $user = User::find($p->userid);
            $user->money += $p->total;
            $user->save();
            $codeq = new Code();
            $codeq->code = ['wepay' => '微信', 'qqpay' => 'QQ支付', 'alipay' => '支付宝'][$args['type']] . '充值';
            $codeq->isused = 1;
            $codeq->type = -1;
            $codeq->number = $p->total;
            $codeq->usedatetime = date('Y-m-d H:i:s');
            $codeq->userid = $user->id;
            $codeq->save();
            if ($user->ref_by != '' && $user->ref_by != 0 && $user->ref_by != null) {
                $gift_user = User::where('id', '=', $user->ref_by)->first();
                $gift_user->money += ($codeq->number * (Config::get('code_payback') / 100));
                $gift_user->save();
                $Payback = new Payback();
                $Payback->total = $codeq->number;
                $Payback->userid = $user->id;
                $Payback->ref_by = $user->ref_by;
                $Payback->ref_get = $codeq->number * (Config::get('code_payback') / 100);
                $Payback->datetime = time();
                $Payback->save();
            }
            return json_encode(['errcode' => 0]);
        }

        return '';
    }
}

class DoiAM
{
    public static function sort(&$array)
    {
        ksort($array);
    }

    public static function getsign($array, $key)
    {
        unset($array['sign']);
        self::sort($array);
        $sss = http_build_query($array);
        $sign = hash('sha256', $sss . $key);
        $sign = sha1($sign . hash('sha256', $key));
        return $sign;
    }

    public static function sign($array, $key)
    {
        $array['sign'] = self::getSign($array, $key);
        return $array;
    }

    public static function checksign($array, $key)
    {
        $new = $array;
        $new = self::sign($new, $key);
        if (!isset($array['sign'])) {
            return false;
        }
        return $array['sign'] == $new['sign'];
    }

    public static function post($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
