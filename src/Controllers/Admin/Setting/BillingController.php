<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Setting;
use App\Services\Payment;
use Exception;
use function json_decode;
use function json_encode;

final class BillingController extends BaseController
{
    public static array $update_field = [
        // 支付宝当面付
        'f2f_pay_app_id',
        'f2f_pay_pid',
        'f2f_pay_public_key',
        'f2f_pay_private_key',
        'f2f_pay_notify_url',
        // Stripe
        'stripe_card',
        'stripe_alipay',
        'stripe_wechat',
        'stripe_currency',
        'stripe_pk',
        'stripe_sk',
        'stripe_webhook_key',
        'stripe_min_recharge',
        'stripe_max_recharge',
        // EPay
        'epay_url',
        'epay_pid',
        'epay_key',
        'epay_alipay',
        'epay_wechat',
        'epay_qq',
        'epay_usdt',
        // PayPal
        'paypal_mode',
        'paypal_client_id',
        'paypal_client_secret',
        'paypal_currency',
        'paypal_locale',
    ];

    /**
     * @throws Exception
     */
    public function billing($request, $response, $args)
    {
        $settings = [];
        $settings_raw = Setting::get(['item', 'value', 'type']);

        foreach ($settings_raw as $setting) {
            if ($setting->type === 'bool') {
                $settings[$setting->item] = (bool) $setting->value;
            } else {
                $settings[$setting->item] = (string) $setting->value;
            }
        }

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->assign('payment_gateways', self::returnGatewaysList())
                ->assign('active_payment_gateway', self::returnActiveGateways())
                ->fetch('admin/setting/billing.tpl')
        );
    }

    public function saveBilling($request, $response, $args)
    {
        $gateway_in_use = [];

        foreach (self::returnGatewaysList() as $value) {
            $payment_enable = $request->getParam($value);
            if ($payment_enable === 'true') {
                $gateway_in_use[] = $value;
            }
        }

        $gateway = Setting::where('item', '=', 'payment_gateway')->first();
        $gateway->value = json_encode($gateway_in_use);

        if (! $gateway->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '保存支付网关时出错',
            ]);
        }

        $list = self::$update_field;

        foreach ($list as $item) {
            $setting = Setting::where('item', '=', $item)->first();

            if ($setting->type === 'array') {
                $setting->value = json_encode($request->getParam($item));
            } else {
                $setting->value = $request->getParam($item);
            }

            if (! $setting->save()) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => "保存 {$item} 时出错",
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }

    public function returnGatewaysList(): array
    {
        $result = [];

        foreach (Payment::getAllPaymentMap() as $payment) {
            $result[$payment::_name()] = $payment::_name();
        }

        return $result;
    }

    public function returnActiveGateways()
    {
        $payment_gateways = Setting::where('item', '=', 'payment_gateway')->first();
        return json_decode($payment_gateways->value);
    }
}
