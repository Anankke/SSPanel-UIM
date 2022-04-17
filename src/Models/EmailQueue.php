<?php

namespace App\Models;

/**
 * EmailVerify Model
 */
class EmailQueue extends Model
{
    protected $connection = 'default';
    protected $table = 'email_queue';
}
