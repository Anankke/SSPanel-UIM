<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Setting;
use App\Services\Mail;
use App\Services\Payment;

final class SettingController extends BaseController
{
    public function index($request, $response, $args)
    {
        $config = [];
        $settings = Setting::get(['item', 'value', 'type']);

        foreach ($settings as $setting) {
            if ($setting->type === 'bool') {
                $config[$setting->item] = (bool) $setting->value;
            } else {
                $config[$setting->item] = (string) $setting->value;
            }
        }

        return $response->write(
            $this->view()
                //->registerClass('Setting', Setting::class)
                ->assign('settings', $config)
                ->assign('payment_gateways', self::returnGatewaysList())
                ->assign('active_payment_gateway', self::returnActiveGateways())
                ->display('admin/setting.tpl')
        );
    }

    public function save($request, $response, $args)
    {
        $class = $request->getParam('class');

        switch ($class) {
            // 支付
            case 'f2f_pay':
                $list = ['f2f_pay_app_id', 'f2f_pay_pid', 'f2f_pay_public_key', 'f2f_pay_private_key', 'f2f_pay_notify_url'];
                break;
            case 'vmq_pay':
                $list = ['vmq_gateway', 'vmq_key'];
                break;
            case 'payjs_pay':
                $list = ['payjs_url','payjs_mchid', 'payjs_key'];
                break;
            case 'theadpay':
                $list = ['theadpay_url', 'theadpay_mchid', 'theadpay_key'];
                break;
            case 'coinpay':
                $list = ['coinpay_appid', 'coinpay_secret'];
                break;
            case 'paymentwall':
                $list = ['pmw_publickey', 'pmw_privatekey', 'pmw_widget', 'pmw_height'];
                break;
            case 'stripe':
                $list = ['stripe_card', 'stripe_currency', 'stripe_pk', 'stripe_sk', 'stripe_webhook_key', 'stripe_min_recharge', 'stripe_max_recharge'];
                break;
            case 'e_pay':
                $list = ['epay_url', 'epay_pid', 'epay_key'];
                break;
            // 邮件
            case 'mail':
                $list = ['mail_driver'];
                break;
            case 'smtp':
                $list = ['smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_name', 'smtp_sender', 'smtp_ssl', 'smtp_bbc'];
                break;
            case 'mailgun':
                $list = ['mailgun_key', 'mailgun_domain', 'mailgun_sender'];
                break;
            case 'sendgrid':
                $list = ['sendgrid_key', 'sendgrid_sender', 'sendgrid_name'];
                break;
            case 'ses':
                $list = ['aws_access_key_id', 'aws_secret_access_key'];
                break;
            // 验证码
            case 'verify_code':
                $list = ['captcha_provider', 'enable_reg_captcha', 'enable_login_captcha', 'enable_checkin_captcha'];
                break;
            case 'verify_code_recaptcha':
                $list = ['recaptcha_sitekey', 'recaptcha_secret'];
                break;
            case 'verify_code_geetest':
                $list = ['geetest_id', 'geetest_key'];
                break;
            // 备份
            case 'email_backup':
                $list = ['auto_backup_email', 'auto_backup_password', 'auto_backup_notify'];
                break;
            // 客户服务
            case 'admin_contact':
                $list = ['enable_admin_contact', 'admin_contact1', 'admin_contact2', 'admin_contact3'];
                break;
            case 'web_customer_service_system':
                $list = ['live_chat', 'tawk_id', 'crisp_id', 'livechat_id', 'mylivechat_id'];
                break;
            // 个性化
            case 'background_image':
                $list = ['user_center_bg', 'admin_center_bg', 'user_center_bg_addr', 'admin_center_bg_addr'];
                break;
            // 注册设置
            case 'register':
                $list = ['reg_mode', 'reg_email_verify', 'email_verify_ttl', 'email_verify_ip_limit'];
                break;
            case 'register_default_value':
                $list = ['sign_up_for_free_traffic', 'sign_up_for_free_time', 'sign_up_for_class', 'sign_up_for_class_time', 'sign_up_for_invitation_codes', 'connection_device_limit', 'connection_rate_limit', 'sign_up_for_method', 'sign_up_for_protocol', 'sign_up_for_protocol_param', 'sign_up_for_obfs', 'sign_up_for_obfs_param', 'sign_up_for_daily_report'];
                break;
            // 邀请设置
            case 'invitation_reward':
                $list = ['invitation_to_register_balance_reward', 'invitation_to_register_traffic_reward'];
                break;
            // 返利设置
            case 'rebate_mode':
                $list = ['invitation_mode', 'invite_rebate_mode', 'rebate_ratio', 'rebate_frequency_limit', 'rebate_amount_limit', 'rebate_time_range_limit'];
                break;
        }

        foreach ($list as $item) {
            $setting = Setting::where('item', '=', $item)->first();

            if ($setting->type === 'array') {
                $setting->value = json_encode($request->getParam("${item}"));
            } else {
                $setting->value = $request->getParam("${item}");
            }

            if (! $setting->save()) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => "保存 ${item} 时出错",
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }

    public function test($request, $response, $args)
    {
        $to = $request->getParam('recipient');

        try {
            Mail::send(
                $to,
                '测试邮件',
                'auth/test.tpl',
                [],
                []
            );
        } catch (Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '测试邮件发送失败',
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '测试邮件发送成功',
        ]);
    }

    public function returnGatewaysList()
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

    public function payment($request, $response, $args)
    {
        $gateway_in_use = [];
        foreach (array_values(self::returnGatewaysList()) as $value) {
            $payment_switch = $request->getParam("${value}");
            if ($payment_switch === '1') {
                array_push($gateway_in_use, $value);
            }
        }

        $gateway = Setting::where('item', '=', 'payment_gateway')->first();
        $gateway->value = json_encode($gateway_in_use);
        $gateway->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }
}
