<?php

declare(strict_types=1);

namespace App\Models;

class Token extends Model
{
    protected $connection = 'default';
    protected $table = 'user_token';
}
