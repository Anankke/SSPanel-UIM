<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Ticket Model
 */
final class Ticket extends Model
{
    protected $connection = 'default';
    protected $table = 'ticket';

    /**
     * 工单类型
     */
    public function type(): string
    {
        return match ($this->type) {
            'howto' => '使用',
            'billing' => '财务',
            'account' => '账户',
            default => '其他',
        };
    }

    /**
     * 工单状态
     */
    public function status(): string
    {
        return match ($this->status) {
            'closed' => '已结单',
            'open_wait_user' => '等待用户回复',
            'open_wait_admin' => '进行中',
            default => '未知',
        };
    }
}
