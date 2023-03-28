<?php

declare(strict_types=1);

namespace App\Models;

final class DetectBanLog extends Model
{
    protected $connection = 'default';
    protected $table = 'detect_ban_log';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * 封禁结束时间
     */
    public function banEndTime(): string
    {
        return date('Y-m-d H:i:s', $this->end_time + $this->ban_time * 60);
    }
}
