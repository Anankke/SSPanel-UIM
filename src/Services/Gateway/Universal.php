<?php
namespace App\Services\Gateway;

use App\Controllers\UserController;

class Universal
{
    public static function createOrder($amount, $order_no)
    {
        $config = $_ENV['active_payments']['universal'];

        try {
            if ($config['min'] != false && $amount < $config['min']) {
                throw new \Exception('账单金额低于支付方式限额');
            }
            if ($config['max'] != false && $amount > $config['max']) {
                throw new \Exception('账单金额高于支付方式限额');
            }

            $params = [
                'amount' => $amount,
                'trade_no' => $order_no,
                'return_url' => $_ENV['baseUrl'] . '/user/order/' . $order_no,
            ];

            $order = file_get_contents($config['gateway'] . '/create?' . http_build_query($params));
            $order = json_decode($order, true);

            return json_encode([
                'ret' => 1,
                'type' => 'link',
                'link' => $order['redirect']['url'],
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
        $id = $request->getParam('id'); // 支付服务商生成的订单号
        $sign = $request->getParam('sign'); // 支付服务商对订单的签名
        $order_no = $request->getParam('order_no'); // 接收订单提交方生成的订单号
        $config = $_ENV['active_payments']['universal']; // 获取支付服务商参数
        $_sign = md5($id . $config['sign_key']); // 验证签名

        if ($sign != $_sign) {
            die('error_sign');
        }

        UserController::execute($order_no);
        die('success');
    }
}
