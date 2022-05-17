<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Setting;
use App\Services\Auth;
use App\Services\View;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class PAYJS extends AbstractPayment
{
    private $appSecret;
    private $gatewayUri;
    /**
     * 签名初始化
     *
     * @param merKey    签名密钥
     */
    public function __construct()
    {
        $this->appSecret = Setting::obtain('payjs_key');
        $this->gatewayUri = Setting::obtain('payjs_url');
    }
    public static function _name(): string
    {
        return 'payjs';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('payjs');
    }

    public static function _readableName(): string
    {
        return 'PAYJS';
    }
    /**
     * @name    准备签名/验签字符串
     */
    public function prepareSign($data)
    {
        $data['mchid'] = Setting::obtain('payjs_mchid');
        $data = array_filter($data);
        ksort($data);
        return http_build_query($data);
    }
    /**
     * @name    生成签名
     *
     * @param sourceData
     */
    public function sign($data): string
    {
        return strtoupper(md5(urldecode($data) . '&key=' . $this->appSecret));
    }
    /*
     * @name    验证签名
     * @param   signData 签名数据
     * @param   sourceData 原数据
     * @return
     */
    public function verify($data, $signature)
    {
        $mySign = $this->sign($data);
        return $mySign === $signature;
    }
    public function post($data, $type = 'pay')
    {
        if ($type === 'pay') {
            $this->gatewayUri .= 'cashier';
        } elseif ($type === 'refund') {
            $this->gatewayUri .= 'refund';
        } else {
            $this->gatewayUri .= 'check';
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->gatewayUri);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
    public function purchase(Request $request, Response $response, array $args): ResponseInterface
    {
        $price = $request->getParam('price');
        if ($price <= 0) {
            return json_encode(['code' => -1, 'errmsg' => '非法的金额.']);
        }
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        //if ($type != 'alipay') {
        //$type = '';
        //}
        $data = [];
        $data['mchid'] = Setting::obtain('payjs_mchid');
        //$data['type'] = $type;
        $data['out_trade_no'] = $pl->tradeno;
        $data['total_fee'] = (float) $price * 100;
        $data['notify_url'] = self::getCallbackUrl();
        //$data['callback_url'] = $_ENV['baseUrl'] . '/user/code';
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        //$url = 'https://payjs.cn/api/cashier?' . http_build_query($data);
        $url = Setting::obtain('payjs_url') . '/cashier?' . http_build_query($data);
        return $response->withJson(['code' => 0, 'url' => $url, 'pid' => $data['out_trade_no']]);
        //$result = json_decode($this->post($data), true);
        //$result['pid'] = $pl->tradeno;
        //return json_encode($result);
    }
    public function query($tradeNo)
    {
        $data = [];
        $data['payjs_order_id'] = $tradeNo;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        return json_decode($this->post($data, $type = 'query'), true);
    }
    public function notify($request, $response, $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $return_code = $data['return_code'] ?? 0;

        if ($return_code === 1) {
            // 验证签名
            // $in_sign = $data['sign'];
            unset($data['sign']);
            $data = array_filter($data);
            ksort($data);
            $sign = strtoupper(md5(urldecode(http_build_query($data) . '&key=' . $this->appSecret)));
            $resultVerify = $sign ? true : false;

            if ($resultVerify) {
                // 验重
                $p = Paylist::where('tradeno', '=', $data['out_trade_no'])->first();
                if ($p->status !== 1) {
                    $this->postPayment($data['out_trade_no'], 'PAYJS ' . $data['out_trade_no']);
                    return $response->write('SUCCESS');
                }
                return $response->write('ERROR');
            }
            return $response->write('FAIL2');
        }

        return $response->write('FAIL1');
    }

    public function refund($merchantTradeNo)
    {
        $data = [];
        $data['payjs_order_id'] = $merchantTradeNo;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        return $this->post($data, 'refund');
    }

    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('user/payjs.tpl');
    }

    public function getReturnHTML($request, $response, $args): ResponseInterface
    {
        $pid = (int) $_GET['merchantTradeNo'];
        $p = Paylist::where('tradeno', '=', $pid)->first();
        $money = $p->total;
        if ($p->status === 1) {
            $success = 1;
        } else {
            $data = $request->getParsedBody();

            // $in_sign = $data['sign'];
            unset($data['sign']);
            $data = array_filter($data);
            ksort($data);
            $sign = strtoupper(md5(urldecode(http_build_query($data) . '&key=' . $this->appSecret)));
            $resultVerify = $sign ? true : false;

            if ($resultVerify) {
                $this->postPayment($data['out_trade_no'], 'PAYJS ' . $data['out_trade_no']);
                $success = 1;
            } else {
                $success = 0;
            }
        }
        return View::getSmarty()->assign('money', $money)->assign('success', $success)->fetch('user/pay_success.tpl');
    }

    public function getStatus($request, $response, $args): ResponseInterface
    {
        $return = [];
        $p = Paylist::where('tradeno', $request->getParam('pid'))->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return $response->withJson($return);
    }
}
