<?php

declare(strict_types=1);

namespace App\Services\Queue;

use App\Models\EmailQueue;
use App\Services\Queue\Jobs\SendEmailJob;
use App\Services\Queue\Jobs\SendNotificationJob;

/**
 * 队列门面类
 *
 * 提供统一的队列操作接口，自动根据配置选择使用 Redis 或数据库队列
 */
final class Queue
{
    /**
     * 判断是否使用 Redis 队列
     */
    public static function useRedis(): bool
    {
        return (bool) ($_ENV['enable_redis_queue'] ?? false);
    }

    /**
     * 发送邮件（自动选择队列）
     */
    public static function email(
        string $toEmail,
        string $subject,
        string $template,
        array $templateData = [],
        int $delay = 0
    ): void {
        if (self::useRedis()) {
            SendEmailJob::dispatch($toEmail, $subject, $template, $templateData, $delay);
        } else {
            // 使用数据库队列（不支持延迟）
            (new EmailQueue())->add($toEmail, $subject, $template, $templateData);
        }
    }

    /**
     * 批量发送邮件
     */
    public static function emailBatch(array $emails): void
    {
        if (self::useRedis()) {
            SendEmailJob::dispatchBatch($emails);
        } else {
            foreach ($emails as $email) {
                (new EmailQueue())->add(
                    $email['to_email'],
                    $email['subject'],
                    $email['template'],
                    $email['array'] ?? []
                );
            }
        }
    }

    /**
     * 发送通知（仅 Redis 队列支持）
     */
    public static function notification(
        mixed $imValue,
        string $message,
        int $imType = 0,
        int $delay = 0
    ): void {
        if (self::useRedis()) {
            SendNotificationJob::dispatch($imValue, $message, $imType, $delay);
        } else {
            // 数据库队列不支持通知，直接发送
            \App\Services\IM::send($imValue, $message, $imType);
        }
    }

    /**
     * 推送自定义任务到队列
     *
     * @param string $jobClass 任务类名（必须实现 JobInterface）
     * @param array $payload 任务数据
     * @param string $queue 队列名称
     * @param int $delay 延迟秒数
     */
    public static function push(
        string $jobClass,
        array $payload = [],
        string $queue = RedisQueue::QUEUE_DEFAULT,
        int $delay = 0
    ): ?string {
        if (!self::useRedis()) {
            throw new \RuntimeException('Custom jobs require Redis queue to be enabled');
        }

        $redisQueue = new RedisQueue();
        return $redisQueue->push($jobClass, $payload, $queue, $delay);
    }

    /**
     * 获取队列状态
     */
    public static function stats(): array
    {
        if (self::useRedis()) {
            $redisQueue = new RedisQueue();
            return $redisQueue->stats();
        }

        // 数据库队列统计
        return [
            'queues' => [
                'email' => [
                    'pending' => (new EmailQueue())->count(),
                    'delayed' => 0,
                    'processing' => 0,
                ],
            ],
            'failed' => 0,
        ];
    }
}