<?php

namespace App\Controllers;

use App\Models\InviteCode;
use App\Models\User;
use App\Models\Code;
use App\Models\Payback;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\Config;
use App\Utils\Tools;
use App\Utils\Telegram;
use App\Utils\Tuling;
use App\Utils\TelegramSessionManager;
use App\Utils\QRcode;
use App\Utils\Pay;
use App\Utils\TelegramProcess;
use App\Utils\Spay_tool;

/**
 *  HomeController
 */
class HomeController extends BaseController
{
    public function index()
    {
        return $this->view()->display('index.tpl');
    }

    public function code()
    {
        $codes = InviteCode::where('user_id', '=', '0')->take(10)->get();
        return $this->view()->assign('codes', $codes)->display('code.tpl');
    }

    public function down()
    {
    }

    public function tos()
    {
        return $this->view()->display('tos.tpl');
    }
    
    public function staff()
    {
        return $this->view()->display('staff.tpl');
    }
    
    public function telegram($request, $response, $args)
    {
        $token = "";
        if (isset($request->getQueryParams()["token"])) {
            $token = $request->getQueryParams()["token"];
        }
        
        if ($token == Config::get('telegram_request_token')) {
            TelegramProcess::process();
        } else {
            echo("不正确请求！");
        }
    }
    
    public function page404($request, $response, $args)
    {
        return $this->view()->display('404.tpl');
    }
    
    public function page405($request, $response, $args)
    {
        return $this->view()->display('405.tpl');
    }
    
    public function page500($request, $response, $args)
    {
		return $this->view()->display('500.tpl');
    }
    
    public function codepay_callback($request, $response, $args)
    {
        echo '
            <script>
               window.location.href="/user/code";
            </script>
            ';
        return;
    }
  
    public function pay_callback($request, $response, $args)
    {
        Pay::callback($request);
    }
  
    public function f2fpay_pay_callback($request, $response, $args)
    {
        Pay::f2fpay_pay_callback($request);
    }
  
    public function codepay_pay_callback($request, $response, $args)
    {
        Pay::codepay_pay_callback($request);
    }
}
