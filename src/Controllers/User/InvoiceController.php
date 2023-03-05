<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Invoice;
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
        $invoice_id = $args['id'];
        $invoice = Invoice::where('user_id', $this->user->id)
            ->where('id', $invoice_id)
            ->first();

        return $response->write(
            $this->view()
                ->assign('invoice', $invoice)
                ->fetch('user/invoice/index.tpl')
        );
    }
}
