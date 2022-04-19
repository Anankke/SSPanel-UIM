<?php

declare(strict_types=1);

namespace App\Models;

final class UserHourlyUsage extends Model
{
    protected $connection = 'default';
    protected $table = 'user_hourly_usage';
}
