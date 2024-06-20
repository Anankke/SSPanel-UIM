<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\SysLog\DBHandler;
use Monolog\Logger;
use function count;
use function method_exists;
use const PHP_EOL;

final class Test extends Command
{
    public string $description = <<<EOL
├─=: php xcat Test [arg]
│ └─ generateSysLog - 生成系统日志

EOL;

    public function boot(): void
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                echo '方法不存在' . PHP_EOL;
            }
        }
    }

    public function generateSysLog(): void
    {
        $message = '测试日志';
        $context = [
            'user_id' => 1,
            'ip' => '127.0.0.1',
        ];

        $logger = new Logger('admin');
        $logger->pushHandler(new DBHandler());
        $logger->debug($message, $context);
    }
}
