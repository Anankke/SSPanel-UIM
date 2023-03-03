<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Utils\Tools;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class OrderController extends BaseController
{
    public static array $details = [
        'field' => [
            'op' => '操作',
            'id' => '订单ID',
            'product_id' => '商品ID',
            'product_type' => '商品类型',
            'product_name' => '商品名称',
            'coupon' => '优惠码',
            'price' => '金额',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ],
    ];

    public function order(ServerRequest $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('user/order/index.tpl')
        );
    }

    public function create(ServerRequest $request, Response $response, array $args)
    {
        $product_id = $request->getQueryParams()['product_id'] ?? null;

        if ($product_id === null || $product_id === '') {
            return $response->withRedirect('/user/product');
        }

        $product = Product::where('id', $product_id)->first();

        $product->content = \json_decode($product->content);

        return $response->write(
            $this->view()
                ->assign('product', $product)
                ->fetch('user/order/create.tpl')
        );
    }

    public function detail(ServerRequest $request, Response $response, array $args)
    {
        $order_id = $args['id'];
        $order = Order::where('user_id', $this->user->id)
            ->where('id', $order_id)
            ->first();

        return $response->write(
            $this->view()
                ->assign('order', $order)
                ->fetch('user/order/view.tpl')
        );
    }

    public function ajax(ServerRequest $request, Response $response, array $args)
    {
        $orders = Order::orderBy('id', 'desc')->where('user_id', $this->user->id)->get();

        foreach ($orders as $order) {
            $order->op = '<a class="btn btn-blue" href="/user/order/' . $order->id . '/view">查看</a>';
            if ($order->status === 'pending_payment') {
                $invoice_id = Invoice::where('order_id', $order->id)->first()->id;
                $order->op .= '<a class="btn btn-red" href="/user/invoice/' . $invoice_id . '/cancel">支付</a>';
            }
            $order->product_type = Tools::getOrderProductType($order);
            $order->status = Tools::getOrderStatus($order);
            $order->create_time = Tools::toDateTime($order->create_time);
            $order->update_time = Tools::toDateTime($order->update_time);
        }

        return $response->withJson([
            'orders' => $orders,
        ]);
    }
}
