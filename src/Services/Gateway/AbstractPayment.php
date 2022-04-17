<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午4:23
 */

namespace App\Services\Gateway;

use App\Models\User;
use App\Models\Code;
use App\Models\Paylist;
use App\Models\Payback;
use App\Models\Setting;
use App\Utils\Telegram;
use Slim\Http\{Request, Response};

abstract class AbstractPayment
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    abstract public function purchase($request, $response, $args);

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    abstract public function notify($request, $response, $args);

    /**
     * 支付网关的 codeName, 规则为 [0-9a-zA-Z_]*
     */
    abstract public static function _name();

    /**
     * 是否启用支付网关
     * 
     * TODO: 传入目前用户信, etc..
     */
    abstract public static function _enable();

    /**
     * 显示给用户的名称
     */
    public static function _readableName() {
        return (get_called_class())::_name() . ' 充值';
    }
    
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    abstract public function getReturnHTML($request, $response, $args);

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    abstract public function getStatus($request, $response, $args);

    abstract public static function getPurchaseHTML();

    protected static function getCallbackUrl() {
        return $_ENV['baseUrl'] . '/payment/notify/' . (get_called_class())::_name();
    }

    protected static function getUserReturnUrl() {
        return $_ENV['baseUrl'] . '/user/payment/return/' . (get_called_class())::_name();
    }

    protected static function getActiveGateway($key) {
        $payment_gateways = Setting::where('item', '=', 'payment_gateway')->first();
        $active_gateways = json_decode($payment_gateways->value);
        if (in_array($key, $active_gateways)) {
            return true;
        }
        return false;
    }

    public function postPayment($pid, $method)
    {
        $p = Paylist::where('tradeno', $pid)->first();

        if ($p->status == 1) {
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
        if ($user->ref_by > 0 && Setting::obtain('invitation_mode') == 'after_recharge') {
            Payback::rebate($user->id, $p->total);
        }

        if ($_ENV['enable_donate'] == true) {
            if ($user->is_hide == 1) {
                Telegram::Send('一位不愿透露姓名的大老爷给我们捐了 ' . $codeq->number . ' 元!');
            } else {
                Telegram::Send($user->user_name . ' 大老爷给我们捐了 ' . $codeq->number . ' 元！');
            }
        }
        return 0;
    }

    public static function generateGuid()
    {
        mt_srand((double)microtime() * 10000);
        $charid = strtoupper(md5(uniqid(mt_rand() + time(), true)));
        $hyphen = chr(45);
        $uuid = chr(123)
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12)
            . chr(125);
        $uuid = str_replace(['}', '{', '-'], '', $uuid);
        $uuid = substr($uuid, 0, 8);
        return $uuid;
    }
}
