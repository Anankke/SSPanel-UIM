<?php

declare(strict_types=1);

namespace App\Models;

/**
 * EmailVerify Model
 */
final class EmailVerify extends Model
{
    protected $connection = 'default';
    protected $table = 'email_verify';
}
