<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id       '记录ID'
 * @property string $user_id  '触发用户'
 * @property string $ip       '触发IP'
 * @property string $message  '日志内容'
 * @property int    $level    '日志等级'
 * @property string $context  '日志内容'
 * @property string $channel  '日志类别'
 * @property int    $datetime '记录时间'
 *
 * @mixin Builder
 */
final class SysLog extends Model
{
    protected $connection = 'default';
    protected $table = 'syslog';

    public function level(): string
    {
        return match ($this->level) {
            200 => 'INFO',
            250 => 'NOTICE',
            300 => 'WARNING',
            400 => 'ERROR',
            500 => 'CRITICAL',
            550 => 'ALERT',
            600 => 'EMERGENCY',
            999 => 'KABOOM',
            default => 'DEBUG',
        };
    }

    public function channel(): string
    {
        return match ($this->channel) {
            'cron' => '计划任务',
            'sub' => '订阅',
            'auth' => '认证',
            'user' => '用户',
            'admin' => '管理员',
            default => '未知',
        };
    }
}
