<?php

namespace App\Models;

/**
 * DetectLog Model
 */
class Relay extends Model
{
    protected $connection = 'default';
    protected $table = 'relay';

    public function User()
    {
        $user = User::where('id', $this->attributes['user_id'])->first();
        if ($user == null) {
            self::where('id', '=', $this->attributes['id'])->delete();
            return null;
        }

        return $user;
    }

    public function Source_Node()
    {
        $node = Node::where('id', $this->attributes['source_node_id'])->first();
        if ($node == null && $this->attributes['source_node_id'] != 0) {
            self::where('id', '=', $this->attributes['id'])->delete();
            return null;
        }

        return $node;
    }

    public function Dist_Node()
    {
        if ($this->attributes['dist_node_id'] == -1) {
            return null;
        }

        $node = Node::where('id', $this->attributes['dist_node_id'])->first();
        if ($node == null) {
            self::where('id', '=', $this->attributes['id'])->delete();
            return null;
        }

        return $node;
    }
}
