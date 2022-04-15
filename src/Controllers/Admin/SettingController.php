<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\{
    Setting
};
use Slim\Http\{
    Request,
    Response
};
use App\Services\{
    Mail
};

class SettingController extends AdminController
{
    public function index($request, $response, $args)
    {
        $config = array();
        $settings = Setting::get(['item', 'value', 'type']);
        
        foreach ($settings as $setting)
        {
        	if ($setting->type == 'bool') {
                $config[$setting->item] = (bool) $setting->value;
            } else {
                $config[$setting->item] = (string) $setting->value;
            }
        }
        
        return $response->write(
            $this->view()
                //->registerClass('Setting', Setting::class)
                ->assign('settings', $config)
                ->assign('payment_gateways', self::return_gateways_list())
                ->assign('active_payment_gateway', self::return_active_gateways())
                ->display('admin/setting.tpl')
        );
    }
    
    public function save($request, $response, $args)
    {
        $class = $request->getParam('class');
        
        switch ($class) {
            // 邮件
            case 'mail':
                $list = array('mail_driver');
                break;
            case 'smtp':
                $list = array('smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_name', 'smtp_sender', 'smtp_ssl', 'smtp_bbc');
                break;
            case 'mailgun':
                $list = array('mailgun_key', 'mailgun_domain', 'mailgun_sender');
                break;
            case 'sendgrid':
                $list = array('sendgrid_key', 'sendgrid_sender', 'sendgrid_name');
                break;
            case 'ses':
                $list = array('aws_access_key_id', 'aws_secret_access_key');
                break;
            // 验证码
            case 'verify_code':
                $list = array('captcha_provider', 'enable_reg_captcha', 'enable_login_captcha', 'enable_checkin_captcha');
                break;
            case 'verify_code_recaptcha':
                $list = array('recaptcha_sitekey', 'recaptcha_secret');
                break;
            case 'verify_code_geetest':
                $list = array('geetest_id', 'geetest_key');
                break;
            // 客户服务
            case 'web_customer_service_system':
                $list = array('live_chat', 'tawk_id', 'crisp_id', 'livechat_id', 'mylivechat_id');
                break;
            // 个性化
            case 'background_image':
                $list = array('user_center_bg', 'admin_center_bg', 'user_center_bg_addr', 'admin_center_bg_addr');
                break;
            // 注册设置
            case 'register':
                $list = array('reg_mode', 'reg_email_verify', 'email_verify_ttl', 'email_verify_ip_limit');
                break;
            // 返利设置
            case 'rebate_mode':
                $list = array('invitation_mode', 'invite_rebate_mode', 'rebate_ratio', 'rebate_frequency_limit', 'rebate_amount_limit', 'rebate_time_range_limit');
                break;
        }
        
        foreach ($list as $item)
        {
            $setting = Setting::where('item', '=', $item)->first();
            
            if ($setting->type == 'array') {
                $setting->value = json_encode($request->getParam("$item"));
            } else {
                $setting->value = $request->getParam("$item");
            }

            if(!$setting->save()) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => "保存 $item 时出错"
                ]);
            }
        }
        
        return $response->withJson([
            'ret' => 1,
            'msg' => "保存成功"
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
                'msg' => '测试邮件发送失败'
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '测试邮件发送成功'
        ]);
    }

    public function return_gateways_list()
    {
        $payment_gateways = array(
            // 网关名 网关代号
            "CoinPay" => "coinpay",
            "当面付" => "f2fpay",
            "PayJs" => "payjs",
            "PaymentWall" => "paymentwall",
            "Stripe" => "stripe",
            "TheadPay" => "theadpay",
            "V免签" => "vmqpay"
        );

        return $payment_gateways;
    }

    public function return_active_gateways()
    {
        $payment_gateways = Setting::where('item', '=', 'payment_gateway')->first();
        $active_gateways = json_decode($payment_gateways->value);
        return $active_gateways;
    }

    public function payment($request, $response, $args)
    {
        $gateway_in_use = array();
        $payment_gateways = self::return_gateways_list();
        foreach ($payment_gateways as $key => $value)
        {
            $payment_switch = $request->getParam("$value");
            if ($payment_switch == '1') {
                array_push($gateway_in_use, $value);
            }
        }

        $gateway = Setting::where('item', '=', 'payment_gateway')->first();
        $gateway->value = json_encode($gateway_in_use);
        $gateway->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => "保存成功"
        ]);
    }
}
