<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Config;
use App\Models\Invoice;
use App\Models\Paylist;
use App\Models\User;
use App\Services\DB;
use App\Services\Reward;
use App\Services\Wallet;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function bccomp;
use function bcsub;
use function get_called_class;
use function in_array;
use function json_decode;
use function time;

abstract class Base
{
    protected AntiXSS $antiXss;

    abstract public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface;

    abstract public function notify(ServerRequest $request, Response $response, array $args): ResponseInterface;

    /**
     * 支付网关的 codeName
     */
    abstract public static function _name(): string;

    /**
     * 是否启用支付网关
     */
    abstract public static function _enable(): bool;

    /**
     * 显示给用户的名称
     */
    abstract public static function _readableName(): string;

    public function getReturnHTML(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write('ok');
    }

    abstract public static function getPurchaseHTML(): string;

    public function postPayment(string $trade_no): void
    {
        $attempt = (new Paylist())->where('tradeno', $trade_no)->first(['id', 'invoice_id']);

        if ($attempt === null) {
            return;
        }

        DB::connection()->transaction(static function () use ($attempt): void {
            $invoice = (new Invoice())->lockForUpdate()->find($attempt->invoice_id);
            $paylist = (new Paylist())->lockForUpdate()->find($attempt->id);

            // 每笔支付只入账一次，网关重复通知时不再处理
            if ($paylist->status !== 0) {
                return;
            }

            $paylist->datetime = time();
            $paylist->status = 1;
            $paylist->save();

            $total = (string) $paylist->total;

            if ($invoice?->acceptsPayment() !== true) {
                Wallet::credit(
                    $paylist->userid,
                    $total,
                    '账单 #' . $paylist->invoice_id . ' 已不收款，支付 ' . $paylist->tradeno . ' 转入余额'
                );
            } else {
                $price = (string) $invoice->price;

                if (bccomp($total, $price, 2) >= 0) {
                    $invoice->status = 'paid_gateway';
                    $invoice->update_time = time();
                    $invoice->pay_time = time();
                    $invoice->save();
                }

                if (bccomp($total, $price, 2) > 0) {
                    Wallet::credit($paylist->userid, bcsub($total, $price, 2), '超额支付账单 #' . $invoice->id);
                }
            }

            $user = (new User())->find($paylist->userid);

            if ($user !== null && $user->ref_by > 0 && Config::obtain('invite_mode') === 'reward') {
                Reward::issuePaybackReward($user->id, $user->ref_by, $invoice?->price, $paylist->invoice_id);
            }
        }, 3);
    }

    public static function generateGuid(): string
    {
        return Tools::genRandomChar();
    }

    protected static function getCallbackUrl(): string
    {
        return $_ENV['baseUrl'] . '/payment/notify/' . get_called_class()::_name();
    }

    protected static function getUserReturnUrl(): string
    {
        return $_ENV['baseUrl'] . '/user/payment/return/' . get_called_class()::_name();
    }

    protected static function getActiveGateway(string $key): bool
    {
        $payment_gateways = (new Config())->where('item', 'payment_gateway')->first();
        $active_gateways = json_decode($payment_gateways->value);

        if (in_array($key, $active_gateways)) {
            return true;
        }

        return false;
    }
}
