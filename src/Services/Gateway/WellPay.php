<?php
namespace App\Services\Gateway;

use App\Controllers\UserController;

class WellPay
{
    public static function _name(): string
    {
        return 'well_pay';
    }

    public static function _enable(): bool
    {
        if (empty($_ENV['active_payments']['well_pay']) || $_ENV['active_payments']['well_pay']['enable'] == false) {
            return false;
        }

        return true;
    }

    public static function postOrder($url, $data): array
    {
        if (is_array($data)) {
            $data = http_build_query($data, null, '&');
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $data = curl_exec($curl);
        curl_close($curl);
        return json_decode($data, true);
    }

    public function createOrder($amount, $order_no, $user_id)
    {
        $configs = $_ENV['active_payments']['well_pay'];

        try {
            if (!$configs['enable']) {
                throw new \Exception('此方式暂未启用');
            }
            if ($configs['visible_range']) {
                if ($user_id < $configs['visible_min_range'] || $user_id > $configs['visible_max_range']) {
                    throw new \Exception('此方式暂未启用');
                }
            }
            if ($configs['min'] != false && $amount < $configs['min']) {
                throw new \Exception('账单金额低于支付方式限额');
            }
            if ($configs['max'] != false && $amount > $configs['max']) {
                throw new \Exception('账单金额高于支付方式限额');
            }

            $params = [
                'appid' => $configs['appid'],
                'orderno' => $order_no,
                'totalfee' => sprintf("%.2f", $amount),
                'notifyurl' => $_ENV['baseUrl'] . '/payments/notify/well_pay',
                'returnurl' => $_ENV['baseUrl'] . '/user/order/' . $order_no,
            ];

            $business = [
                'ip' => $_SERVER['REMOTE_ADDR'],
                'remark' => '',
                'version' => '1.01',
                'request_time' => time(),
            ];

            ksort($params);
            $sign = htmlspecialchars(http_build_query($params));
            $sign = strtolower(md5($sign . $configs['appkey']));
            $business['sign'] = $sign;
            $business['data'] = $params;
            $response = self::postOrder($configs['gateway'], $business);

            return json_encode([
                'ret' => 1,
                'type' => 'link',
                'link' => $response['data']['url'],
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public static function verifySign($context_arr, $signature)
    {
        $sign_key = $_ENV['active_payments']['well_pay']['appkey'];
        $return_arr_forsort = [];
        foreach ($context_arr as $key => $val) {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'data' && !empty($val)) {
                $return_arr_forsort[$key] = $val;
            }
            if ($key == 'data') {
                foreach ($val as $key1 => $val1) {
                    if (!empty($val1)) {
                        $return_arr_forsort[$key1] = $val1;
                    }
                }
            }
        }
        ksort($return_arr_forsort);
        $signStr = htmlspecialchars(http_build_query($return_arr_forsort));
        $sign2 = strtolower(md5($signStr . $sign_key));
        return $sign2 === $signature;
    }

    public function notify($request, $response, $args)
    {
        if (!self::verifySign($request->getParams(), $request->getParam('sign'))) {
            die('fail');
        }
        $order_no = $request->getParam('orderno');
        UserController::execute($order_no);
        die('success');
    }
}
