<?php

declare(strict_types=1);

namespace App\Models;

/**
 * InviteCode Model
 */
class InviteCode extends Model
{
    protected $connection = 'default';
    protected $table = 'user_invite_code';
}
