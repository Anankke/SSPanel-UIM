<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Config;
use Illuminate\Database\DatabaseManager;
use Smarty;

final class View
{
    public static DatabaseManager $connection;
    public static float $beginTime;

    public static function getSmarty(): Smarty
    {
        $smarty = new Smarty(); //实例化smarty

        $user = Auth::getUser();

        if ($user->isLogin) {
            $theme = $user->theme;
        } else {
            $theme = $_ENV['theme'];
        }

        $smarty->settemplatedir(BASE_PATH . '/resources/views/' . $theme . '/'); //设置模板文件存放目录
        $smarty->setcompiledir(BASE_PATH . '/storage/framework/smarty/compile/'); //设置生成文件存放目录
        $smarty->setcachedir(BASE_PATH . '/storage/framework/smarty/cache/'); //设置缓存文件存放目录
        // add config
        $smarty->assign('config', self::getConfig());
        $smarty->assign('public_setting', Config::getPublicConfig());
        $smarty->assign('user', $user);

        return $smarty;
    }

    public static function getConfig(): array
    {
        return [
            'appName' => $_ENV['appName'],
            'baseUrl' => $_ENV['baseUrl'],

            'enable_checkin' => $_ENV['enable_checkin'],
            'checkinMin' => $_ENV['checkinMin'],
            'checkinMax' => $_ENV['checkinMax'],

            'jump_delay' => $_ENV['jump_delay'],

            'enable_kill' => $_ENV['enable_kill'],
            'enable_change_email' => $_ENV['enable_change_email'],

            'enable_r2_client_download' => $_ENV['enable_r2_client_download'],

            'jsdelivr_url' => $_ENV['jsdelivr_url'],
        ];
    }
}
