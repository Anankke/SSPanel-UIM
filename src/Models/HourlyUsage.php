<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;
use function array_fill;
use function date;
use function json_decode;
use function json_encode;

/**
 * @property int $id           记录ID
 * @property int $user_id      用户ID
 * @property string $date      记录日期
 * @property string $usage     流量用量
 *
 * @mixin Builder
 */
final class HourlyUsage extends Model
{
    protected $connection = 'default';
    protected $table = 'hourly_usage';

    public function add(int $user_id, int $usage): void
    {
        $hour = (int) date('H');
        $date = date('Y-m-d');
        $exist_usage = $this->where('user_id', $user_id)->where('date', $date)->first();

        if ($exist_usage === null) {
            $new_usage_array = array_fill(0, 24, 0);
            $new_usage_array[$hour] = $usage;
            $this->user_id = $user_id;
            $this->date = $date;
            $this->usage = json_encode($new_usage_array);
            $this->save();
        } else {
            $exist_usage_array = json_decode($exist_usage->usage, true);
            $exist_usage_array[$hour] += $usage;
            $exist_usage->usage = json_encode($exist_usage_array);
            $exist_usage->save();
        }
    }
}
