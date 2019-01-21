<?php


namespace App\Models;

use App\Utils\Tools;

class BlockIp extends Model
{
    protected $connection = "default";
    protected $table = "blockip";

    
    
    public function node()
    {
        return Node::where("id", $this->attributes['nodeid'])->first();
    }
    
    public function time()
    {
        return date("Y-m-d H:i:s", $this->attributes['datetime']);
    }
}
