<?php
namespace App\Services\Gateway;

use App\Controllers\UserController;

class VmqWechat
{
    public static function createOrder($amount, $order_no, $user_id)
    {
        $config = $_ENV['active_payments']['vmq_wechat'];

        try {
            if (!$config['enable']) {
                throw new \Exception('此方式暂未启用');
            }
            if ($config['visible_range']) {
                if ($user_id < $config['visible_min_range'] || $user_id > $config['visible_max_range']) {
                    throw new \Exception('此方式暂未启用');
                }
            }
            if ($config['min'] != false && $amount < $config['min']) {
                throw new \Exception('账单金额低于支付方式限额');
            }
            if ($config['max'] != false && $amount > $config['max']) {
                throw new \Exception('账单金额高于支付方式限额');
            }

            $sign = md5($order_no . $user_id . '1' . $amount . $config['key']);

            $params = [
                'payId' => $order_no,
                'type' => '1', // 1: wechat; 2: alipay
                'price' => $amount,
                'sign' => $sign,
                'param' => $user_id,
                'isHtml' => '1',
                'notifyUrl' => $_ENV['baseUrl'] . '/payments/notify/vmq_wechat',
                'returnUrl' => $_ENV['baseUrl'] . '/user/order/' . $order_no,
            ];

            return json_encode([
                'ret' => 1,
                'type' => 'link',
                'link' => $config['gateway'] . '/createOrder?' . http_build_query($params),
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function notify($request, $response, $args)
    {
        $config = $_ENV['active_payments']['vmq_wechat'];
        $payId = $request->getParam('payId');
        $param = $request->getParam('param');
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        $reallyPrice = $request->getParam('reallyPrice');
        $cloud_sign = $request->getParam('sign');

        $local_sign = md5($payId . $param . $type . $price . $reallyPrice . $config['key']);

        if ($cloud_sign != $local_sign) {
            die('fail');
        }

        UserController::execute($payId);
        die('success');
    }
}
