<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/.config.php';
require_once __DIR__ . '/config/appprofile.php';
require_once __DIR__ . '/app/predefine.php';

use App\Services\Boot;
use Predis\Client;
use App\Services\Mail;
use App\Utils\Tools;
use Psr\Http\Client\ClientExceptionInterface;

Boot::setTime();
Boot::bootSentry();
Boot::bootDb();

$redis = new Client([
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
]);

$queueName = 'email_queue';

echo Tools::toDateTime(time()) . " 邮件队列消费者启动\n";

function processEmailTask(Client $redis, string $key): void
{
    $dataJson = $redis->get($key);
    if ($dataJson === null) {
        echo Tools::toDateTime(time()) . " 任务数据缺失，跳过 Key：{$key}\n";
        return;
    }

    $redis->del([$key]);

    $email = json_decode($dataJson, true);
    if (!isset($email['to_email']) || !Tools::isEmail($email['to_email'])) {
        echo Tools::toDateTime(time()) . " 邮箱格式错误或缺失，跳过：" . ($email['to_email'] ?? 'null') . "\n";
        return;
    }

    echo Tools::toDateTime(time()) . " 准备发送邮件至：{$email['to_email']}\n";

    try {
        Mail::send(
            $email['to_email'],
            $email['subject'],
            $email['template'],
            json_decode($email['array'], true)
        );
        echo Tools::toDateTime(time()) . " 邮件发送成功：{$email['to_email']}\n";
    } catch (Exception | ClientExceptionInterface $ex) {
        echo Tools::toDateTime(time()) . " 邮件发送失败：" . $ex->getMessage() . "\n";
    }
}

while (true) {
    try {
        $result = $redis->brpop([$queueName], 30);

        if ($result === null) {
            echo Tools::toDateTime(time()) . " 无新任务，继续等待...\n";
            continue;
        }

        [, $key] = $result;
        processEmailTask($redis, $key);

    } catch (Throwable $e) {
        echo Tools::toDateTime(time()) . " 消费进程异常：" . $e->getMessage() . "\n";
        sleep(5);
    }
}
