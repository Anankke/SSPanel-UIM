<?php

declare(strict_types=1);

namespace App\Models;

use function time;

/**
 * Ip Model
 */
final class LoginIp extends Model
{
    protected $connection = 'default';
    protected $table = 'login_ip';

    /**
     * 登录用户
     */
    public function user(): ?User
    {
        return User::find($this->userid);
    }

    /**
     * 登录用户名
     */
    public function userName(): string
    {
        return $this->user() === null ? '用户不存在' : $this->user()->user_name;
    }

    /**
     * 登录成功与否
     */
    public function type(): string
    {
        return $this->type === 0 ? '成功' : '失败';
    }

    /**
     * 记录登录 IP
     *
     * @param string $ip IP
     * @param int $type 1 = failed, 0 = success
     * @param int $user_id User ID
     *
     * @return void
     */
    public function collectLoginIP(string $ip, int $type = 0, int $user_id = 0): void
    {
        if (Setting::obtain('login_log')) {
            $this->ip = $ip;
            $this->userid = $user_id;
            $this->datetime = time();
            $this->type = $type;
            $this->save();
        }
    }
}
