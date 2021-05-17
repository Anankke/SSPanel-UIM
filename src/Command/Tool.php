<?php

namespace App\Command;

use App\Utils\QQWry;

class Tool extends Command
{
    public $description = ''
        . '├─=: php xcat Tool [选项]' . PHP_EOL
        . '│ ├─ initQQWry               - 下载 IP 解析库' . PHP_EOL
        . '│ ├─ setTelegram             - 设置 Telegram 机器人' . PHP_EOL
        . '│ ├─ detectConfigs           - 检查数据库内新增的配置' . PHP_EOL;

    public function boot()
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                echo '方法不存在.' . PHP_EOL;
            }
        }
    }

    /**
     * 设定 Telegram Bot
     *
     * @return void
     */
    public function setTelegram()
    {
        if ($_ENV['use_new_telegram_bot'] === true) {
            $WebhookUrl = ($_ENV['baseUrl'] . '/telegram_callback?token=' . $_ENV['telegram_request_token']);
            $telegram = new \Telegram\Bot\Api($_ENV['telegram_token']);
            $telegram->removeWebhook();
            if ($telegram->setWebhook(['url' => $WebhookUrl])) {
                echo ('New Bot @' . $telegram->getMe()->getUsername() . ' 设置成功！' . PHP_EOL);
            }
        } else {
            $bot = new \TelegramBot\Api\BotApi($_ENV['telegram_token']);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf('https://api.telegram.org/bot%s/deleteWebhook', $_ENV['telegram_token']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $deleteWebhookReturn = json_decode(curl_exec($ch));
            curl_close($ch);
            if ($deleteWebhookReturn->ok && $deleteWebhookReturn->result && $bot->setWebhook($_ENV['baseUrl'] . '/telegram_callback?token=' . $_ENV['telegram_request_token']) == 1) {
                echo ('Old Bot 设置成功！' . PHP_EOL);
            }
        }
    }

    /**
     * 下载 IP 库
     *
     * @return void
     */
    public function initQQWry()
    {
        echo ('开始下载或更新纯真 IP 数据库....');
        $path  = BASE_PATH . '/storage/qqwry.dat';
        $qqwry = file_get_contents('https://qqwry.mirror.noc.one/QQWry.Dat?from=sspanel_uim');
        if ($qqwry != '') {
            if (is_file($path)) {
                rename($path, $path . '.bak');
            }
            $fp = fopen($path, 'wb');
            if ($fp) {
                fwrite($fp, $qqwry);
                fclose($fp);
                echo ('纯真 IP 数据库下载成功！');
                $iplocation   = new QQWry();
                $location     = $iplocation->getlocation('8.8.8.8');
                $Userlocation = $location['country'];
                if (iconv('gbk', 'utf-8//IGNORE', $Userlocation) !== '美国') {
                    unlink($path);
                    if (is_file($path . '.bak')) {
                        rename($path . '.bak', $path);
                    }
                }
            } else {
                echo ('纯真 IP 数据库保存失败！');
            }
        } else {
            echo ('下载失败！请重试，或在 https://github.com/SukkaW/qqwry-mirror/issues/new 反馈！');
        }
    }

    /**
     * 探测新增配置
     *
     * @return void
     */
    public function detectConfigs()
    {
        echo \App\Services\DefaultConfig::detectConfigs();
    }
}
