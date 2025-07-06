<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Paylist;
use App\Models\UserMoneyLog;
use App\Services\Payment;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_decode;
use function json_encode;
use function time;

final class InvoiceController extends BaseController
{
    private static array $details = [
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

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('user/invoice/index.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function detail(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $this->antiXss->xss_clean($args['id']);

        $invoice = (new Invoice())->where('user_id', $this->user->id)->where('id', $id)->first();

        if ($invoice === null) {
            return $response->withRedirect('/user/invoice');
        }

        $paylist = [];

        if ($invoice->status === 'paid_gateway') {
            $paylist = (new Paylist())->where('invoice_id', $invoice->id)->where('status', 1)->first();
        }

        $invoice->status_text = $invoice->status();
        $invoice->create_time = Tools::toDateTime($invoice->create_time);
        $invoice->update_time = Tools::toDateTime($invoice->update_time);
        $invoice->pay_time = Tools::toDateTime($invoice->pay_time);
        $invoice_content = json_decode($invoice->content);

        return $response->write(
            $this->view()
                ->assign('invoice', $invoice)
                ->assign('invoice_content', $invoice_content)
                ->assign('paylist', $paylist)
                ->assign('payments', Payment::getPaymentsEnabled())
                ->fetch('user/invoice/view.tpl')
        );
    }

    public function payBalance(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $invoice_id = $this->antiXss->xss_clean($request->getParam('invoice_id'));

        $invoice = (new Invoice())->where('user_id', $this->user->id)->where('id', $invoice_id)->first();

        if ($invoice === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '账单不存在',
            ]);
        }

        $user = $this->user;

        if ($user->is_shadow_banned) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '支付失败，请稍后再试',
            ]);
        }

        // 账单是否为充值
        if ($invoice->type === 'topup') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '该账单不支持使用余额支付',
            ]);
        }

        // 组合支付
        if ($user->money > 0) {
            $money_before = $user->money;

            if ($user->money >= $invoice->price) {
                $paid = $invoice->price;
                $invoice->status = 'paid_balance';
            } else {
                $paid = $user->money;
                $invoice->status = 'partially_paid';
                $invoice->price -= $paid;
                $invoice_content = json_decode($invoice->content);
                $invoice_content[] = [
                    'content_id' => count($invoice_content),
                    'name' => '余额部分支付',
                    'price' => '-' . $paid,
                ];
                $invoice->content = json_encode($invoice_content);
            }

            $user->money -= $paid;
            $user->save();

            (new UserMoneyLog())->add(
                $user->id,
                $money_before,
                (float) $user->money,
                -$paid,
                '支付账单 #' . $invoice->id
            );

            $invoice->update_time = time();
            $invoice->pay_time = time();
            $invoice->save();
        } else {
            return $response->withJson([
                'ret' => 0,
                'msg' => '余额不足',
            ]);
        }

        if ($invoice->status === 'paid_balance') {
            return $response->withHeader('HX-Redirect', '/user/invoice');
        }

        return $response->withHeader('HX-Refresh', 'true');
    }

    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $invoices = (new Invoice())->orderBy('id', 'desc')->where('user_id', $this->user->id)->get();

        foreach ($invoices as $invoice) {
            $invoice->op = '<a class="btn btn-primary" href="/user/invoice/' . $invoice->id . '/view">查看</a>';
            $invoice->status = $invoice->status();
            $invoice->create_time = Tools::toDateTime($invoice->create_time);
            $invoice->update_time = Tools::toDateTime($invoice->update_time);
            $invoice->pay_time = Tools::toDateTime($invoice->pay_time);
        }

        return $response->withJson([
            'invoices' => $invoices,
        ]);
    }
}
