<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\DB;
use function strval;

/**
 * Ip Model
 */
final class Ip extends Model
{
    protected $connection = 'default';
    protected $table = 'alive_ip';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->userid);
    }

    /**
     * 用户名
     */
    public function userName(): string
    {
        if ($this->user() === null) {
            return '用户已不存在';
        }
        return $this->user()->user_name;
    }

    /**
     * 节点
     */
    public function node(): ?Node
    {
        return Node::find($this->nodeid);
    }

    /**
     * 节点名
     */
    public function nodeName(): string
    {
        if ($this->node() === null) {
            return '节点已不存在';
        }
        return $this->node()->name;
    }

    /**
     * 时间
     */
    public function datetime(): string
    {
        return date('Y-m-d H:i:s', $this->datetime);
    }

    public function getUserAliveIpCount(): array
    {
        $pdo = DB::getPdo();
        $res = [];
        foreach ($pdo->query('SELECT `userid`, COUNT(DISTINCT `ip`) AS `count` FROM `alive_ip` WHERE `datetime` >= UNIX_TIMESTAMP(NOW()) - 60 GROUP BY `userid`') as $line) {
            $res[strval($line['userid'])] = $line['count'];
        }
        return $res;
    }

    public function ip(): array|string
    {
        return str_replace('::ffff:', '', $this->attributes['ip']);
    }
}
