<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/28
 * Time: 9:08 PM
 */

namespace App\Services\Gateway;

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;

class SfPay extends AbstractPayment
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
        $this->sfgatewayUri = 'https://p.spay.xin/';
    }


    /**
     * @name    准备签名/验签字符串
     */
    public function prepareSign($data)
    {
      
		$md5str = "";
        foreach ($data as $key => $val) {
        $md5str = $md5str . $key . "=" . $val . "&";
      
         }
         
        return $md5str;
    }

    /**
     * @name    生成签名
     * @param sourceData
     * @return    签名数据
     */
    public function sign($data)
    {
      
       
      return strtoupper(md5($data . "key=" . $this->appSecret));
   
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
        if ($type == 'pay') {
            $this->sfgatewayUri .= 'pay/go';
        } elseif ($type == 'refund') {
            $this->sfgatewayUri .= 'refund/go';
        } elseif ($type == 'pre') {
            $this->sfgatewayUri .= 'pay/pre';
        } else {
            $this->sfgatewayUri .= 'query/go';
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->sfgatewayUri);
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
        $price = $request->getParam('price');
        $type = $request->getParam('type');


        if ($price <= 0) {
            return json_encode(['code' => -1, 'errmsg' => '非法的金额.']);
        }
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        

        $data['pay_memberid'] = Config::get('sf_appid');
        $data['pay_orderid'] = $pl->tradeno;
        $data['pay_amount'] = (float)$price;
        $data['pay_applydate'] = date("Y-m-d H:i:s");  //订单时间
        $data['pay_bankcode'] = $type;
        $data['pay_notifyurl'] = Config::get('baseUrl') . '/payment/notify';
        $data['pay_callbackurl'] = Config::get('baseUrl') . '/user/payment/return';
        ksort($data);
        $params = $this->prepareSign($data);
      
        $data['pay_md5sign'] = $this->sign($params);
        $data['pay_attach'] = $type;
        $data['pay_productname'] ='Span';

    
        $this->setHtml(Config::get('sfgatewayUri'),$data);
        
    }
   public function setHtml($tjurl, $arraystr)
        {
            $str = '<form id="Form1" name="Form1" method="post" action="' . $tjurl . '">';
            foreach ($arraystr as $key => $val) {
                $str .= '<input type="hidden" name="' . $key . '" value="' . $val . '">';
            }
            $str .= '</form>';
            $str .= '<script>';
            $str .= 'document.Form1.submit();';
            $str .= '</script>';
         
            exit($str);
        }
  public function query($tradeNo)
    {
        $data['appId'] = Config::get('sf_appid');
        $data['merchantTradeNo'] = $tradeNo;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        return json_decode($this->post($data, $type = 'query'), true);
    }

  public function notify($request, $response, $args)
    {
      
      
   
    $returnArray = array( // 返回字段
            "memberid" => $_REQUEST["memberid"], // 商户ID
            "orderid" =>  $_REQUEST["orderid"], // 订单号
            "amount" =>  $_REQUEST["amount"], // 交易金额
            "datetime" =>  $_REQUEST["datetime"], // 交易时间
            "transaction_id" =>  $_REQUEST["transaction_id"], // 支付流水号
            "returncode" => $_REQUEST["returncode"],
        );
       
        ksort($returnArray);
        reset($returnArray);
        $md5str =  $this->prepareSign($returnArray);
        $sign = strtoupper(md5($md5str . "key=" . $this->appSecret));
       
        if ($sign == $_REQUEST["sign"]) {
            if ($_REQUEST["returncode"] == "00") {
                    $this->postPayment($returnArray['orderid'], 'SfPay');
                    exit("OK");
            }
        }
    
   
    }
    public function refund($merchantTradeNo)
    {
        $data['appId'] = Config::get('sf_appid');
        $data['merchantTradeNo'] = $merchantTradeNo;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);

        return $this->post($data, 'refund');
    }


    public function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/sfpay.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
      
        $pid = $_REQUEST['orderid'];
     
        $p = Paylist::where('tradeno', '=', $pid)->first();
        $money = $p->total;
        if ($p->status == 1) {
            $success = 1;
        } else {
             $success = 0;
           
        }
        return View::getSmarty()->assign('money', $money)->assign('success', $success)->fetch('user/pay_success.tpl');
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
