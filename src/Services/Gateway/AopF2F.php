<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Setting;
use App\Services\Auth;
use App\Services\View;
use Exception;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;

final class AopF2F extends AbstractPayment
{
    public static function _name(): string
    {
        return 'f2f';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('f2f');
    }

    public static function _readableName(): string
    {
        return 'Alipay F2F';
    }

    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();

        $price = $antiXss->xss_clean($request->getParam('amount'));
        $invoice_id = $antiXss->xss_clean($request->getParam('invoice_id'));

        if ($price <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法的金额',
            ]);
        }

        $user = Auth::getUser();
        $pl = new Paylist();

        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->invoice_id = $invoice_id;
        $pl->tradeno = self::generateGuid();
        $pl->gateway = self::_readableName();

        $pl->save();

        $gateway = $this->createGateway();

        $request = $gateway->purchase();
        $request->setBizContent([
            'subject' => $pl->tradeno,
            'out_trade_no' => $pl->tradeno,
            'total_amount' => $pl->total,
        ]);

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

    /**
     * @throws InvalidRequestException
     */
    public function notify($request, $response, $args): ResponseInterface
    {
        $gateway = $this->createGateway();
        $aliRequest = $gateway->completePurchase();
        $aliRequest->setParams($_POST);
        $aliResponse = $aliRequest->send();
        $pid = $aliResponse->data('out_trade_no');

        if ($aliResponse->isPaid()) {
            $this->postPayment($pid);
            return $response->withJson([
                'ret' => 1,
                'msg' => '支付成功',
            ]);
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => '支付失败',
        ]);
    }

    /**
     * @throws Exception
     */
    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/f2f.tpl');
    }

    private function createGateway(): GatewayInterface
    {
        $configs = Setting::getClass('f2f');
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
