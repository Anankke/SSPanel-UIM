<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午9:24
 */

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Setting;
use App\Services\Auth;
use App\Services\View;
use Exception;
use Omnipay\Alipay\AbstractAopGateway;
use Omnipay\Alipay\AopF2FGateway;
use Omnipay\Omnipay;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class AopF2F extends AbstractPayment
{
    public static function _name(): string
    {
        return 'f2fpay';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('f2fpay');
    }

    public static function _readableName(): string
    {
        return '支付宝在线充值';
    }

    public function purchase(Request $request, Response $response, array $args): ResponseInterface
    {
        $amount = $request->getParam('amount');
        $user = Auth::getUser();
        if ($amount === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '订单金额错误：' . $amount,
            ]);
        }

        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->tradeno = self::generateGuid();
        $pl->total = $amount;
        $pl->save();

        $gateway = $this->createGateway();

        /** @var AopTradePreCreateRequest $request */
        $request = $gateway->purchase();
        $request->setBizContent([
            'subject' => $pl->tradeno,
            'out_trade_no' => $pl->tradeno,
            'total_amount' => $pl->total,
        ]);

        /** @var \Omnipay\Alipay\Responses\AopTradePreCreateResponse $aliResponse */
        $aliResponse = $request->send();

        // 获取收款二维码内容
        $qrCodeContent = $aliResponse->getQrCode();

        return $response->withJson([
            'ret' => 1,
            'qrcode' => $qrCodeContent,
            'amount' => $pl->total,
            'pid' => $pl->tradeno,
        ]);
    }

    public function notify($request, $response, $args): ResponseInterface
    {
        $gateway = $this->createGateway();
        /** @var AopCompletePurchaseRequest $aliRequest */
        $aliRequest = $gateway->completePurchase();
        $aliRequest->setParams($_POST);

        try {
            /** @var \Omnipay\Alipay\Responses\AopCompletePurchaseResponse $aliResponse */
            $aliResponse = $aliRequest->send();
            $pid = $aliResponse->data('out_trade_no');
            if ($aliResponse->isPaid()) {
                $this->postPayment($pid, '支付宝当面付 ' . $pid);
                return $response->write('success');
            }
        } catch (Exception $e) {
            return $response->write('fail');
        }
    }

    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('user/aopf2f.tpl');
    }

    private function createGateway(): AbstractAopGateway
    {
        $configs = Setting::getClass('f2f');
        /** @var AopF2FGateway $gateway */
        $gateway = Omnipay::create('Alipay_AopF2F');
        $gateway->setSignType('RSA2'); //RSA/RSA2
        $gateway->setAppId($configs['f2f_pay_app_id']);
        $gateway->setPrivateKey($configs['f2f_pay_private_key']); // 可以是路径，也可以是密钥内容
        $gateway->setAlipayPublicKey($configs['f2f_pay_public_key']); // 可以是路径，也可以是密钥内容
        if ($configs['f2f_pay_notify_url'] === '') {
            $notifyUrl = self::getCallbackUrl();
        } else {
            $notifyUrl = $configs['f2f_pay_notify_url'];
        }
        $gateway->setNotifyUrl($notifyUrl);
        return $gateway;
    }
}
