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
        $pics=scandir(BASE_PATH."/public/theme/".(Auth::getUser()->isLogin==false?Config::get("theme"):Auth::getUser()->theme)."/images/error/404/");
        
        if (count($pics)>2) {
            $pic=$pics[rand(2, count($pics)-1)];
        } else {
            $pic="4041.png";
        }
        
        $newResponse = $response->withStatus(404);
        $newResponse->getBody()->write($this->view()->assign("pic", "/theme/".(Auth::getUser()->isLogin==false?Config::get("theme"):Auth::getUser()->theme)."/images/error/404/".$pic)->display('404.tpl'));
        return $newResponse;
    }
    
    public function page405($request, $response, $args)
    {
        $pics=scandir(BASE_PATH."/public/theme/".(Auth::getUser()->isLogin==false?Config::get("theme"):Auth::getUser()->theme)."/images/error/405/");
        if (count($pics)>2) {
            $pic=$pics[rand(2, count($pics)-1)];
        } else {
            $pic="4051.png";
        }
        
        $newResponse = $response->withStatus(405);
        $newResponse->getBody()->write($this->view()->assign("pic", "/theme/".(Auth::getUser()->isLogin==false?Config::get("theme"):Auth::getUser()->theme)."/images/error/405/".$pic)->display('405.tpl'));
        return $newResponse;
    }
    
    public function page500($request, $response, $args)
    {
        $pics=scandir(BASE_PATH."/public/theme/".(Auth::getUser()->isLogin==false?Config::get("theme"):Auth::getUser()->theme)."/images/error/500/");
        if (count($pics)>2) {
            $pic=$pics[rand(2, count($pics)-1)];
        } else {
            $pic="5001.png";
        }
        
        $newResponse = $response->withStatus(500);
        $newResponse->getBody()->write($this->view()->assign("pic", "/theme/".(Auth::getUser()->isLogin==false?Config::get("theme"):Auth::getUser()->theme)."/images/error/500/".$pic)->display('500.tpl'));
        return $newResponse;
    }
    
    public function pay_callback($request, $response, $args)
    {
        Pay::callback($request);
    }
}
