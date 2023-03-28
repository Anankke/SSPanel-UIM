<?php

declare(strict_types=1);

namespace App\Models;

use voku\helper\AntiXSS;

final class UserSubscribeLog extends Model
{
    protected $connection = 'default';
    protected $table = 'user_subscribe_log';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * 记录订阅日志
     *
     * @param User   $user 用户
     * @param string $type 订阅类型
     * @param string $ua   UA
     */
    public static function addSubscribeLog(User $user, string $type, string $ua): void
    {
        $log = new UserSubscribeLog();
        $antiXss = new AntiXSS();
        $log->user_name = $user->user_name;
        $log->user_id = $user->id;
        $log->email = $user->email;
        $log->subscribe_type = $antiXss->xss_clean($type);
        $log->request_ip = $_SERVER['REMOTE_ADDR'];
        $log->request_time = date('Y-m-d H:i:s');
        $log->request_user_agent = $antiXss->xss_clean($ua);
        $log->save();
    }
}
