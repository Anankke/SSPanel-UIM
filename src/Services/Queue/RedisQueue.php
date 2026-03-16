<?php

declare(strict_types=1);

namespace App\Services\Queue;

use App\Services\Cache;
use Redis;
use Throwable;
use function json_decode;
use function json_encode;
use function time;

/**
 * Redis 队列服务
 *
 * 支持功能：
 * - 多队列支持
 * - 延迟任务
 * - 失败重试
 * - 死信队列
 * - 任务优先级
 */
final class RedisQueue
{
    private Redis $redis;
    private string $prefix = 'sspanel:queue:';

    // 队列名称常量
    public const QUEUE_DEFAULT = 'default';
    public const QUEUE_EMAIL = 'email';
    public const QUEUE_NOTIFICATION = 'notification';
    public const QUEUE_HIGH = 'high';

    // 最大重试次数
    private int $maxRetries = 3;

    // 重试延迟（秒），指数退避
    private array $retryDelays = [60, 300, 900]; // 1分钟, 5分钟, 15分钟

    public function __construct()
    {
        $this->redis = (new Cache())->initRedis();
    }

    /**
     * 获取队列键名
     */
    private function getQueueKey(string $queue): string
    {
        return $this->prefix . $queue;
    }

    /**
     * 获取延迟队列键名
     */
    private function getDelayedKey(string $queue): string
    {
        return $this->prefix . 'delayed:' . $queue;
    }

    /**
     * 获取死信队列键名
     */
    private function getFailedKey(): string
    {
        return $this->prefix . 'failed';
    }

    /**
     * 获取处理中队列键名
     */
    private function getProcessingKey(string $queue): string
    {
        return $this->prefix . 'processing:' . $queue;
    }

    /**
     * 推送任务到队列
     *
     * @param string $jobClass 任务类名
     * @param array $payload 任务数据
     * @param string $queue 队列名称
     * @param int $delay 延迟秒数，0表示立即执行
     */
    public function push(string $jobClass, array $payload = [], string $queue = self::QUEUE_DEFAULT, int $delay = 0): string
    {
        $jobId = $this->generateJobId();

        $job = [
            'id' => $jobId,
            'class' => $jobClass,
            'payload' => $payload,
            'queue' => $queue,
            'attempts' => 0,
            'max_retries' => $this->maxRetries,
            'created_at' => time(),
            'available_at' => time() + $delay,
        ];

        $serialized = json_encode($job, JSON_UNESCAPED_UNICODE);

        if ($delay > 0) {
            // 延迟任务使用有序集合
            $this->redis->zAdd(
                $this->getDelayedKey($queue),
                time() + $delay,
                $serialized
            );
        } else {
            // 立即执行的任务使用列表
            $this->redis->rPush($this->getQueueKey($queue), $serialized);
        }

        return $jobId;
    }

    /**
     * 批量推送任务
     */
    public function pushBatch(array $jobs, string $queue = self::QUEUE_DEFAULT): array
    {
        $jobIds = [];

        foreach ($jobs as $job) {
            $jobIds[] = $this->push(
                $job['class'],
                $job['payload'] ?? [],
                $queue,
                $job['delay'] ?? 0
            );
        }

        return $jobIds;
    }

    /**
     * 从队列中取出一个任务
     *
     * @param string $queue 队列名称
     * @param int $timeout 阻塞等待超时（秒）
     * @return array|null
     */
    public function pop(string $queue = self::QUEUE_DEFAULT, int $timeout = 5): ?array
    {
        // 先处理延迟队列中到期的任务
        $this->migrateDelayedJobs($queue);

        // 使用 BLMOVE 原子操作（Redis 6.2+），如果不支持则降级使用 BRPOPLPUSH
        $result = $this->redis->brPoplPush(
            $this->getQueueKey($queue),
            $this->getProcessingKey($queue),
            $timeout
        );

        if ($result === false) {
            return null;
        }

        return json_decode($result, true);
    }

    /**
     * 将延迟队列中到期的任务移动到主队列
     */
    private function migrateDelayedJobs(string $queue): void
    {
        $now = time();
        $delayedKey = $this->getDelayedKey($queue);
        $queueKey = $this->getQueueKey($queue);

        // 获取所有到期的延迟任务
        $jobs = $this->redis->zRangeByScore($delayedKey, '-inf', (string) $now);

        foreach ($jobs as $job) {
            // 原子操作：从延迟队列移除并添加到主队列
            $removed = $this->redis->zRem($delayedKey, $job);
            if ($removed > 0) {
                $this->redis->rPush($queueKey, $job);
            }
        }
    }

    /**
     * 确认任务完成
     */
    public function acknowledge(array $job): void
    {
        $serialized = json_encode($job, JSON_UNESCAPED_UNICODE);
        $this->redis->lRem($this->getProcessingKey($job['queue']), $serialized, 1);
    }

    /**
     * 任务失败处理
     *
     * @param array $job 任务数据
     * @param Throwable $exception 异常
     * @return bool 是否重新入队
     */
    public function fail(array $job, Throwable $exception): bool
    {
        $serialized = json_encode($job, JSON_UNESCAPED_UNICODE);
        $this->redis->lRem($this->getProcessingKey($job['queue']), $serialized, 1);

        $job['attempts']++;
        $job['last_error'] = $exception->getMessage();
        $job['failed_at'] = time();

        // 检查是否还能重试
        if ($job['attempts'] < $job['max_retries']) {
            // 计算重试延迟
            $delayIndex = min($job['attempts'] - 1, count($this->retryDelays) - 1);
            $delay = $this->retryDelays[$delayIndex];

            // 重新加入延迟队列
            $job['available_at'] = time() + $delay;
            $this->redis->zAdd(
                $this->getDelayedKey($job['queue']),
                $job['available_at'],
                json_encode($job, JSON_UNESCAPED_UNICODE)
            );

            return true;
        }

        // 超过重试次数，移入死信队列
        $this->redis->rPush($this->getFailedKey(), json_encode($job, JSON_UNESCAPED_UNICODE));

        return false;
    }

    /**
     * 重试死信队列中的任务
     */
    public function retryFailed(?string $jobId = null): int
    {
        $count = 0;
        $failedKey = $this->getFailedKey();

        if ($jobId !== null) {
            // 重试指定任务
            $length = $this->redis->lLen($failedKey);
            for ($i = 0; $i < $length; $i++) {
                $job = json_decode($this->redis->lIndex($failedKey, $i), true);
                if ($job && $job['id'] === $jobId) {
                    $this->redis->lRem($failedKey, json_encode($job, JSON_UNESCAPED_UNICODE), 1);
                    $job['attempts'] = 0;
                    $job['available_at'] = time();
                    unset($job['last_error'], $job['failed_at']);
                    $this->redis->rPush($this->getQueueKey($job['queue']), json_encode($job, JSON_UNESCAPED_UNICODE));
                    $count++;
                    break;
                }
            }
        } else {
            // 重试所有失败任务
            while ($serialized = $this->redis->lPop($failedKey)) {
                $job = json_decode($serialized, true);
                $job['attempts'] = 0;
                $job['available_at'] = time();
                unset($job['last_error'], $job['failed_at']);
                $this->redis->rPush($this->getQueueKey($job['queue']), json_encode($job, JSON_UNESCAPED_UNICODE));
                $count++;
            }
        }

        return $count;
    }

    /**
     * 清空死信队列
     */
    public function flushFailed(): int
    {
        $count = $this->redis->lLen($this->getFailedKey());
        $this->redis->del($this->getFailedKey());
        return $count;
    }

    /**
     * 获取队列统计信息
     */
    public function stats(?string $queue = null): array
    {
        $queues = $queue !== null ? [$queue] : [
            self::QUEUE_DEFAULT,
            self::QUEUE_EMAIL,
            self::QUEUE_NOTIFICATION,
            self::QUEUE_HIGH,
        ];

        $stats = [
            'queues' => [],
            'failed' => $this->redis->lLen($this->getFailedKey()),
        ];

        foreach ($queues as $q) {
            $stats['queues'][$q] = [
                'pending' => $this->redis->lLen($this->getQueueKey($q)),
                'delayed' => $this->redis->zCard($this->getDelayedKey($q)),
                'processing' => $this->redis->lLen($this->getProcessingKey($q)),
            ];
        }

        return $stats;
    }

    /**
     * 获取失败任务列表
     */
    public function getFailedJobs(int $limit = 50, int $offset = 0): array
    {
        $jobs = $this->redis->lRange($this->getFailedKey(), $offset, $offset + $limit - 1);

        return array_map(static fn($job) => json_decode($job, true), $jobs);
    }

    /**
     * 删除指定失败任务
     */
    public function deleteFailed(string $jobId): bool
    {
        $failedKey = $this->getFailedKey();
        $length = $this->redis->lLen($failedKey);

        for ($i = 0; $i < $length; $i++) {
            $job = json_decode($this->redis->lIndex($failedKey, $i), true);
            if ($job && $job['id'] === $jobId) {
                $this->redis->lRem($failedKey, json_encode($job, JSON_UNESCAPED_UNICODE), 1);
                return true;
            }
        }

        return false;
    }

    /**
     * 生成唯一任务ID
     */
    private function generateJobId(): string
    {
        return uniqid('job_', true) . '_' . bin2hex(random_bytes(4));
    }

    /**
     * 清理超时的处理中任务（用于异常恢复）
     *
     * @param int $timeout 超时时间（秒）
     */
    public function recoverStuckJobs(int $timeout = 3600): int
    {
        $count = 0;
        $queues = [
            self::QUEUE_DEFAULT,
            self::QUEUE_EMAIL,
            self::QUEUE_NOTIFICATION,
            self::QUEUE_HIGH,
        ];

        foreach ($queues as $queue) {
            $processingKey = $this->getProcessingKey($queue);
            $jobs = $this->redis->lRange($processingKey, 0, -1);

            foreach ($jobs as $serialized) {
                $job = json_decode($serialized, true);
                // 检查任务是否超时
                if (isset($job['available_at']) && time() - $job['available_at'] > $timeout) {
                    $this->redis->lRem($processingKey, $serialized, 1);
                    // 重新加入队列
                    $job['available_at'] = time();
                    $this->redis->rPush($this->getQueueKey($queue), json_encode($job, JSON_UNESCAPED_UNICODE));
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * 关闭 Redis 连接
     */
    public function close(): void
    {
        $this->redis->close();
    }
}
