<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Ip Model
 */
final class LoginIp extends Model
{
    protected $connection = 'default';

    protected $table = 'login_ip';

    /**
     * [静态方法] 删除不存在的用户的记录
     */
    public static function userIsNull(LoginIp $LoginIp): void
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
    public function userName(): string
    {
        if ($this->user() === null) {
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
     * 登录成功与否
     */
    public function type(): string
    {
        return $this->type === 0 ? '成功' : '失败';
    }
}
