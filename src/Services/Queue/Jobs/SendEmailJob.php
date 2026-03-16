<?php

declare(strict_types=1);

namespace App\Services\Queue\Jobs;

use App\Services\Mail;
use App\Services\Queue\JobInterface;
use App\Services\Queue\RedisQueue;
use App\Utils\Tools;
use Throwable;
use function json_encode;

/**
 * 邮件发送任务
 */
final class SendEmailJob implements JobInterface
{
    /**
     * 执行邮件发送
     *
     * @param array $payload 包含 to_email, subject, template, array
     */
    public function handle(array $payload): void
    {
        $toEmail = $payload['to_email'];
        $subject = $payload['subject'];
        $template = $payload['template'];
        $templateData = $payload['array'] ?? [];

        // 验证邮箱格式
        if (!Tools::isEmail($toEmail)) {
            throw new \InvalidArgumentException("Invalid email address: {$toEmail}");
        }

        Mail::send($toEmail, $subject, $template, $templateData);
    }

    /**
     * 获取任务所属队列
     */
    public static function getQueue(): string
    {
        return RedisQueue::QUEUE_EMAIL;
    }

    /**
     * 任务失败时的回调
     */
    public function failed(array $payload, Throwable $exception): void
    {
        // 记录失败日志
        error_log(sprintf(
            '[SendEmailJob] Failed to send email to %s: %s | Payload: %s',
            $payload['to_email'] ?? 'unknown',
            $exception->getMessage(),
            json_encode($payload, JSON_UNESCAPED_UNICODE)
        ));
    }

    /**
     * 快捷方法：将邮件任务加入队列
     */
    public static function dispatch(
        string $toEmail,
        string $subject,
        string $template,
        array $templateData = [],
        int $delay = 0
    ): string {
        $queue = new RedisQueue();

        return $queue->push(
            self::class,
            [
                'to_email' => $toEmail,
                'subject' => $subject,
                'template' => $template,
                'array' => $templateData,
            ],
            self::getQueue(),
            $delay
        );
    }

    /**
     * 批量发送邮件
     */
    public static function dispatchBatch(array $emails): array
    {
        $queue = new RedisQueue();
        $jobs = [];

        foreach ($emails as $email) {
            $jobs[] = [
                'class' => self::class,
                'payload' => [
                    'to_email' => $email['to_email'],
                    'subject' => $email['subject'],
                    'template' => $email['template'],
                    'array' => $email['array'] ?? [],
                ],
                'delay' => $email['delay'] ?? 0,
            ];
        }

        return $queue->pushBatch($jobs, self::getQueue());
    }
}