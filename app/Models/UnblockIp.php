<?php


namespace App\Models;

use App\Utils\Tools;

class UnblockIp extends Model
{
    protected $connection = "default";
    protected $table = "unblockip";

    public function user()
    {
        $user = User::where("id", $this->attributes['userid'])->first();
        if ($user == null) {
            UnblockIp::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $user;
        }
    }

    public function time()
    {
        return date("Y-m-d H:i:s", $this->attributes['datetime']);
    }
}
