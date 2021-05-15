<?php

namespace App\Models;

class DetectBanLog extends Model
{
    protected $connection = "default";

    protected $table = "detect_ban_log";

    /**
     * [静态方法] 删除不存在的用户的记录
     *
     * @param DetectBanLog $DetectBanLog
     */
    public static function user_is_null($DetectBanLog): void
    {
        self::where('user_id', $DetectBanLog->user_id)->delete();
    }

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * 统计开始时间
     */
    public function start_time(): string
    {
        return date('Y-m-d H:i:s', $this->start_time);
    }

    /**
     * 统计结束以及封禁开始时间
     */
    public function end_time(): string
    {
        return date('Y-m-d H:i:s', $this->end_time);
    }

    /**
     * 封禁结束时间
     */
    public function ban_end_time(): string
    {
        return date('Y-m-d H:i:s', $this->ban_end_time + $this->ban_time * 60);
    }
}
