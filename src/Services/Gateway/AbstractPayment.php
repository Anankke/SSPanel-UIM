<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午4:23
 */

namespace App\Services\Gateway;

use App\Models\Code;
use App\Models\Payback;
use App\Models\Paylist;
use App\Models\Setting;
use App\Models\User;
use App\Utils\Telegram;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Http\Request;
use Slim\Http\Response;

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
     *
     * TODO: 传入目前用户信, etc..
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

    public function postPayment($pid, $method)
    {
        $p = Paylist::where('tradeno', $pid)->first();

        if ($p->status === 1) {
            return json_encode(['errcode' => 0]);
        }

        $p->status = 1;
        $p->save();
        $user = User::find($p->userid);
        $user->money += $p->total;
        $user->save();
        $codeq = new Code();
        $codeq->code = $method;
        $codeq->isused = 1;
        $codeq->type = -1;
        $codeq->number = $p->total;
        $codeq->usedatetime = date('Y-m-d H:i:s');
        $codeq->userid = $user->id;
        $codeq->save();

        // 返利
        if ($user->ref_by > 0 && Setting::obtain('invitation_mode') === 'after_recharge') {
            Payback::rebate($user->id, $p->total);
        }

        if ($_ENV['enable_donate'] === true) {
            if ($user->is_hide === 1) {
                Telegram::send('一位不愿透露姓名的大老爷给我们捐了 ' . $codeq->number . ' 元!');
            } else {
                Telegram::send($user->user_name . ' 大老爷给我们捐了 ' . $codeq->number . ' 元！');
            }
        }
        return 0;
    }

    public static function generateGuid()
    {
        return substr(Uuid::uuid4()->toString(), 0, 8);
    }

    protected static function getCallbackUrl()
    {
        return $_ENV['baseUrl'] . '/payment/notify/' . get_called_class()::_name();
    }

    protected static function getUserReturnUrl()
    {
        return $_ENV['baseUrl'] . '/user/payment/return/' . get_called_class()::_name();
    }

    protected static function getActiveGateway($key)
    {
        $payment_gateways = Setting::where('item', '=', 'payment_gateway')->first();
        $active_gateways = json_decode($payment_gateways->value);
        if (in_array($key, $active_gateways)) {
            return true;
        }
        return false;
    }
}
