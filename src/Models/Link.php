<?php

namespace App\Models;

class Link extends Model
{
    protected $connection = 'default';
    protected $table = 'link';

    public function getUser(): ?User
    {
        return User::find($this->attributes['userid']);
    }
}
