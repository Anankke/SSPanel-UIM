<?php

namespace App\Models;

/**
 * DetectLog Model
 */

use App\Utils\Tools;

class DetectLog extends Model
{
    protected $connection = "default";
    protected $table = "detect_log";

    public function DetectRule()
    {
        $rule = DetectRule::where("id", $this->attributes['list_id'])->first();
        if ($rule == null) {
            DetectLog::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $rule;
        }
    }

    public function User()
    {
        $user = User::where("id", $this->attributes['user_id'])->first();
        if ($user == null) {
            DetectLog::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $user;
        }
    }

    public function Node()
    {
        $node = Node::where("id", $this->attributes['node_id'])->first();
        if ($node == null) {
            DetectLog::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $node;
        }
    }
}
