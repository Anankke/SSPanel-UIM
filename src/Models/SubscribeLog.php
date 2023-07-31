<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Tools;
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
        } catch (InvalidDatabaseException $e) {
            return '未知';
        }
    }

    /**
     * 记录订阅日志
     */
    public static function add(User $user, string $type, string $ua): void
    {
        $log = new SubscribeLog();
        $antiXss = new AntiXSS();
        $log->user_id = $user->id;
        $log->type = $antiXss->xss_clean($type);
        $log->request_ip = $_SERVER['REMOTE_ADDR'];
        $log->request_user_agent = $antiXss->xss_clean($ua);
        $log->request_time = time();
        $log->save();
    }
}
