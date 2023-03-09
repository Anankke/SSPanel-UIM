<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Code Model
 */
final class Code extends Model
{
    protected $connection = 'default';

    protected $table = 'code';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->userid);
    }

    /**
     * 用户 ID
     */
    public function userid(): int
    {
        return $this->userid;
    }

    /**
     * 用户名
     */
    public function userName(): string
    {
        if ($this->userid === 0) {
            return '未使用';
        }
        if ($this->user() === null) {
            return '用户已不存在';
        }
        return $this->user()->user_name;
    }

    /**
     * 类型
     */
    public function type(): string
    {
        return match ($this->type) {
            -1 => '充值金额',
            -2 => '财务支出',
            default => '已经废弃',
        };
    }

    /**
     * 操作
     */
    public function number(): string
    {
        return match ($this->type) {
            -1 => '充值 ' . $this->number . ' 元',
            -2 => '支出 ' . $this->number . ' 元',
            default => '已经废弃',
        };
    }

    /**
     * 是否已经使用
     */
    public function isused(): string
    {
        return $this->isused === 1 ? '已使用' : '未使用';
    }

    /**
     * 使用时间
     */
    public function usedatetime(): string
    {
        return $this->usedatetime > '2000-1-1 0:0:0' ? $this->usedatetime : '未使用';
    }
}
