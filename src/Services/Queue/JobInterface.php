<?php

declare(strict_types=1);

namespace App\Services\Queue;

/**
 * 队列任务接口
 *
 * 所有队列任务都必须实现此接口
 */
interface JobInterface
{
    /**
     * 执行任务
     *
     * @param array $payload 任务数据
     * @return void
     * @throws \Exception 任务执行失败时抛出异常
     */
    public function handle(array $payload): void;

    /**
     * 获取任务所属队列
     *
     * @return string
     */
    public static function getQueue(): string;

    /**
     * 任务失败时的回调
     *
     * @param array $payload 任务数据
     * @param \Throwable $exception 异常
     * @return void
     */
    public function failed(array $payload, \Throwable $exception): void;
}