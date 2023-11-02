<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int $id           记录ID
 * @property int $user_id      用户ID
 * @property int $traffic      当前总流量
 * @property int $hourly_usage 过去一小时流量
 * @property int $datetime     记录时间
 *
 * @mixin Builder
 */
final class UserHourlyUsage extends Model
{
    protected $connection = 'default';
    protected $table = 'user_hourly_usage';
}
