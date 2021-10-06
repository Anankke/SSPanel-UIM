<?php

namespace App\Models;

class PasswordReset extends Model
{
    protected $connection = 'default';
    protected $table = 'user_password_reset';

    /**
     * 获取对应用户
     */
    public function getUser(): ?User
    {
        return User::where('email', $this->email)->first();
    }
}
