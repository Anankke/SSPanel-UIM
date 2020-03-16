<?php

namespace App\Models;

class NodeInfoLog extends Model
{
    protected $connection = 'default';
    protected $table = 'ss_node_info';

    public function getNodeLoad()
    {
        $load = $this->attributes['load'];
        $exp = explode(' ', $load);
        return $exp[0];
    }

    public function getTime()
    {
        $time = $this->attributes['log_time'];
        return date('Y-m-d H:i:s', $time);
    }
}
