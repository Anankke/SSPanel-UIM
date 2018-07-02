<?php

namespace App\Services;

use Smarty;
use App\Utils;

class View
{
    public static function getSmarty()
    {
        $smarty=new smarty(); //实例化smarty
        
        $user = Auth::getUser();
        
        if ($user->isLogin) {
            $theme=$user->theme;
        } else {
            $theme=Config::get('theme');
        }
        
        $can_backtoadmin = 0;
        if (Utils\Cookie::get('old_uid') && Utils\Cookie::get('old_email') && Utils\Cookie::get('old_key') && Utils\Cookie::get('old_ip') && Utils\Cookie::get('old_expire_in') && Utils\Cookie::get('old_local')) {
            $can_backtoadmin = 1;
        }
        $smarty->settemplatedir(BASE_PATH.'/resources/views/'.$theme.'/'); //设置模板文件存放目录
        $smarty->setcompiledir(BASE_PATH.'/storage/framework/smarty/compile/'); //设置生成文件存放目录
        $smarty->setcachedir(BASE_PATH.'/storage/framework/smarty/cache/'); //设置缓存文件存放目录
        // add config
        $smarty->assign('config', Config::getPublicConfig());
        $smarty->assign('user', Auth::getUser());
        $smarty->assign('can_backtoadmin', $can_backtoadmin);
        return $smarty;
    }
}
