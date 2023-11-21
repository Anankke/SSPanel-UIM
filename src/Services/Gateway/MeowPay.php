<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Config;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\View;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use voku\helper\AntiXSS;
use function json_decode;
use Slim\Http\ServerRequest;

final class MeowPay extends Base
{
    private $app_id;
    public function __construct()
    {
        $configs = Config::getClass('billing');
        $this->app_id = $configs['meowpay_app_id'];
    }

    public static function _name(): string
    {
        return 'meowpay';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('meowpay');
    }

    public static function _readableName(): string
    {
        return 'MeowPay';
    }

    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();
        $price = $antiXss->xss_clean($request->getParam('price'));
        $invoice_id = $antiXss->xss_clean($request->getParam('invoice_id'));
        $trade_no = uniqid();

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
        $pl->tradeno = $trade_no;
        $pl->invoice_id = $invoice_id;
        $pl->gateway = self::_readableName();
        $pl->save();
        $app_id = $this->app_id;
        $currency_type = "CNY";
        $amount = bcmul($price, "100", 0);
        $meowpay = new Payment($app_id, $trade_no, $currency_type, (int) $amount);
        $pay_link = $meowpay->get_pay_link();
        return $response->withRedirect($pay_link);
    }

    public function notify($request, $response, $args): ResponseInterface
    {
        $r = (object) $request->getParsedBody();
        $params = (object) $r->{'params'};
        $app_id = $params->{'app_id'};
        if ($app_id == $this->app_id) {
            $order_id = $params->{'trade_no'};
            $this->postPayment($order_id);
            return $response->withJson(['jsonrpc' => '2.0', 'id' => $r->id, 'result' => ['status' => 'Done']]);
        }
        return $response->withJson(['jsonrpc' => '2.0', 'id' => $r->id, 'error' => "fail"]);
    }

    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/meowpay.tpl');
    }
    protected static function getUserReturnUrl(): string
    {
        return $_ENV['baseUrl'] . '/user/payment/return/' . get_called_class()::_name();
    }
}

function post_request($url, $data)
{
    $headerArray = array("Content-Type: application/json", "charset='utf-8'", "Accept:application/json");
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYSTATUS, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

final class Payment
{
    var $url = "https://api.meowpay.org/json_rpc/";
    var $app_id;
    var $trade_no;
    var $amount;
    var $currency_type;
    var $return_url;
    var $notify_url;

    function __construct(
        string $app_id,
        string $trade_no,
        string $currency_type,
        int $amount,
        string $return_url = null,
        string $notify_url = null,
    ) {
        $this->app_id = $app_id;
        $this->trade_no = $trade_no;
        $this->amount = $amount;
        $this->currency_type = $currency_type;
        $this->return_url = $return_url;
        $this->notify_url = $notify_url;
    }

    function get_pay_link($url = null, $method = "create_payment")
    {
        if ($url === null) {
            $url = $this->url;
        }
        $js_rq_data = [];
        $js_rq_data['jsonrpc'] = '2.0';
        $js_rq_data['id'] = '0';
        $js_rq_data['method'] = $method;
        $js_rq_data['params']['app_id'] = $this->app_id;
        $js_rq_data['params']['trade_no'] = $this->trade_no;
        $js_rq_data['params']['amount'] = $this->amount;
        $js_rq_data['params']['currency_type'] = $this->currency_type;
        $js_rq_data['params']['return_url'] = $this->return_url;
        $js_rq_data['params']['notify_url'] = $this->notify_url;
        $rq = json_encode($js_rq_data, JSON_PARTIAL_OUTPUT_ON_ERROR);
        $response = post_request($url, $rq);
        return $response['result']['payment_info']['pay_link'];
    }
}

