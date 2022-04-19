<?php

declare(strict_types=1);

namespace App\Models;

/**
 * EmailVerify Model
 */
final class EmailQueue extends Model
{
    protected $connection = 'default';
    protected $table = 'email_queue';
}
