<?php

declare(strict_types=1);

namespace App\Services\Queue;

use App\Utils\Tools;
use Exception;
use Throwable;
use function class_exists;
use function class_implements;
use function in_array;
use function pcntl_async_signals;
use function pcntl_signal;
use function time;
use const PHP_EOL;
use const SIGINT;
use const SIGTERM;

/**
 * 队列 Worker
 *
 * 负责从队列中取出任务并执行
 */
final class Worker
{
    private RedisQueue $queue;
    private bool $shouldQuit = false;
    private int $processedJobs = 0;
    private int $failedJobs = 0;

    /**
     * @var string[] 要监听的队列列表
     */
    private array $queues;

    /**
     * @var int 每批次最大处理任务数，0表示无限制
     */
    private int $maxJobs;

    /**
     * @var int 最大运行时间（秒），0表示无限制
     */
    private int $maxTime;

    /**
     * @var int 内存限制（MB），0表示无限制
     */
    private int $memoryLimit;

    /**
     * @var int 任务间休眠时间（微秒）
     */
    private int $sleep;

    private int $startTime;

    public function __construct(
        array $queues = [RedisQueue::QUEUE_DEFAULT],
        int $maxJobs = 0,
        int $maxTime = 0,
        int $memoryLimit = 128,
        int $sleep = 1000000
    ) {
        $this->queue = new RedisQueue();
        $this->queues = $queues;
        $this->maxJobs = $maxJobs;
        $this->maxTime = $maxTime;
        $this->memoryLimit = $memoryLimit;
        $this->sleep = $sleep;
        $this->startTime = time();

        $this->registerSignalHandlers();
    }

    /**
     * 注册信号处理器（优雅关闭）
     */
    private function registerSignalHandlers(): void
    {
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGTERM, fn() => $this->shouldQuit = true);
            pcntl_signal(SIGINT, fn() => $this->shouldQuit = true);
        }
    }

    /**
     * 启动 Worker
     */
    public function run(): void
    {
        echo Tools::toDateTime(time()) . " Worker 启动，监听队列: " . implode(', ', $this->queues) . PHP_EOL;

        while (!$this->shouldQuit) {
            // 检查是否达到限制条件
            if ($this->shouldStop()) {
                break;
            }

            // 轮询所有队列
            $jobProcessed = false;
            foreach ($this->queues as $queueName) {
                $job = $this->queue->pop($queueName, 1);

                if ($job !== null) {
                    $this->processJob($job);
                    $jobProcessed = true;
                    break; // 处理完一个任务后重新开始轮询
                }
            }

            // 如果没有任务，休眠一段时间
            if (!$jobProcessed) {
                usleep($this->sleep);
            }
        }

        $this->shutdown();
    }

    /**
     * 处理单个任务
     */
    private function processJob(array $job): void
    {
        $jobId = $job['id'] ?? 'unknown';
        $jobClass = $job['class'] ?? '';
        $payload = $job['payload'] ?? [];
        $attempts = $job['attempts'] ?? 0;

        echo Tools::toDateTime(time()) . " [处理中] Job: {$jobId} | Class: {$jobClass} | 尝试次数: " . ($attempts + 1) . PHP_EOL;

        try {
            // 验证任务类
            if (!class_exists($jobClass)) {
                throw new Exception("Job class not found: {$jobClass}");
            }

            $implements = class_implements($jobClass);
            if (!in_array(JobInterface::class, $implements, true)) {
                throw new Exception("Job class must implement JobInterface: {$jobClass}");
            }

            // 实例化并执行任务
            $handler = new $jobClass();
            $handler->handle($payload);

            // 任务完成，确认
            $this->queue->acknowledge($job);
            $this->processedJobs++;

            echo Tools::toDateTime(time()) . " [完成] Job: {$jobId}" . PHP_EOL;
        } catch (Throwable $e) {
            echo Tools::toDateTime(time()) . " [失败] Job: {$jobId} | Error: " . $e->getMessage() . PHP_EOL;

            // 调用任务的失败回调
            if (isset($handler) && $handler instanceof JobInterface) {
                try {
                    $handler->failed($payload, $e);
                } catch (Throwable $callbackException) {
                    echo Tools::toDateTime(time()) . " [警告] Failed callback error: " . $callbackException->getMessage() . PHP_EOL;
                }
            }

            // 处理失败（重试或移入死信队列）
            $willRetry = $this->queue->fail($job, $e);
            $this->failedJobs++;

            if ($willRetry) {
                echo Tools::toDateTime(time()) . " [重试] Job: {$jobId} 将在稍后重试" . PHP_EOL;
            } else {
                echo Tools::toDateTime(time()) . " [死信] Job: {$jobId} 已移入死信队列" . PHP_EOL;
            }
        }
    }

    /**
     * 检查是否应该停止
     */
    private function shouldStop(): bool
    {
        // 检查最大任务数
        if ($this->maxJobs > 0 && $this->processedJobs >= $this->maxJobs) {
            echo Tools::toDateTime(time()) . " 已达到最大任务数限制 ({$this->maxJobs})" . PHP_EOL;
            return true;
        }

        // 检查最大运行时间
        if ($this->maxTime > 0 && (time() - $this->startTime) >= $this->maxTime) {
            echo Tools::toDateTime(time()) . " 已达到最大运行时间限制 ({$this->maxTime}s)" . PHP_EOL;
            return true;
        }

        // 检查内存限制
        $memoryUsage = memory_get_usage(true) / 1024 / 1024;
        if ($this->memoryLimit > 0 && $memoryUsage >= $this->memoryLimit) {
            echo Tools::toDateTime(time()) . " 已达到内存限制 ({$this->memoryLimit}MB)" . PHP_EOL;
            return true;
        }

        return false;
    }

    /**
     * 关闭 Worker
     */
    private function shutdown(): void
    {
        $runtime = time() - $this->startTime;
        $memoryPeak = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        echo PHP_EOL;
        echo Tools::toDateTime(time()) . " Worker 关闭" . PHP_EOL;
        echo "运行时间: {$runtime}s | 处理任务: {$this->processedJobs} | 失败任务: {$this->failedJobs} | 峰值内存: {$memoryPeak}MB" . PHP_EOL;

        $this->queue->close();
    }

    /**
     * 获取统计信息
     */
    public function getStats(): array
    {
        return [
            'processed' => $this->processedJobs,
            'failed' => $this->failedJobs,
            'runtime' => time() - $this->startTime,
            'memory_peak' => memory_get_peak_usage(true),
        ];
    }
}
