<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Paylist;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function in_array;
use function json_decode;
use function time;

final class OrderController extends BaseController
{
    private static array $details = [
        'field' => [
            'op' => '操作',
            'id' => '订单ID',
            'user_id' => '提交用户',
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

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/order/index.tpl')
        );
    }

    public function search(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $out_order_id = $request->getParam('gateway_order_id');
        $paylist = (new Paylist())->where('tradeno', $out_order_id)->first();
        $invoice = (new Invoice())->where('id', $paylist?->invoice_id)->first();
        $order = (new Order())->where('id', $invoice?->order_id)->first();

        if ($order === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '未找到订单',
            ]);
        }

        return $response->withHeader('HX-Redirect', '/admin/order/' . $order->id . '/view')->withJson([
            'ret' => 1,
            'msg' => '找到了订单',
        ]);
    }

    /**
     * @throws Exception
     */
    public function detail(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $order = (new Order())->find($id);

        if ($order === null) {
            return $response->withStatus(301)->withHeader('Location', '/admin/order');
        }

        $order->product_type_text = $order->productType();
        $order->status_text = $order->status();
        $order->create_time = Tools::toDateTime($order->create_time);
        $order->update_time = Tools::toDateTime($order->update_time);
        $order->content = json_decode($order->product_content);

        $invoice = (new Invoice())->where('order_id', $id)->first();
        $invoice->status = $invoice->status();
        $invoice->create_time = Tools::toDateTime($invoice->create_time);
        $invoice->update_time = Tools::toDateTime($invoice->update_time);
        $invoice->pay_time = Tools::toDateTime($invoice->pay_time);
        $invoice->content = json_decode($invoice->content);

        return $response->write(
            $this->view()
                ->assign('order', $order)
                ->assign('invoice', $invoice)
                ->fetch('admin/order/view.tpl')
        );
    }

    public function cancel(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $order_id = $args['id'];
        $order = (new Order())->find($order_id);

        if ($order === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '订单不存在',
            ]);
        }

        if (in_array($order->status, ['activated', 'expired', 'cancelled'])) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '无法取消 ' . $order->status() . ' 状态的产品',
            ]);
        }

        $invoice = (new Invoice())->where('order_id', $order_id)->first();

        if ($invoice === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '关联账单不存在',
            ]);
        }

        if ($invoice->status === 'partially_paid') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '无法取消账单已部分支付的订单',
            ]);
        }

        $order->update_time = time();
        $order->status = 'cancelled';
        $order->save();

        if (in_array($invoice->status, ['paid_gateway', 'paid_balance', 'paid_admin'])) {
            $invoice->refundToBalance();

            return $response->withJson([
                'ret' => 1,
                'msg' => '订单取消成功，关联账单已退款至余额',
            ]);
        }

        $invoice->update_time = time();
        $invoice->status = 'cancelled';
        $invoice->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '订单取消成功',
        ]);
    }

    public function delete(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $order_id = $args['id'];
        $order = (new Order())->find($order_id);

        if ($order === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '订单不存在',
            ]);
        }

        $invoice = (new Invoice())->where('order_id', $order_id)->first();

        if ($order->delete() && $invoice->delete()) {
            return $response->withJson([
                'ret' => 1,
                'msg' => '删除成功',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除失败',
        ]);
    }

    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $orders = (new Order())->orderBy('id', 'desc')->get();

        foreach ($orders as $order) {
            $order->op = '<button class="btn btn-red" id="delete-order-' . $order->id . '"
             onclick="deleteOrder(' . $order->id . ')">删除</button>';

            if (in_array($order->status, ['pending_payment', 'pending_activation'])) {
                $order->op .= '
                <button class="btn btn-orange" id="cancel-order-' . $order->id . '"
                 onclick="cancelOrder(' . $order->id . ')">取消</button>';
            }

            $order->op .= '
            <a class="btn btn-primary" href="/admin/order/' . $order->id . '/view">查看</a>';
            $order->product_type = $order->productType();
            $order->status = $order->status();
            $order->create_time = Tools::toDateTime($order->create_time);
            $order->update_time = Tools::toDateTime($order->update_time);
        }

        return $response->withJson([
            'orders' => $orders,
        ]);
    }
}
