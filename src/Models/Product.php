<?php

declare(strict_types=1);

namespace App\Models;

final class Product extends Model
{
    protected $connection = 'default';
    protected $table = 'product';
}
