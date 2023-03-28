<?php

declare(strict_types=1);

namespace App\Models;

final class DetectLog extends Model
{
    protected $connection = 'default';
    protected $table = 'detect_log';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
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
        return Node::find($this->node_id);
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
     * 规则
     */
    public function rule(): ?DetectRule
    {
        return DetectRule::find($this->list_id);
    }

    /**
     * 规则名
     */
    public function ruleName(): string
    {
        if ($this->rule() === null) {
            return '规则已不存在';
        }
        return $this->rule()->name;
    }

    /**
     * 规则描述
     */
    public function ruleText(): string
    {
        if ($this->rule() === null) {
            return '规则已不存在';
        }
        return $this->rule()->text;
    }

    /**
     * 规则正则表达式
     */
    public function ruleRegex(): string
    {
        if ($this->rule() === null) {
            return '规则已不存在';
        }
        return $this->rule()->regex;
    }

    /**
     * 规则类型
     */
    public function ruleType(): string
    {
        if ($this->rule() === null) {
            return '规则已不存在';
        }
        return $this->rule()->type();
    }

    /**
     * 时间
     */
    public function datetime(): string
    {
        return date('Y-m-d H:i:s', $this->datetime);
    }
}
