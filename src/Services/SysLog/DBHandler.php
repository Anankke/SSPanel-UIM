<?php

declare(strict_types=1);

namespace App\Services\SysLog;

use App\Models\SysLog;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use function json_encode;

final class DBHandler extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        (new SysLog())->insert([
            'user_id' => $record->context['user_id'] ?? 0,
            'ip' => $record->context['ip'] ?? '',
            'message' => $record->message,
            'level' => $record->level,
            'context' => json_encode($record->context),
            'channel' => $record->channel,
            'datetime' => $record->datetime->format('U'),
        ]);
    }
}
