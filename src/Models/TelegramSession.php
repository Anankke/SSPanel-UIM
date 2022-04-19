<?php

declare(strict_types=1);

namespace App\Models;

/**
 * TelegramSession Model
 */
final class TelegramSession extends Model
{
    protected $connection = 'default';
    protected $table = 'telegram_session';

    public function datetime()
    {
        return date('Y-m-d H:i:s', $this->attributes['datetime']);
    }
}
