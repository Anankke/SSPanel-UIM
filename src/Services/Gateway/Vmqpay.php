<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Setting;
use App\Services\Auth;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class Vmqpay extends AbstractPayment
{
    public static function _name(): string
    {
        return 'vmqpay';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('vmqpay');
    }

    public static function _readableName(): string
    {
        return 'Vmq 支付';
    }

    public function purchase(Request $request, Response $response, array $args): ResponseInterface
    {
        $trade_no = time();
        $user = Auth::getUser();
        $configs = Setting::getClass('vmq');

        $param = $user->id;
        $key = $configs['vmq_key'];
        $gateway = $configs['vmq_gateway'];
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        $sign = md5($trade_no.$param.$type.$price.$key);

        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = $trade_no;
        $pl->save();

        $params = [
            'payId' => $trade_no,
            'type' => $type,
            'price' => $price,
            'sign' => $sign,
            'param' => $param,
            'isHtml' => '1',
            'notifyUrl' => self::getCallbackUrl(),
            'returnUrl' => $_ENV['baseUrl'] . '/user/code',
        ];

        $pay_url = $gateway . '/createOrder?' . http_build_query($params);
        return $response->withRedirect($pay_url);
    }

    public function notify($request, $response, $args): ResponseInterface
    {
        $key = Setting::obtain('vmq_key');
        $payId = $request->getParam('payId');
        $param = $request->getParam('param');
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        $reallyPrice = $request->getParam('reallyPrice');
        $cloud_sign = $request->getParam('sign');

        $local_sign = md5($payId.$param.$type.$price.$reallyPrice.$key);

        if ($cloud_sign !== $local_sign) {
            die('error_sign');
        }

        $this->postPayment($payId, "在线支付 ${payId}");
        die('success');
    }

    public static function getPurchaseHTML(): string
    {
        return '
            <div class="card-inner">
            <p class="card-heading">请输入充值金额</p>
            <form class="vmqpay" name="vmqpay" action="/user/payment/purchase/vmqpay" method="get">
                <input class="form-control maxwidth-edit" id="price" name="price" placeholder="输入充值金额后，点击你要付款的应用图标即可" autofocus="autofocus" type="number" min="0.01" max="1000" step="0.01" required="required">
                <br>
                <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="2" ><img src="/images/alipay.jpg" width="50px" height="50px" /></button>
                <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="1" ><img src="/images/weixin.jpg" width="50px" height="50px" /></button>
            </form>
            </div>
        ';
    }
}
