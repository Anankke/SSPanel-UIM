<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Tools;
use Exception;
use MaxMind\Db\Reader\InvalidDatabaseException;
use voku\helper\AntiXSS;
use function time;

final class SubscribeLog extends Model
{
    protected $connection = 'default';
    protected $table = 'subscribe_log';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Ip 地理位置
     */
    public function getLocationAttribute(): string
    {
        try {
            return Tools::getIpLocation($this->request_ip);
        } catch (InvalidDatabaseException|Exception $e) {
            return '未知';
        }
    }

    /**
     * 记录订阅日志
     */
    public function add(User $user, string $type, string $ua): void
    {
        $antiXss = new AntiXSS();
        $this->user_id = $user->id;
        $this->type = $antiXss->xss_clean($type);
        $this->request_ip = $_SERVER['REMOTE_ADDR'];
        $this->request_user_agent = $antiXss->xss_clean($ua);
        $this->request_time = time();
        $this->save();
    }
}
