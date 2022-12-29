<?php

declare(strict_types=1);

namespace App\Models;

final class Order extends Model
{
    protected $connection = 'default';
    protected $table = 'order';
}
