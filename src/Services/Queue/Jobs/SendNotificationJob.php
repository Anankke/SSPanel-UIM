<?php

declare(strict_types=1);

namespace App\Services\Queue\Jobs;

use App\Services\IM;
use App\Services\Queue\JobInterface;
use App\Services\Queue\RedisQueue;
use Throwable;
use function json_encode;

/**
 * IM 通知任务
 */
final class SendNotificationJob implements JobInterface
{
    /**
     * 执行通知发送
     *
     * @param array $payload 包含 im_value, message, im_type
     */
    public function handle(array $payload): void
    {
        $imValue = $payload['im_value'];
        $message = $payload['message'];
        $imType = $payload['im_type'] ?? 0;

        IM::send($imValue, $message, $imType);
    }

    /**
     * 获取任务所属队列
     */
    public static function getQueue(): string
    {
        return RedisQueue::QUEUE_NOTIFICATION;
    }

    /**
     * 任务失败时的回调
     */
    public function failed(array $payload, Throwable $exception): void
    {
        error_log(sprintf(
            '[SendNotificationJob] Failed to send notification: %s | Payload: %s',
            $exception->getMessage(),
            json_encode($payload, JSON_UNESCAPED_UNICODE)
        ));
    }

    /**
     * 快捷方法：将通知任务加入队列
     */
    public static function dispatch(
        mixed $imValue,
        string $message,
        int $imType = 0,
        int $delay = 0
    ): string {
        $queue = new RedisQueue();

        return $queue->push(
            self::class,
            [
                'im_value' => $imValue,
                'message' => $message,
                'im_type' => $imType,
            ],
            self::getQueue(),
            $delay
        );
    }
}