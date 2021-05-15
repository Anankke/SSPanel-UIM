<?php

namespace App\Models;

use App\Utils\QQWry;

/**
 * Ip Model
 */
class LoginIp extends Model
{
    protected $connection = 'default';

    protected $table = 'login_ip';

    protected $casts = [
        'type' => 'int',
    ];

    /**
     * [静态方法] 删除不存在的用户的记录
     *
     * @param LoginIp $LoginIp
     */
    public static function user_is_null($LoginIp): void
    {
        self::where('userid', $LoginIp->userid)->delete();
    }

    /**
     * 登录用户
     */
    public function user(): ?User
    {
        return User::find($this->userid);
    }

    /**
     * 登录用户
     */
    public function user_name(): string
    {
        if ($this->user() == null) {
            return '用户已不存在';
        }
        return $this->user()->user_name;
    }

    /**
     * 登录时间
     */
    public function datetime(): string
    {
        return date('Y-m-d H:i:s', $this->datetime);
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
     * 登录成功与否
     */
    public function type(): string
    {
        return $this->type == 0 ? '成功' : '失败';
    }
}
