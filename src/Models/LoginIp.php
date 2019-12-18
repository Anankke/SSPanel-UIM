<?php

namespace App\Models;

/**
 * Ip Model
 */
class LoginIp extends Model
{
    protected $connection = 'default';
    protected $table = 'login_ip';

    public function user()
    {
        $user = User::where('id', $this->attributes['userid'])->first();
        if ($user == null) {
            self::where('id', '=', $this->attributes['id'])->delete();
            return null;
        }

        return $user;
    }

    public function datetime()
    {
        return date('Y-m-d H:i:s', $this->attributes['datetime']);
    }
}
