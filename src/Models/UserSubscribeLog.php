<?php

namespace App\Models;

use App\Utils\QQWry;

use voku\helper\AntiXSS;

class UserSubscribeLog extends Model
{
    protected $connection = 'default';

    protected $table = 'user_subscribe_log';

    /**
     * [静态方法] 删除不存在的用户的记录
     *
     * @param UserSubscribeLog $UserSubscribeLog
     */
    public static function user_is_null($UserSubscribeLog): void
    {
        self::where('user_id', $UserSubscribeLog->user_id)->delete();
    }

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * 获取 IP 位置
     *
     * @param QQWry $QQWry
     */
    public function location(QQWry $QQWry = null)
    {
        if ($QQWry === null) {
            $QQWry = new QQWry();
        }
        $location = $QQWry->getlocation($this->request_ip);
        return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
    }

    /**
     * 记录订阅日志
     *
     * @param User   $user 用户
     * @param string $type 订阅类型
     * @param string $ua   UA
     *
     * @return void
     */
    public static function addSubscribeLog($user, $type, $ua)
    {
        $log                     = new UserSubscribeLog();
        $log->user_name          = $user->user_name;
        $log->user_id            = $user->id;
        $log->email              = $user->email;
        $log->subscribe_type     = $type;
        $log->request_ip         = $_SERVER['REMOTE_ADDR'];
        $log->request_time       = date('Y-m-d H:i:s');
        $antiXss                 = new AntiXSS();
        $log->request_user_agent = $antiXss->xss_clean($ua);
        $log->save();
    }
}
