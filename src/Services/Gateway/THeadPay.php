<?php

namespace App\Services\Gateway;

use App\Services\Auth;
use App\Models\Paylist;
use App\Services\View;
use Exception;

class THeadPay extends AbstractPayment
{
    protected $sdk;

    public function __construct()
    {
        $this->sdk = new THeadPaySDK([
            'theadpay_url'      => $_ENV['theadpay_url'],
            'theadpay_mchid'    => $_ENV['theadpay_mchid'],
            'theadpay_key'      => $_ENV['theadpay_key'],
        ]);
    }


    public function purchase($request, $response, $args)
    {
        $amount = (int)$request->getParam('amount');
        $user = Auth::getUser();
        if ($amount <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '订单金额错误：' . $amount
            ]);
        }

        $pl = new Paylist();
        $pl->userid     = $user->id;
        $pl->tradeno    = self::generateGuid();
        $pl->total      = $amount;
        $pl->save();

        try {
            $res = $this->sdk->pay([
                'trade_no'      => $pl->tradeno,
                'total_fee'     => $pl->total*100,
                'notify_url'    => rtrim($_ENV['baseUrl'], '/') . '/payment/notify',
            ]);

            return $response->withJson([
                'ret'       => 1,
                'qrcode'    => $res['data'],
                'amount'    => $pl->total,
                'pid'       => $pl->tradeno,
            ]);
        } catch (Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '创建支付订单错误：' . $e->getMessage(),
            ]);
        }
    }

    public function notify($request, $response, $args)
    {
        $inputString = file_get_contents('php://input', 'r');
        $inputStripped = str_replace(array("\r", "\n", "\t", "\v"), '', $inputString);
        $params = json_decode($inputStripped, true); //convert JSON into array

        if ($this->sdk->verify($params)) {
            $pid = $params['out_trade_no'];
            $this->postPayment($pid, 'THeadPay 平头哥支付 ' . $pid);
            die('success'); //The response should be 'success' only
        }

        die('fail');
    }


    public function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/theadpay.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
        return 0;
    }

    public function getStatus($request, $response, $args)
    {
        $p = Paylist::where('tradeno', $_POST['pid'])->first();
        return $response->withJson([
            'ret'       => 1,
            'result'    => $p->status,
        ]);
    }
}
