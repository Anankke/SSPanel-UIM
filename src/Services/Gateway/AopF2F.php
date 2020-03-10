<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午9:24
 */

namespace App\Services\Gateway;

use App\Services\Auth;
use App\Models\Paylist;
use App\Services\View;
use Exception;
use Omnipay\Omnipay;

class AopF2F extends AbstractPayment
{
    private function createGateway()
    {
        $gateway = Omnipay::create('Alipay_AopF2F');
        $gateway->setSignType('RSA2'); //RSA/RSA2
        $gateway->setAppId($_ENV['f2fpay_app_id']);
        $gateway->setPrivateKey($_ENV['merchant_private_key']); // 可以是路径，也可以是密钥内容
        $gateway->setAlipayPublicKey($_ENV['alipay_public_key']); // 可以是路径，也可以是密钥内容
        $notifyUrl = $_ENV['f2fNotifyUrl'] ?? ($_ENV['baseUrl'] . '/payment/notify');
        $gateway->setNotifyUrl($notifyUrl);
        return $gateway;
    }


    public function purchase($request, $response, $args)
    {
        $amount = $request->getParam('amount');
        $user = Auth::getUser();
        if ($amount == '') {
            $res['ret'] = 0;
            $res['msg'] = '订单金额错误：' . $amount;
            return $response->getBody()->write(json_encode($res));
        }

        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->tradeno = self::generateGuid();
        $pl->total = $amount;
        $pl->save();

        $gateway = $this->createGateway();

        $request = $gateway->purchase();
        $request->setBizContent([
            'subject' => $pl->tradeno,
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

    public function notify($request, $response, $args)
    {
        $gateway = $this->createGateway();
        $aliRequest = $gateway->completePurchase();
        $aliRequest->setParams($_POST);

        try {
            /** @var \Omnipay\Alipay\Responses\AopCompletePurchaseResponse $response */
            $aliResponse = $aliRequest->send();
            $pid = $aliResponse->data('out_trade_no');
            if ($aliResponse->isPaid()) {
                $this->postPayment($pid, '支付宝当面付 ' . $pid);
                die('success'); //The response should be 'success' only
            }
        } catch (Exception $e) {
            die('fail');
        }
    }


    public function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/aopf2f.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
        return 0;
    }

    public function getStatus($request, $response, $args)
    {
        $p = Paylist::where('tradeno', $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return json_encode($return);
    }
}
