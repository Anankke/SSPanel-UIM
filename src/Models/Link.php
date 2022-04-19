<?php

declare(strict_types=1);

namespace App\Models;

final class Link extends Model
{
    protected $connection = 'default';
    protected $table = 'link';

    public function getUser(): ?User
    {
        return User::find($this->attributes['userid']);
    }
}
