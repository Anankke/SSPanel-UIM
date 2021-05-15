<?php

namespace App\Models;

use App\Utils\QQWry;
use App\Utils\Tools;

/**
 * Ip Model
 */
class Ip extends Model
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
    public function user_name(): string
    {
        if ($this->user() == null) {
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
    public function node_name(): string
    {
        if ($this->node() == null) {
            return '节点已不存在';
        }
        return $this->node()->name;
    }

    /**
     * 获取 IP 位置
     *
     * @param QQWry $QQWry
     */
    public function location(QQWry $QQWry = null): string
    {
        if ($QQWry === null) {
            $QQWry = new QQWry();
        }
        $location = $QQWry->getlocation(Tools::getRealIp($this->ip));
        return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
    }

    /**
     * 时间
     */
    public function datetime(): string
    {
        return date('Y-m-d H:i:s', $this->datetime);
    }

    /**
     * 是否为中转连接
     */
    public function is_node(): string
    {
        return Node::where('node_ip', Tools::getRealIp($this->ip))->first() ? '是' : '否';
    }

    public function getUserAliveIpCount($userid)
    {
        return count(self::where('userid', '=', $userid)->where('datetime', '>=', time() - 60)->get());
    }

    public function ip()
    {
        return str_replace('::ffff:', '', $this->attributes['ip']);
    }
}
