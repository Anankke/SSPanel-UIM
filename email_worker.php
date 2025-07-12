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

// 初始化 Redis 连接
$redis = new Client([
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
]);

try {
    $redis->connect();
    echo Tools::toDateTime(time()) . " Redis 连接成功\n";
} catch (Exception $e) {
    echo Tools::toDateTime(time()) . " Redis 连接失败：" . $e->getMessage() . "\n";
    \Sentry\captureException($e);
    exit(1);
}

$queueName = 'email_queue';

echo Tools::toDateTime(time()) . " 邮件队列消费者启动\n";

function processEmailTask(Client $redis, string $key, string $queueName): void
{
    $dataJson = $redis->get($key);
    if ($dataJson === null) {
        echo Tools::toDateTime(time()) . " 任务数据缺失，跳过 Key：{$key}\n";
        return;
    }

    echo Tools::toDateTime(time()) . " 获取任务数据：{$key}, 数据：" . substr($dataJson, 0, 100) . "...\n";

    $email = json_decode($dataJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo Tools::toDateTime(time()) . " JSON 解析错误：{$key}, 错误：" . json_last_error_msg() . "\n";
        $redis->del([$key]);
        \Sentry\captureMessage("JSON 解析错误：Key={$key}, 错误=" . json_last_error_msg());
        return;
    }

    if (!isset($email['to_email']) || !Tools::isEmail($email['to_email'])) {
        echo Tools::toDateTime(time()) . " 邮箱格式错误或缺失，跳过：" . ($email['to_email'] ?? 'null') . "\n";
        $redis->del([$key]);
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
        $redis->del([$key]);
    } catch (Exception | ClientExceptionInterface $ex) {
        echo Tools::toDateTime(time()) . " 邮件发送失败：{$email['to_email']}, 错误：" . $ex->getMessage() . "\n";
        \Sentry\captureException($ex);
        $redis->lpush($queueName, $key);
        echo Tools::toDateTime(time()) . " 任务已重新入队：{$key}\n";
        sleep(1);
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
        echo Tools::toDateTime(time()) . " 从队列获取任务：{$key}\n";
        processEmailTask($redis, $key, $queueName);

    } catch (Throwable $e) {
        echo Tools::toDateTime(time()) . " 消费进程异常：" . $e->getMessage() . "\n";
        \Sentry\captureException($e);
        sleep(5);
    }
}