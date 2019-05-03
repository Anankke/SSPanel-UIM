<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Author:
 * Date: 2018/9/24
 * Time: 下午4:01
 */

/**
 * Made By Leo
 * 黛米付接口
 * 2.1.4版本
 * 适用于 V1 API  黛米付面板V1
 * copyright all rights reserved
 */

namespace App\Services\Gateway;

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;

use App\Models\Paylist;


class DoiAMPay extends AbstractPayment
{
    public function getPurchaseHTML()
    {
        return View::getSmarty()->assign("enabled", Config::get("doiampay")['enabled'])->fetch("user/doiam.tpl");
    }

    public function notify($request, $response, $args)
    {
        $order_data = $_POST;
        $status = $order_data['status'];         //获取传递过来的交易状态
        $invoiceid = $order_data['out_trade_no'];     //订单号
        $transid = $order_data['trade_no'];       //转账交易号
        $amount = $order_data['money'];          //获取递过来的总价格
        if (!DoiAM::checksign($_POST, Config::get("doiampay")['mchdata'][$args['type']]['token'])) {
            return (json_encode(array('errcode' => 2333)));
        }
        if ($status == 'success') {
            self::postPayment($invoiceid, "黛米付" . ['wepay' => "(微信)", 'qqpay' => '(QQ支付)', 'alipay' => "(支付宝)"][$args['type']]);
            return json_encode(['errcode' => 0]);
        } else {
            return 1;
        }
    }

    public function purchase($request, $response, $args)
    {
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        if (Config::get("doiampay")['enabled'][$type] == 0) {
            return json_encode(['errcode' => -1, 'errmsg' => "非法的支付方式."]);
        }
        if ($price <= 0) {
            return json_encode(['errcode' => -1, 'errmsg' => "非法的金额."]);
        }
        $user = Auth::getUser();
        $settings = Config::get("doiampay")['mchdata'][$type];
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        $data = [
            'trade' => $pl->tradeno,
            'price' => $price,
            'phone' => $settings['phone'],
            'mchid' => $settings['mchid'],
            'subject' => Config::get("appName") . "充值" . $price . "元",
            'body' => Config::get("appName") . "充值" . $price . "元",
            'type' => 'Mod',
        ];
        $data = DoiAM::sign($data, $settings['token']);
        $ret = DoiAM::post("https://api.daimiyun.cn/v2/" . $type . "/create", $data);
        $result = json_decode($ret, true);
        if ($result and $result['errcode'] == 0) {
            $result['pid'] = $pl->tradeno;
            return json_encode($result);
        } else {
            return json_encode([
                'errcode' => -1,
                'errmsg' => "接口调用失败!" . $ret,
            ]);
        }
        return json_encode($result);
    }


    public function getReturnHTML($request, $response, $args)
    {
        $money = $_GET['money'];
        echo "您已经成功支付 $money 元,正在跳转..";
        echo <<<HTML
<script>
    location.href="/user/code";
</script>
HTML;
        return;
    }

    public function getStatus($request, $response, $args)
    {
        $p = Paylist::where("tradeno", $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return json_encode($return);
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
        $sign = hash("sha256", $sss . $key);
        $sign = sha1($sign . hash("sha256", $key));
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
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