<?php

declare(strict_types=1);

namespace App\Command;

/**
 * - 可添加自定义命令
 * - 如需使用 $argv 请继承此类
 * - 无论是否继承都需实现 boot 方法
 */
abstract class Command
{
    public array|null $argv;

    public function __construct($argv)
    {
        $this->argv = $argv;
    }

    abstract public function boot(): void;
}
