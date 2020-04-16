<?php

namespace App\Models;

/**
 * TelegramTasks Model
 *
 * 请勿重复使用 type
 *
 * type：
 * = 1 删除消息
 * = 2 储存 /setuser 操作产生的 messageid
 *
 */
class TelegramTasks extends Model
{
    protected $connection = 'default';

    protected $table = 'telegram_tasks';

    protected $casts = [
        'type'        => 'int',
        'status'      => 'int',
        'chatid'      => 'int',
        'messageid'   => 'int',
        'userid'      => 'int',
        'tguserid'    => 'int',
        'executetime' => 'int',
        'datetime'    => 'int',
    ];

    public function datetime()
    {
        return date('Y-m-d H:i:s', $this->attributes['datetime']);
    }
}
