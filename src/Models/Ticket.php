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
}
