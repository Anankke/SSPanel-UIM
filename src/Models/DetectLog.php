<?php

namespace App\Models;

class DetectLog extends Model
{
    protected $connection = 'default';

    protected $table = 'detect_log';

    /**
     * [静态方法] 删除不存在的节点的记录
     *
     * @param DetectLog $DetectLog
     */
    public static function node_is_null($DetectLog): void
    {
        self::where('node_id', $DetectLog->node_id)->delete();
    }

    /**
     * [静态方法] 删除不存在的规则的记录
     *
     * @param DetectLog $DetectLog
     */
    public static function rule_is_null($DetectLog): void
    {
        self::where('list_id', $DetectLog->list_id)->delete();
    }

    /**
     * [静态方法] 删除不存在的用户的记录
     *
     * @param DetectLog $DetectLog
     */
    public static function user_is_null($DetectLog): void
    {
        self::where('user_id', $DetectLog->user_id)->delete();
    }

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
        return Node::find($this->node_id);
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
     * 规则
     */
    public function rule(): ?DetectRule
    {
        return DetectRule::find($this->list_id);
    }

    /**
     * 规则名
     */
    public function rule_name(): string
    {
        if ($this->rule() == null) {
            return '规则已不存在';
        }
        return $this->rule()->name;
    }

    /**
     * 规则描述
     */
    public function rule_text(): string
    {
        if ($this->rule() == null) {
            return '规则已不存在';
        }
        return $this->rule()->text;
    }

    /**
     * 规则正则表达式
     */
    public function rule_regex(): string
    {
        if ($this->rule() == null) {
            return '规则已不存在';
        }
        return $this->rule()->regex;
    }

    /**
     * 规则类型
     */
    public function rule_type(): string
    {
        if ($this->rule() == null) {
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
