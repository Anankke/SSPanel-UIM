<?php

namespace App\Models;

/**
 * Ip Model
 */

use App\Utils\Tools;

class Ip extends Model
{
    protected $connection = "default";
    protected $table = "alive_ip";
    
    public function user()
    {
        $user = User::where("id", $this->attributes['userid'])->first();
        if ($user == null) {
            Ip::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $user;
        }
    }

    public function Node()
    {
        $node = Node::where("id", $this->attributes['nodeid'])->first();
        if ($node == null) {
            Ip::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $node;
        }
    }
    
    
    public function ip()
    {
        return str_replace("::ffff:", "", $this->attributes['ip']);
    }
}
