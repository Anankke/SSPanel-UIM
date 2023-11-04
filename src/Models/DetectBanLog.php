<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Tools;
use Illuminate\Database\Query\Builder;

/**
 * @property int    $id                封禁记录ID
 * todo: delete this
 * @property string $user_name         用户名
 * @property int    $user_id           用户ID
 * todo: delete this as well
 * @property string $email             用户邮箱
 * @property int    $detect_number     本次违规次数
 * @property int    $ban_time          封禁时长
 * @property int    $start_time        封禁开始时间
 * @property int    $end_time          封禁结束时间
 * @property int    $all_detect_number 累计违规次数
 *
 * @mixin Builder
 */
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
        return Tools::toDateTime($this->end_time + $this->ban_time * 60);
    }
}
