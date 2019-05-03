<?php

namespace App\Models;

/**
 * Ticket Model
 */
class Ticket extends Model
{
    protected $connection = "default";
    protected $table = "ticket";

    public function datetime()
    {
        return date("Y-m-d H:i:s", $this->attributes['datetime']);
    }

    public function User()
    {
        $user = User::where("id", $this->attributes['userid'])->first();
        if ($user == null) {
            Ticket::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $user;
        }
    }
}
