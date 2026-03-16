<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\DB;
use App\Services\Queue\Jobs\SendEmailJob;
use App\Services\Queue\RedisQueue;
use App\Services\Queue\Worker;
use App\Utils\Tools;
use function array_slice;
use function count;
use function in_array;
use function is_numeric;
use function method_exists;
use const PHP_EOL;

/**
 * 队列命令
 *
 * 用于管理 Redis 队列系统
 */
final class Queue extends Command
{
    public string $description = <<<EOL
├─=: php xcat Queue [选项]
│ ├─ work [options]      - 启动队列 Worker
│ │   --queue=default    - 指定监听的队列（可用逗号分隔多个）
│ │   --max-jobs=0       - 最大处理任务数（0=无限制）
│ │   --max-time=0       - 最大运行时间秒数（0=无限制）
│ │   --memory=128       - 内存限制 MB
│ │   --sleep=1000000    - 空闲休眠微秒
│ ├─ status              - 查看队列状态
│ ├─ failed              - 查看失败任务列表
│ ├─ retry [job_id]      - 重试失败任务（不指定ID则重试全部）
│ ├─ flush               - 清空死信队列
│ ├─ recover             - 恢复卡住的任务
│ └─ migrate             - 迁移数据库邮件队列到Redis

EOL;

    public function boot(): void
    {
        if (count($this->argv) < 3) {
            echo $this->description;
            return;
        }

        $action = $this->argv[2];

        match ($action) {
            'work' => $this->work(),
            'status' => $this->status(),
            'failed' => $this->failed(),
            'retry' => $this->retry(),
            'flush' => $this->flush(),
            'recover' => $this->recover(),
            'migrate' => $this->migrate(),
            default => $this->unknownCommand($action),
        };
    }

    /**
     * 启动 Worker
     */
    private function work(): void
    {
        $options = $this->parseOptions(array_slice($this->argv, 3));


        // 验证队列名称
        $validQueues = [
            RedisQueue::QUEUE_DEFAULT,
            RedisQueue::QUEUE_EMAIL,
            RedisQueue::QUEUE_NOTIFICATION,
            RedisQueue::QUEUE_HIGH,
        ];
        if (!isset($options['queue'])) {
            $queues = $validQueues;
        } else {
            $queuesString = $options['queue'];
            $queues = array_map('trim', explode(',', $queuesString));
            foreach ($queues as $queue) {
                if (!in_array($queue, $validQueues, true)) {
                    echo "警告: 队列 '{$queue}' 不是预定义队列，但仍会监听" . PHP_EOL;
                }
            }
        }

        $maxJobs = (int)($options['max-jobs'] ?? 0);
        $maxTime = (int)($options['max-time'] ?? 0);
        $memory = (int)($options['memory'] ?? 128);
        $sleep = (int)($options['sleep'] ?? 1000000);

        echo "启动参数:" . PHP_EOL;
        echo "  队列: " . implode(', ', $queues) . PHP_EOL;
        echo "  最大任务数: " . ($maxJobs > 0 ? $maxJobs : '无限制') . PHP_EOL;
        echo "  最大运行时间: " . ($maxTime > 0 ? $maxTime . 's' : '无限制') . PHP_EOL;
        echo "  内存限制: {$memory}MB" . PHP_EOL;
        echo "  休眠间隔: {$sleep}μs" . PHP_EOL;
        echo PHP_EOL;

        $worker = new Worker($queues, $maxJobs, $maxTime, $memory, $sleep);
        $worker->run();
    }

    /**
     * 查看队列状态
     */
    private function status(): void
    {
        $queue = new RedisQueue();
        $stats = $queue->stats();

        echo PHP_EOL;
        echo "╔═══════════════════════════════════════════════════════════╗" . PHP_EOL;
        echo "║                      队列状态                              ║" . PHP_EOL;
        echo "╠═══════════════════════════════════════════════════════════╣" . PHP_EOL;

        foreach ($stats['queues'] as $name => $data) {
            $total = $data['pending'] + $data['delayed'] + $data['processing'];
            echo sprintf(
                "║ %-12s │ 待处理: %-5d │ 延迟: %-5d │ 处理中: %-5d ║" . PHP_EOL,
                $name,
                $data['pending'],
                $data['delayed'],
                $data['processing']
            );
        }

        echo "╠═══════════════════════════════════════════════════════════╣" . PHP_EOL;
        echo sprintf("║ 死信队列: %-47d ║" . PHP_EOL, $stats['failed']);
        echo "╚═══════════════════════════════════════════════════════════╝" . PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * 查看失败任务
     */
    private function failed(): void
    {
        $queue = new RedisQueue();
        $jobs = $queue->getFailedJobs(50);

        if (empty($jobs)) {
            echo "死信队列为空" . PHP_EOL;
            return;
        }

        echo PHP_EOL;
        echo "失败任务列表:" . PHP_EOL;
        echo str_repeat('-', 100) . PHP_EOL;

        foreach ($jobs as $index => $job) {
            echo sprintf(
                "%d. [%s] %s" . PHP_EOL,
                $index + 1,
                $job['id'] ?? 'unknown',
                $job['class'] ?? 'unknown'
            );
            echo "   队列: " . ($job['queue'] ?? 'unknown') . PHP_EOL;
            echo "   尝试次数: " . ($job['attempts'] ?? 0) . PHP_EOL;
            echo "   错误信息: " . ($job['last_error'] ?? 'unknown') . PHP_EOL;
            echo "   失败时间: " . (isset($job['failed_at']) ? Tools::toDateTime($job['failed_at']) : 'unknown') . PHP_EOL;
            echo str_repeat('-', 100) . PHP_EOL;
        }

        echo PHP_EOL;
    }

    /**
     * 重试失败任务
     */
    private function retry(): void
    {
        $jobId = $this->argv[3] ?? null;
        $queue = new RedisQueue();

        if ($jobId !== null) {
            $count = $queue->retryFailed($jobId);
            if ($count > 0) {
                echo "任务 {$jobId} 已重新加入队列" . PHP_EOL;
            } else {
                echo "未找到任务 {$jobId}" . PHP_EOL;
            }
        } else {
            $count = $queue->retryFailed();
            echo "已将 {$count} 个失败任务重新加入队列" . PHP_EOL;
        }
    }

    /**
     * 清空死信队列
     */
    private function flush(): void
    {
        $queue = new RedisQueue();
        $count = $queue->flushFailed();
        echo "已清空死信队列，删除 {$count} 个任务" . PHP_EOL;
    }

    /**
     * 恢复卡住的任务
     */
    private function recover(): void
    {
        $queue = new RedisQueue();
        $count = $queue->recoverStuckJobs(3600);
        echo "已恢复 {$count} 个卡住的任务" . PHP_EOL;
    }

    /**
     * 迁移数据库邮件队列到 Redis
     */
    private function migrate(): void
    {
        echo "正在迁移数据库邮件队列到 Redis..." . PHP_EOL;

        // 使用数据库操作
        $emails = DB::select('SELECT * FROM email_queue');

        if (empty($emails)) {
            echo "数据库邮件队列为空，无需迁移" . PHP_EOL;
            return;
        }

        $queue = new RedisQueue();
        $count = 0;

        foreach ($emails as $email) {
            $queue->push(
                SendEmailJob::class,
                [
                    'to_email' => $email->to_email,
                    'subject' => $email->subject,
                    'template' => $email->template,
                    'array' => json_decode($email->array, true) ?? [],
                ],
                RedisQueue::QUEUE_EMAIL
            );
            $count++;
        }

        echo "已迁移 {$count} 封邮件到 Redis 队列" . PHP_EOL;
        echo "如需删除数据库中的邮件队列记录，请手动执行: DELETE FROM email_queue" . PHP_EOL;
    }

    /**
     * 未知命令处理
     */
    private function unknownCommand(string $action): void
    {
        echo "未知命令: {$action}" . PHP_EOL . $this->description;
    }

    /**
     * 解析命令行选项
     */
    private function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--')) {
                $parts = explode('=', substr($arg, 2), 2);
                $key = $parts[0];
                $value = $parts[1] ?? true;
                $options[$key] = $value;
            }
        }

        return $options;
    }
}