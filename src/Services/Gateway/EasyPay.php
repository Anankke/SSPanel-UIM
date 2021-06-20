<?php
namespace App\Services\Gateway;

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;

class EasyPay extends AbstractPayment
{

    private $appSecret;
    private $gatewayUri;

    /**
     * 签名初始化
     * @param merKey    签名密钥
     */

    public function __construct($appSecret)
    {
        $this->appSecret = $appSecret;
        $this->gatewayUri = 'https://api.crossz.pro/v1/gateway/fetch';
    }


    /**
     * @name    准备签名/验签字符串
     */
    public function prepareSign($data)
    {
        ksort($data);
        return http_build_query($data);
    }

    /**
     * @name    生成签名
     * @param sourceData
     * @return    签名数据
     */
    public function sign($data)
    {
        return strtolower(md5($data . $this->appSecret));
    }

    /*
     * @name    验证签名
     * @param   signData 签名数据
     * @param   sourceData 原数据
     * @return
     */
    public function verify($data, $signature)
    {
    	unset($data['sign']);
        $mySign = $this->sign($this->prepareSign($data));
        return $mySign === $signature;
    }

    public function post($data)
    {
        if (is_array($data))
        {
            $data = http_build_query($data, null, '&');
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


    public function purchase($request, $response, $args)
    {
        $price = $request->getParam('amount');
        if ($price <= 0) {
            return json_encode(['code' => -1, 'msg' => '非法的金额.']);
        }
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        $data['app_id'] = Config::get('easypay_app_id');
        $data['out_trade_no'] = $pl->tradeno;
        $data['total_amount'] = (int)($price * 100);
        $data['notify_url'] = Config::get('baseUrl') . '/payment/notify';
        $data['return_url'] = Config::get('baseUrl') . '/user/payment/return';
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
    	$result = json_decode($this->post($data), true);
    	if (!isset($result['data'])) {
    		return json_encode(['code' => -1, 'msg' => '支付网关处理失败']);
    	}
        $result['pid'] = $pl->tradeno;
        return json_encode(['url' => $result['data']['pay_url'], 'code' => 0, 'pid' => $pl->tradeno]);
    }

    public function notify($request, $response, $args)
    {
    	file_put_contents(BASE_PATH . '/storage/easypay.log', json_encode($request->getParams())."\r\n", FILE_APPEND);
    	if (!$this->verify($request->getParams(), $request->getParam('sign'))) {
    		die('FAIL');
    	}
    	$this->postPayment($request->getParam('out_trade_no'), 'EasyPay');
    	die('SUCCESS');
    }

    public function getPurchaseHTML()
    {
    	return View::getSmarty()->fetch('user/easypay.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
        header('Location:/user/code');
    }

    public function getStatus($request, $response, $args)
    {
        $return = [];
        $p = Paylist::where('tradeno', $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return json_encode($return);
    }
}