<?php
/**
 * @author Cian topjohncian@gmail.com
 * date 2019-08-20
 */


namespace App\Services\Gateway;


use App\Models\Paylist;
use App\Services\Auth;
use App\Services\Config;
use App\Services\View;

/**
 * Class YyhyoPay
 * @package App\Services\Gateway
 * @author Cian topjohncian@gmail.com
 * @author 烟雨寒云 admin@yyhy.me
 */
class YyhyoPay extends AbstractPayment
{
    /**
     * 商户ID
     * @var string
     */
    private $pid;

    /**
     * 商户KEY
     * @var string
     */
    private $key;

    /**
     * 支付接口地址
     * @var string
     */
    private $apiUrl;

    /**
     * 配置基础信息
     * YyhyoPay constructor.
     */
    public function __construct()
    {
        $this->pid = Config::get('yyhyopay_pid');
        $this->key = Config::get('yyhyopay_key');
        $this->apiUrl = 'http://pay.yyhyo.com';
    }

    /**
     * 发起支付
     * @param $request
     * @param $response
     * @param $args
     * @return false|string
     */
    public function purchase($request, $response, $args)
    {
        $price = $request->getParam('price');
        $type = $request->getParam('type');

        if ($price <= 0) {
            return json_encode(['code' => -1, 'errmsg' => '非法的金额.']);
        }

        if ($type != 'wxpay' && $type != 'alipay' && $type != 'qqpay') {
            return json_encode(['code' => -1, 'errmsg' => '非法的支付方式.']);
        }

        $out_trade_no = self::generateTradeNo();

        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = $out_trade_no;
        $pl->save();

        $notify_url = Config::get('baseUrl') . '/payment/yyhyo/notify';
        $return_url = Config::get('baseUrl') . '/user/payment/return';
        $name = Config::get('appName') . '充值' . $price . '元';
        $sitename = Config::get('appName');

        $url = $this->submit($type, $out_trade_no, $notify_url, $return_url, $name, $price, $sitename);

        $result = ['code' => 0, 'url' => $url,'pid'=>$out_trade_no];

        return json_encode($result);
    }

    /**
     * 生成19位订单号
     * @return string
     */
    static private function generateTradeNo(): string
    {
        $time = date('YmdHis', time());
        $rand = (string)mt_rand(10000, 99999);
        return $time . $rand;
    }

    /**
     * @Note  支付发起
     * @param $type string 支付方式
     * @param $out_trade_no string 订单号
     * @param $notify_url string 异步通知地址
     * @param $return_url string 回调通知地址
     * @param $name string 商品名称
     * @param $money string 金额
     * @param $sitename string 站点名称
     * @return string
     */
    public function submit($type, $out_trade_no, $notify_url, $return_url, $name, $money, $sitename): string
    {
        $data = [
            'pid'          => $this->pid,
            'type'         => $type,
            'out_trade_no' => $out_trade_no,
            'notify_url'   => $notify_url,
            'return_url'   => $return_url,
            'name'         => $name,
            'money'        => $money,
            'sitename'     => $sitename,
        ];
        $string = http_build_query($data);
        $sign = $this->getsign($data);
        return $this->apiUrl . '/submit.html?' . $string . '&sign=' . $sign . '&sign_type=MD5';
    }

    /**
     * @Note 生成签名
     * @param $data array 参与签名的参数
     * @return string
     */
    private function getSign($data): string
    {
        $data = array_filter($data);
        ksort($data);
        $str1 = '';
        foreach ($data as $k => $v) {
            $str1 .= '&' . $k . "=" . $v;
        }
        $str = $str1 . $this->key;
        $str = trim($str, '&');
        $sign = md5($str);
        return $sign;
    }

    /**
     * 支付通知
     * @param $request
     * @param $response
     * @param $args
     */
    public function notify($request, $response, $args)
    {
        $data = [
            'pid'          => $request->getParam('pid'),
            'trade_no'     => $request->getParam('trade_no'),
            'out_trade_no' => $request->getParam('out_trade_no'),
            'type'         => $request->getParam('type'),
            'name'         => $request->getParam('name'),
            'money'        => $request->getParam('money'),
            'trade_status' => $request->getParam('trade_status'),
            'sign'         => $request->getParam('sign'),
            'sign_type'    => $request->getParam('sign_type'),
        ];

        if ($this->verify($data)) {
            if ($data['trade_status'] = 'TRADE_SUCCESS') {
                $this->postPayment($data['out_trade_no'], '烟雨云支付');
                echo 'success';
            }
        } else {
            echo 'fail';
        }
    }

    /**
     * @Note 验证签名
     * @param $data array 待验证参数
     * @return bool
     */
    public function verify($data): bool
    {
        if (!isset($data['sign']) || !$data['sign']) {
            return false;
        }
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['sign_type']);
        if (get_magic_quotes_gpc()) {
            $data = stripslashes($data);
        }
        $sign2 = $this->getSign($data);
        if ($sign != $sign2) {
            //兼容傻逼彩虹易支付
            unset($data['_input_charset']);
            $sign2 = $this->getSign($data);
            if ($sign == $sign2) return true;
            return false;
        }
        return true;
    }

    /**
     * 获取支付HTML
     */
    public function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/yyhyopay.tpl');
    }

    /**
     * 获取成功HTML
     * @param $request
     * @param $response
     * @param $args
     * @return
     */
    public function getReturnHTML($request, $response, $args)
    {
        $out_trade_no = $request->getParam('out_trade_no');
        $payList = Paylist::where('tradeno', '=', $out_trade_no)->first();
        $money = $payList->total;

        if ($payList->status == 1) {
            $success = 1;
        } else {
            $data = [
                'pid'          => $request->getParam('pid'),
                'trade_no'     => $request->getParam('trade_no'),
                'out_trade_no' => $request->getParam('out_trade_no'),
                'type'         => $request->getParam('type'),
                'name'         => $request->getParam('name'),
                'money'        => $request->getParam('money'),
                'trade_status' => $request->getParam('trade_status'),
                'sign'         => $request->getParam('sign'),
                'sign_type'    => $request->getParam('sign_type'),
            ];

            if ($this->verify($data)) {
                if ($data['trade_status'] = 'TRADE_SUCCESS') {
                    $this->postPayment($data['out_trade_no'], 'YyhyoPay');
                    echo 'success';
                    $success = 1;
                }
            } else {
                echo 'fail';
                $success = 0;
            }
        }

        return View::getSmarty()->assign('money', $money)->assign('success', $success)->fetch('user/pay_success.tpl');
    }

    /**
     * 获取支付状态
     * @param $request
     * @param $response
     * @param $args
     * @return false|string
     */
    public function getStatus($request, $response, $args)
    {
        $return = [];
        $p = Paylist::where('tradeno',$request->getParam('pid'))->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return json_encode($return);
    }


}
