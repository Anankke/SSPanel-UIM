<?php

declare(strict_types=1);

namespace App\Models;

final class Link extends Model
{
    protected $connection = 'default';
    protected $table = 'link';

    public function user(): ?User
    {
        return User::find($this->attributes['userid']);
    }

    public function isValid(): bool
    {
        if ($this !== null && $this->user() !== null && $this->user()->is_banned === 0) {
            return true;
        }

        return false;
    }
}
