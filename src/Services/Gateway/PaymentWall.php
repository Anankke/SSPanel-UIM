<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/27
 * Time: 7:20 PM
 */

namespace App\Services\Gateway;

use App\Models\Code;
use App\Models\Payback;
use App\Models\Setting;
use App\Models\User;
use App\Services\Auth;
use App\Utils\Telegram;
use Paymentwall_Config;
use Paymentwall_Pingback;
use Paymentwall_Widget;

final class PaymentWall extends AbstractPayment
{
    public static function _name()
    {
        return 'paymentwall';
    }

    public static function _enable()
    {
        return self::getActiveGateway('paymentwall');
    }

    public static function _readableName()
    {
        return 'PaymentWall';
    }

    public function purchase($request, $response, $args): void
    {
        // TODO: Implement purchase() method.
    }

    public function notify($request, $response, $args): void
    {
        $configs = Setting::getClass('pmw');
        if ($configs['pmw_publickey'] !== '') {
            Paymentwall_Config::getInstance()->set([
                'api_type' => Paymentwall_Config::API_VC,
                'public_key' => $configs['pmw_publickey'],
                'private_key' => $configs['pmw_privatekey'],
            ]);
            $pingback = new Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
            if ($pingback->validate()) {
                $virtualCurrency = $pingback->getVirtualCurrencyAmount();
                // if ($pingback->isDeliverable()) {
                //     // deliver the virtual currency
                // } elseif ($pingback->isCancelable()) {
                //     // withdraw the virual currency
                // }
                $user = User::find($pingback->getUserId());
                $user->money += $pingback->getVirtualCurrencyAmount();
                $user->save();
                $codeq = new Code();
                $codeq->code = 'Payment Wall 充值';
                $codeq->isused = 1;
                $codeq->type = -1;
                $codeq->number = $pingback->getVirtualCurrencyAmount();
                $codeq->usedatetime = date('Y-m-d H:i:s');
                $codeq->userid = $user->id;
                $codeq->save();
                // 返利
                if ($user->ref_by > 0 && Setting::obtain('invitation_mode') === 'after_recharge') {
                    Payback::rebate($user->id, $virtualCurrency);
                }
                // 通知
                echo 'OK'; // Paymentwall expects response to be OK, otherwise the pingback will be resent
                if ($_ENV['enable_donate'] === true) {
                    if ($user->is_hide === 1) {
                        Telegram::send('姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 ' . $codeq->number . ' 元呢~');
                    } else {
                        Telegram::send('姐姐姐姐，' . $user->user_name . ' 大老爷给我们捐了 ' . $codeq->number . ' 元呢~');
                    }
                }
            } else {
                echo $pingback->getErrorSummary();
            }
        } else {
            echo 'error';
        }
    }

    public static function getPurchaseHTML()
    {
        $configs = Setting::getClass('pmw');
        Paymentwall_Config::getInstance()->set([
            'api_type' => Paymentwall_Config::API_VC,
            'public_key' => $configs['pmw_publickey'],
            'private_key' => $configs['pmw_privatekey'],
        ]);
        $user = Auth::getUser();
        $widget = new Paymentwall_Widget(
            $user->id, // id of the end-user who's making the payment
            $configs['pmw_widget'],      // widget code, e.g. p1; can be picked inside of your merchant account
            [],     // array of products - leave blank for Virtual Currency API
            [
                'email' => $user->email,
                'history' => [
                    'registration_date' => strtotime($user->reg_date),
                    'registration_ip' => $user->reg_ip,
                    'payments_number' => Code::where('userid', '=', $user->id)->where('type', '=', -1)->count(),
                    'membership' => $user->class,
                ],
                'customer' => [
                    'username' => $user->user_name,
                ],
            ] // additional parameters
        );
        return $widget->getHtmlCode(['height' => $configs['pmw_height'], 'width' => '100%']);
    }

}
