<?php

namespace App\Services;

use Smarty;

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
        
        
        $smarty->settemplatedir(BASE_PATH.'/resources/views/'.$theme.'/'); //设置模板文件存放目录
        $smarty->setcompiledir(BASE_PATH.'/storage/framework/smarty/compile/'); //设置生成文件存放目录
        $smarty->setcachedir(BASE_PATH.'/storage/framework/smarty/cache/'); //设置缓存文件存放目录
        // add config
        $smarty->assign('config', Config::getPublicConfig());
        $smarty->assign('user', Auth::getUser());
        return $smarty;
    }
}
