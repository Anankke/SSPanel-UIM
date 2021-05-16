<?php

namespace App\Models;

use App\Utils\QQWry;

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
}
