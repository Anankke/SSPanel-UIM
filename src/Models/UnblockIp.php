<?php

namespace App\Models;

use App\Utils\QQWry;

class UnblockIp extends Model
{
    protected $connection = 'default';

    protected $table = 'unblockip';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->userid);
    }

    /**
     * 用户名
     */
    public function user_name(): string
    {
        if ($this->user() == null) {
            return '用户已不存在';
        }
        return $this->user()->user_name;
    }

    /**
     * 获取 IP 位置
     *
     * @param QQWry $QQWry
     */
    public function location(QQWry $QQWry = null): string
    {
        if ($QQWry === null) {
            $QQWry = new QQWry();
        }
        $location = $QQWry->getlocation($this->ip);
        return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
    }

    /**
     * 时间
     */
    public function datetime(): string
    {
        return date('Y-m-d H:i:s', $this->datetime);
    }
}
