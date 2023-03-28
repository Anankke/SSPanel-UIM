<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Code Model
 */
final class Code extends Model
{
    protected $connection = 'default';
    protected $table = 'code';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->userid);
    }
}
