<?php

namespace App\Models;

use App\Utils\QQWry;

class BlockIp extends Model
{
    protected $connection = 'default';

    protected $table = 'blockip';

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
        $location = $QQWry->getlocation($this->ip);
        return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
    }

    /**
     * 时间
     */
    public function datetime(): string
    {
        return date('Y-m-d H:i:s', $this->datetime);
    }
}
