<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午9:24
 */

namespace App\Services\Gateway;

use App\Services\Auth;
use App\Services\Config;
use App\Models\Code;
use App\Models\Paylist;
use App\Services\View;
use Omnipay\Omnipay;

class AopF2F extends AbstractPayment
{
    private function createGateway(){
        $gateway = Omnipay::create('Alipay_AopF2F');
        $gateway->setSignType('RSA2'); //RSA/RSA2
        $gateway->setAppId(Config::get("f2fpay_app_id"));
        $gateway->setPrivateKey(Config::get("merchant_private_key")); // 可以是路径，也可以是密钥内容
        $gateway->setAlipayPublicKey(Config::get("alipay_public_key")); // 可以是路径，也可以是密钥内容
        $gateway->setNotifyUrl(Config::get("baseUrl")."/payment/notify");

        return $gateway;
    }


    function purchase($request, $response, $args)
    {
        $amount = $request->getParam('amount');
        $user = Auth::getUser();
        if ($amount == "") {
            $res['ret'] = 0;
            $res['msg'] = "订单金额错误：" . $amount;
            return $response->getBody()->write(json_encode($res));
        }

        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->tradeno = self::generateGuid();
        $pl->total = $amount;
        $pl->save();

        $gateway = self::createGateway();

        $request = $gateway->purchase();
        $request->setBizContent([
            'subject'      => "￥".$pl->total." - ".Config::get("appName")." - {$user->user_name}({$user->email})",
            'out_trade_no' => $pl->tradeno,
            'total_amount' => $pl->total
        ]);

        /** @var \Omnipay\Alipay\Responses\AopTradePreCreateResponse $response */
        $aliResponse = $request->send();

        // 获取收款二维码内容
        $qrCodeContent = $aliResponse->getQrCode();

        $return['ret'] = 1;
        $return['qrcode'] = $qrCodeContent;
        $return['amount'] = $pl->total;
        $return['pid'] = $pl->tradeno;

        return json_encode($return);
    }

    function notify($request, $response, $args)
    {
        $gateway = self::createGateway();
        $aliRequest = $gateway->completePurchase();
        $aliRequest->setParams($_POST);

        try {
            /** @var \Omnipay\Alipay\Responses\AopCompletePurchaseResponse $response */
            $aliResponse = $aliRequest->send();
            $pid = $aliResponse->data('out_trade_no');
            if($aliResponse->isPaid()){
                self::postPayment($pid, "支付宝当面付");
                die('success'); //The response should be 'success' only
            }
        } catch (\Exception $e) {
            die('fail');
        }
    }


    function getPurchaseHTML()
    {
        return View::getSmarty()->fetch("user/aopf2f.tpl");
    }

    function getReturnHTML($request, $response, $args)
    {
        return 0;
    }

    function getStatus($request, $response, $args)
    {
        $p = Paylist::where("tradeno", $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return json_encode($return);
    }
}