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
     * [静态方法] 删除不存在的用户的记录
     */
    public static function userIsNull(Ticket $Ticket): void
    {
        $tickets = Ticket::where('userid', $Ticket->userid)->get();
        foreach ($tickets as $ticket) {
            $ticket->delete();
        }
    }

    /**
     * 时间
     */
    public function datetime(): string
    {
        return date('Y-m-d H:i:s', $this->datetime);
    }

    /**
     * 工单状态
     */
    public function status(): string
    {
        if ($this->status === 'closed') {
            return '已结单';
        }
        if ($this->status === 'open_wait_user') {
            return '等待用户回复';
        }
        if ($this->status === 'open_wait_admin') {
            return '进行中';
        }
        return '未知';
    }
}
