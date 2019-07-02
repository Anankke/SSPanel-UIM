<?php


namespace App\Models;

class UnblockIp extends Model
{
    protected $connection = 'default';
    protected $table = 'unblockip';

    public function user()
    {
        $user = User::where('id', $this->attributes['userid'])->first();
        if ($user == null) {
            self::where('id', '=', $this->attributes['id'])->delete();
            return null;
        }

        return $user;
    }

    public function time()
    {
        return date('Y-m-d H:i:s', $this->attributes['datetime']);
    }
}
