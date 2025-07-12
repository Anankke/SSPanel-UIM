<?php

declare(strict_types=1);

namespace App\Services;
use Redis;
use function json_encode;
use function time;

/**
 * @property int    $id       记录ID
 * @property string $to_email 收件人邮箱
 * @property string $subject  邮件主题
 * @property string $template 邮件模板
 * @property string $array    模板内容
 * @property int    $time     添加时间
 */
final class EmailQueue
{
    protected $connection = 'redis';
    protected $queueName = 'email_queue';
    private $redis;
    private $ttl = 86400; // 邮件任务默认存活时间为 24 小时（秒）

    public function __construct()
    {
        $this->redis = (new Cache())->initRedis();
    }

    public function add($to, $subject, $template, $array): void
    {
        $emailData = [
            'id' => $this->generateUniqueId(),
            'to_email' => $to,
            'subject' => $subject,
            'template' => $template,
            'array' => json_encode($array),
            'time' => time(),
        ];

        $key = $this->queueName . ':' . $emailData['id'];
        $this->redis->rPush($this->queueName, $key);
        $this->redis->setEx($key, $this->ttl, json_encode($emailData));
    }

    public function count(): int
    {
        return (int) $this->redis->lLen($this->queueName);
    }

    public function pop(): ?array
    {
        $key = $this->redis->lPop($this->queueName);
        if ($key === false) {
            return null;
        }
        $data = $this->redis->get($key);
        $this->redis->del($key); // 删除已处理的邮件数据
        return $data ? json_decode($data, true) : null;
    }
    public function blockingPop(int $timeout = 30): ?array
    {
        $result = $this->redis->brPop([$this->queueName], $timeout);

        if ($result === null || count($result) !== 2) {
            return null;
        }

        [$queue, $key] = $result;
        $data = $this->redis->get($key);
        $this->redis->del($key); // 删除已处理的邮件数据
        return $data ? json_decode($data, true) : null;
    }

    public function where($column, $operator, $value): self
    {
        // TTL 机制自动清理，无需手动 where 过滤
        return $this;
    }

    public function delete(): void
    {
        // 清空整个队列及其关联数据
        $keys = $this->redis->lRange($this->queueName, 0, -1);
        if (!empty($keys)) {
            $this->redis->del($keys); // 删除所有邮件数据
        }
        $this->redis->del($this->queueName); // 删除队列
    }

    private function generateUniqueId(): string
    {
        return uniqid('email_', true);
    }
}