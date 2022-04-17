<?php
namespace App\Models;

class TelegramSession extends Model
{
    protected $connection = 'default';
    protected $table = 'telegram_session';

    public function datetime()
    {
        return date('Y-m-d H:i:s', $this->attributes['datetime']);
    }

    public function User()
    {
        $user = User::where('id', $this->attributes['user_id'])->first();
        if ($user == null) {
            return null;
        }

        return $user;
    }
}
