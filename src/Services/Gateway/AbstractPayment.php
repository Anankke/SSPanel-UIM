<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Code;
use App\Models\Invoice;
use App\Models\Payback;
use App\Models\Paylist;
use App\Models\Setting;
use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

abstract class AbstractPayment
{
    /**
     * @param array     $args
     */
    abstract public function purchase(Request $request, Response $response, array $args): ResponseInterface;

    /**
     * @param array     $args
     */
    abstract public function notify(Request $request, Response $response, array $args): ResponseInterface;

    /**
     * 支付网关的 codeName, 规则为 [0-9a-zA-Z_]*
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

    /**
     * @param array     $args
     */
    public function getReturnHTML(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write('ok');
    }

    /**
     * @param array     $args
     */
    public function getStatus(Request $request, Response $response, array $args): ResponseInterface
    {
        $p = Paylist::where('tradeno', $_POST['pid'])->first();
        return $response->withJson([
            'ret' => 1,
            'result' => $p->satatus,
        ]);
    }

    abstract public static function getPurchaseHTML(): string;

    public function postPayment($tradeno): false|int|string
    {
        $paylist = Paylist::where('tradeno', $tradeno)->first();

        if ($paylist->status === 1) {
            return \json_encode(['errcode' => 0]);
        }

        $paylist->datetime = \time();
        $paylist->status = 1;
        $paylist->save();

        $user = User::find($paylist->userid);

        if ($paylist->invoice_id !== 0) {
            $invoice = Invoice::where('id', $paylist->invoice_id)->first();
            $invoice->status = 'paid_gateway';
            $invoice->update_time = \time();
            $invoice->pay_time = \time();
            $invoice->save();
        } else {
            $user->money += $paylist->total;
            $user->save();
            $codeq = new Code();
            $codeq->code = $method;
            $codeq->isused = 1;
            $codeq->type = -1;
            $codeq->number = $paylist->total;
            $codeq->usedatetime = date('Y-m-d H:i:s');
            $codeq->userid = $user->id;
            $codeq->save();
        }

        // 返利
        if ($user->ref_by > 0 && Setting::obtain('invitation_mode') === 'after_recharge') {
            Payback::rebate($user->id, $paylist->total);
        }

        return 0;
    }

    public static function generateGuid(): string
    {
        return substr(Uuid::uuid4()->toString(), 0, 8);
    }

    public static function exchange($currency)
    {
        $ch = curl_init();
        $url = 'https://api.exchangerate.host/latest?symbols=CNY&base=' . strtoupper($currency);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $currency = json_decode(curl_exec($ch));
        curl_close($ch);

        return $currency->rates->CNY;
    }

    protected static function getCallbackUrl(): string
    {
        return $_ENV['baseUrl'] . '/payment/notify/' . get_called_class()::_name();
    }

    protected static function getUserReturnUrl(): string
    {
        return $_ENV['baseUrl'] . '/user/payment/return/' . get_called_class()::_name();
    }

    protected static function getActiveGateway($key): bool
    {
        $payment_gateways = Setting::where('item', '=', 'payment_gateway')->first();
        $active_gateways = \json_decode($payment_gateways->value);
        if (\in_array($key, $active_gateways)) {
            return true;
        }
        return false;
    }
}
