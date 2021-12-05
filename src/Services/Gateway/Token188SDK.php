<?php
namespace App\Services\Gateway;

class Token188SDK
{
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function pay($order)
    {
        $params = [
            'merchantId'  => $this->config['token188_mchid'],
            'outTradeNo'  => $order['trade_no'],
            'subject'     => $order['trade_no'],
            'totalAmount' => (string) $order['total_fee'],
            'attach'      => (string) $order['total_fee'],
            'body'        => $order['trade_no'],
            'coinName'    => 'USDT-TRC20',
            'notifyUrl'   => $order['notify_url'],
            'timestamp'   => $this->msectime(),
            'nonceStr'    => $this->getNonceStr(16)
        ];

        //echo $params['totalAmount'];
        $mysign = self::GetSign($this->config['token188_key'], $params);

        // 网关连接
        $ret_raw = self::_curlPost($this->config['token188_url'], $params, $mysign, 1);
        $ret = @json_decode($ret_raw, true);

        if (empty($ret['data'])) {
            throw new \Exception($ret['msg']);
        }

        if ($ret['data']['paymentUrl']=='') {
            throw new \Exception('网络连接异常: 无法连接支付网关');
        }

        return $ret['data']['paymentUrl'];
    }

    public function verify($params)
    {
        $sign = $params['sign'];
        unset($params['sign']);
        unset($params['notifyId']);
        $_sign = self::GetSign($this->config['token188_key'], $params);

        // check sign
        if ($_sign !== $sign) {
            echo json_encode(['status' => 400]);
            return false;
        } else {
            return true;
        }
    }

    public function GetSign($secret, $params)
    {
        $p = ksort($params);
        reset($params);

        if ($p) {
            $str = '';
            foreach ($params as $k => $val) {
                $str .= $k . '=' .  $val . '&';
            }
            $strs = rtrim($str, '&');
        }

        $strs .='&key='.$secret;
        $signature = md5($strs);
        //$params['sign'] = base64_encode($signature);

        return $signature;
    }

    public function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";

        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    private function _curlPost($url,$params=false,$signature,$ispost=0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        //设置超时
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array('token:'.$signature)
        );
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
