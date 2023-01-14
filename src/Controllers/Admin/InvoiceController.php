<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Order;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class InvoiceController extends BaseController
{
    public static array $details = [
        'field' => [
            'op' => '操作',
            'id' => '账单ID',
            'user_id' => '归属用户',
            'order_id' => '订单ID',
            'price' => '账单金额',
            'status' => '账单状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'pay_time' => '支付时间',
        ],
    ];

    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->display('admin/invoice/index.tpl')
        );
    }

    public function detail(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        $invoice = Invoice::find($id);
        $invoice->status = Tools::getInvoiceStatus($invoice);
        $invoice->create_time = Tools::toDateTime($invoice->create_time);
        $invoice->update_time = Tools::toDateTime($invoice->update_time);
        $invoice->pay_time = Tools::toDateTime($invoice->pay_time);
        $invoice_content = \json_decode($invoice->content, true);

        return $response->write(
            $this->view()
                ->assign('invoice', $invoice)
                ->assign('invoice_content', $invoice_content)
                ->display('admin/invoice/view.tpl')
        );
    }

    public function mark_paid(Request $request, Response $response, array $args): ResponseInterface
    {
        $invoice_id = $args['id'];
        $invoice = Invoice::find($invoice_id);

        if ($invoice->status === 'paid_gateway' || $invoice->status === 'paid_balance' || $invoice->status === 'paid_admin') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '不能标记已经支付的账单',
            ]);
        }

        $order = Order::find($invoice->order_id);

        if ($order->status === 'cancelled') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '关联订单已被取消，标记失败',
            ]);
        }

        $order->status = 'pending_activation';
        $order->save();

        $invoice->pay_time = \time();
        $invoice->status = 'paid_admin';
        $invoice->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '成功标记账单为已支付（管理员）',
        ]);
    }

    public function ajax(Request $request, Response $response, array $args): ResponseInterface
    {
        $invoices = Invoice::orderBy('id', 'desc')->get();

        foreach ($invoices as $invoice) {
            $invoice->op = '<a class="btn btn-blue" href="/admin/invoice/' . $invoice->id . '/view">查看</a>';
            $invoice->status = Tools::getInvoiceStatus($invoice);
            $invoice->create_time = Tools::toDateTime($invoice->create_time);
            $invoice->update_time = Tools::toDateTime($invoice->update_time);
            $invoice->pay_time = Tools::toDateTime($invoice->pay_time);
        }

        return $response->withJson([
            'invoices' => $invoices,
        ]);
    }
}
