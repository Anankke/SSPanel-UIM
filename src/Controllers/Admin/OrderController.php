<?php
namespace App\Controllers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\ProductOrder;
use App\Controllers\AdminController;

class OrderController extends AdminController
{
    public function index($request, $response, $args)
    {
        $logs = ProductOrder::orderBy('id', 'desc')
        ->limit(500)
        ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->display('admin/order.tpl')
        );
    }

    public function ajaxQuery($request, $response, $args)
    {
        $no = $request->getParam('no');
        $user_id = $request->getParam('user_id');
        $product_name = $request->getParam('product_name');
        $order_coupon = $request->getParam('order_coupon');
        $product_type = $request->getParam('product_type');
        $order_status = $request->getParam('order_status');
        $order_payment = $request->getParam('order_payment');
        $execute_status = $request->getParam('execute_status');

        $condition = [];

        ($no != '') && array_push($condition, ['no', '=', $no]);
        ($user_id != '') && array_push($condition, ['user_id', '=', $user_id]);
        ($product_name != '') && array_push($condition, ['product_name', 'like', '%'.$product_name.'%']);
        ($order_coupon != '') && array_push($condition, ['order_coupon', '=', $order_coupon]);
        ($product_type != 'all') && array_push($condition, ['product_type', '=', $product_type]);
        ($order_status != 'all') && array_push($condition, ['order_status', '=', $order_status]);
        ($order_payment != 'all') && array_push($condition, ['order_payment', '=', $order_payment]);
        ($execute_status != 'all') && array_push($condition, ['execute_status', '=', $execute_status]);

        $results = ProductOrder::orderBy('id', 'desc')
        ->where($condition)
        ->limit(500)
        ->get();

        foreach($results as $result)
        {
            $result->created_at = date('Y-m-d H:i:s', $result->created_at);
            $result->order_price = sprintf("%.2f", $result->order_price / 100);
            $result->product_price = sprintf("%.2f", $result->product_price / 100);
            if ($result->order_status == 'paid') {
                $result->paid_at = date('Y-m-d H:i:s', $result->paid_at);
                $result->execute_status = '已执行';
            } else {
                $result->paid_at = 'null';
                $result->execute_status = '未执行';
            }
            if (empty($result->order_coupon)) {
                $result->order_coupon = 'null';
            }
            if ($result->order_status == 'paid') {
                $result->order_status = '已支付';
            } else {
                if (time() > $result->expired_at) {
                    $result->order_status = '超时';
                } else {
                    $result->order_status = '等待支付';
                }
            }
        }

        return $response->withJson([
            'ret' => 1,
            'result' => $results
        ]);
    }
}
