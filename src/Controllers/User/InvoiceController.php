<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Invoice;
use App\Utils\Tools;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class InvoiceController extends BaseController
{
    public static array $details = [
        'field' => [
            'op' => '操作',
            'id' => '账单ID',
            'order_id' => '订单ID',
            'price' => '账单金额',
            'status' => '账单状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'pay_time' => '支付时间',
        ],
    ];

    public function invoice(ServerRequest $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('user/invoice/index.tpl')
        );
    }

    public function detail(ServerRequest $request, Response $response, array $args)
    {
        $id = $args['id'];

        $invoice = Invoice::find($id);
        $invoice->status = Tools::getInvoiceStatus($invoice);
        $invoice->create_time = Tools::toDateTime($invoice->create_time);
        $invoice->update_time = Tools::toDateTime($invoice->update_time);
        $invoice->pay_time = Tools::toDateTime($invoice->pay_time);
        $invoice_content = \json_decode($invoice->content);

        return $response->write(
            $this->view()
                ->assign('invoice', $invoice)
                ->assign('invoice_content', $invoice_content)
                ->fetch('admin/invoice/view.tpl')
        );
    }

    public function ajax(ServerRequest $request, Response $response, array $args)
    {
        $invoices = Invoice::orderBy('id', 'desc')->get();

        foreach ($invoices as $invoice) {
            $invoice->op = '<a class="btn btn-blue" href="/user/invoice/' . $invoice->id . '/view">查看</a>';
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
