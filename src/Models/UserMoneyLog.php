<?php

declare(strict_types=1);

namespace App\Models;

use function time;

final class UserMoneyLog extends Model
{
    protected $connection = 'default';
    protected $table = 'user_money_log';

    public function addMoneyLog(int $user_id, float $before, float $after, float $amount, string $remark): void
    {
        $this->user_id = $user_id;
        $this->before = $before;
        $this->after = $after;
        $this->amount = $amount;
        $this->remark = $remark;
        $this->create_time = time();
        $this->save();
    }
}
