<?php


namespace App\Models;

use App\Utils\Tools;

class TrafficLog extends Model
{
    protected $connection = "default";
    protected $table = "user_traffic_log";

    public function node()
    {
        $node = Node::where("id", $this->attributes['node_id'])->first();
        if ($node == null) {
            TrafficLog::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $node;
        }
    }

    public function user()
    {
        $user = User::where("id", $this->attributes['user_id'])->first();
        if ($user == null) {
            TrafficLog::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $user;
        }
    }

    public function totalUsed()
    {
        return Tools::flowAutoShow($this->attributes['u'] + $this->attributes['d']);
    }

    public function totalUsedRaw()
    {
        return number_format(($this->attributes['u'] + $this->attributes['d']) / 1024, 2, ".", "");
    }

    public function logTime()
    {
        return Tools::toDateTime($this->attributes['log_time']);
    }
}
