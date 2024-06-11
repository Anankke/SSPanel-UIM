<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use Alipay\OpenAPISDK\Api\AlipayTradeApi;
use Alipay\OpenAPISDK\ApiException;
use Alipay\OpenAPISDK\Model\AlipayTradePrecreateModel;
use Alipay\OpenAPISDK\Model\AlipayTradeQueryModel;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\AlipayLogger;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use App\Models\Config;
use App\Models\Invoice;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\View;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;

final class AlipayF2F extends Base
{
    private AlipayConfig $alipayConfig;

    public function __construct()
    {
        $this->antiXss = new AntiXSS();
        AlipayLogger::setNeedEnableLogger(false);
        $this->alipayConfig = new AlipayConfig();
        $this->alipayConfig->setAppid(Config::obtain('f2f_pay_app_id'));
        $this->alipayConfig->setPrivateKey(Config::obtain('f2f_pay_private_key'));
        $this->alipayConfig->setAlipayPublicKey(Config::obtain('f2f_pay_public_key'));
    }

    public static function _name(): string
    {
        return 'f2f';
    }

    public static function _readableName(): string
    {
        return 'Alipay F2F';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('f2f');
    }

    /**
     * @throws Exception
     */
    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/f2f.tpl');
    }

    /**
     * @throws ApiException
     */
    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $invoice_id = $this->antiXss->xss_clean($request->getParam('invoice_id'));
        $invoice = (new Invoice)->find($invoice_id);

        if ($invoice === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Invoice not found',
            ]);
        }

        $price = $invoice->price;
        $trade_no = self::generateGuid();

        if ($price <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法的金额',
            ]);
        }

        $user = Auth::getUser();
        $paylist = new Paylist();
        $paylist->userid = $user->id;
        $paylist->total = $price;
        $paylist->invoice_id = $invoice_id;
        $paylist->tradeno = $trade_no;
        $paylist->gateway = self::_readableName();
        $paylist->save();

        $f2f_pay_notify_url = Config::obtain('f2f_pay_notify_url');

        if ($f2f_pay_notify_url === '') {
            $notifyUrl = self::getCallbackUrl();
        } else {
            $notifyUrl = $f2f_pay_notify_url;
        }

        $api = $this->createApi();
        $aliRequest = new AlipayTradePrecreateModel();
        $aliRequest->setOutTradeNo($trade_no);
        $aliRequest->setTotalAmount($price);
        $aliRequest->setSubject($trade_no);
        $aliRequest->setNotifyUrl($notifyUrl);

        $aliResponse = $api->precreate($aliRequest);
        // 获取收款二维码内容
        $qrCode = $aliResponse->getQrCode();

        return $response->withJson([
            'ret' => 1,
            'qrcode' => $qrCode,
        ]);
    }

    /**
     * @throws ApiException
     */
    public function notify($request, $response, $args): ResponseInterface
    {
        $api = $this->createApi();

        $aliRequest = new AlipayTradeQueryModel();
        $aliRequest->setOutTradeNo($_POST['out_trade_no']);
        $aliResponse = $api->query($aliRequest);

        if ($aliResponse->getTradeStatus() === 'TRADE_SUCCESS') {
            $this->postPayment($aliResponse->getOutTradeNo());
            // https://opendocs.alipay.com/open/194/103296#%E5%BC%82%E6%AD%A5%E9%80%9A%E7%9F%A5%E7%89%B9%E6%80%A7
            return $response->write('success');
        }

        return $response->write('failed');
    }

    private function createApi(): AlipayTradeApi
    {
        $alipayTradeApi = new AlipayTradeApi(new Client());
        $alipayConfigUtil = new AlipayConfigUtil($this->alipayConfig);
        $alipayTradeApi->setAlipayConfigUtil($alipayConfigUtil);

        return $alipayTradeApi;
    }
}
