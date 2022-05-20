<?php
namespace App\Services\Gateway;

use App\Controllers\UserController;
use Omnipay\Omnipay;

class AlipayF2f
{
    public static function _name(): string
    {
        return 'alipay_f2f';
    }

    public static function _enable(): bool
    {
        if (empty($_ENV['active_payments']['alipay_f2f']) || $_ENV['active_payments']['alipay_f2f']['enable'] == false) {
            return false;
        }

        return true;
    }

    private function createGateway()
    {
        $configs = $_ENV['active_payments']['alipay_f2f'];
        $gateway = Omnipay::create('Alipay_AopF2F');
        $gateway->setSignType('RSA2');
        $gateway->setAppId($configs['f2f_pay_app_id']);
        $gateway->setPrivateKey($configs['f2f_pay_private_key']);
        $gateway->setAlipayPublicKey($configs['f2f_pay_public_key']);
        $notify_url = $_ENV['baseUrl'] . '/payments/notify/alipay_f2f';
        $gateway->setNotifyUrl($notify_url);
        return $gateway;
    }

    public static function createOrder($amount, $order_no, $user_id)
    {
        $configs = $_ENV['active_payments']['alipay_f2f'];

        try {
            $gateway = self::createGateway();
            $request = $gateway->purchase();
            $request->setBizContent([
                'subject' => $order_no,
                'out_trade_no' => $order_no,
                'total_amount' => $amount,
            ]);
            $aliResponse = $request->send();
            $qrcode = $aliResponse->getQrCode();

            return json_encode([
                'ret' => 1,
                'type' => 'qrcode',
                'qrcode' => $qrcode,
                'msg' => '使用支付宝扫描上方二维码支付',
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function notify($request, $response, $args)
    {
        $gateway = self::createGateway();
        $aliRequest = $gateway->completePurchase();
        $aliRequest->setParams($_POST);

        try {
            $aliResponse = $aliRequest->send();
            $order_no = $aliResponse->data('out_trade_no');

            if ($aliResponse->isPaid()) {
                UserController::execute($order_no);
                die('success');
            }
        } catch (\Exception $e) {
            die('fail');
        }
    }
}
