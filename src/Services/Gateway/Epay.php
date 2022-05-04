<?php
namespace App\Services\Gateway;

use App\Controllers\UserController;

class Epay
{
    public static function createOrder($amount, $order_no, $user_id)
    {
        $config = $_ENV['active_payments']['epay'];

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

            $params = [
                'money' => $amount,
                'name' => $order_no,
                'pid' => $config['uid'],
                'notify_url' => $_ENV['baseUrl'] . '/payments/notify/epay',
                'return_url' => $_ENV['baseUrl'] . '/user/order/' . $order_no,
                'out_trade_no' => $order_no,
            ];

            ksort($params);
            reset($params);

            $str = stripslashes(urldecode(http_build_query($params))) . $config['key'];
            $params['sign'] = md5($str);
            $params['sign_type'] = 'MD5';

            return json_encode([
                'ret' => 1,
                'type' => 'link',
                'link' => $config['gateway'] . '/submit.php?' . http_build_query($params),
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
        $params = [
            'pid' => $request->getParam('pid'),
            'money' => $request->getParam('money'),
            'name' => $request->getParam('name'),
            'notify_url' => $request->getParam('notify_url'),
            'return_url' => $request->getParam('return_url'),
            'out_trade_no' => $request->getParam('out_trade_no'),
        ];

        ksort($params);
        reset($params);

        $sign = $request->getParam('sign');
        $config = $_ENV['active_payments']['epay'];
        $str = stripslashes(urldecode(http_build_query($params))) . $config['key'];

        if ($sign != md5($str)) {
            return false;
        }

        UserController::execute($order_no);
        die('success');
    }
}
