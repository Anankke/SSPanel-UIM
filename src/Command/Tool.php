<?php

namespace App\Command;

class Tool extends Command
{
    public $description = ''
        . '├─=: php xcat Tool [选项]' . PHP_EOL
        . '│ ├─ initQQWry               - 下载 IP 解析库' . PHP_EOL
        . '│ ├─ setTelegram             - 设置 Telegram 机器人' . PHP_EOL
        . '│ ├─ initdownload            - 下载 SSR 程序至服务器' . PHP_EOL
        . '│ ├─ detectConfigs           - 检查数据库内新增的配置' . PHP_EOL
        . '│ ├─ initdocuments           - 下载用户使用文档至服务器' . PHP_EOL;

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
                echo ('New Bot @' . $telegram->getMe()->getUsername() . ' 设置成功！');
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
     * 下载客户端
     *
     * @return void
     */
    public function initdownload()
    {
        system('git clone --depth=3 https://github.com/xcxnig/ssr-download.git ' . BASE_PATH . '/public/ssr-download/ && git gc', $ret);
        echo $ret;
    }

    /**
     * 下载使用文档
     *
     * @return void
     */
    public function initdocuments()
    {
        system('git clone https://github.com/GeekQuerxy/PANEL_DOC.git ' . BASE_PATH . "/public/docs/", $ret);
        echo $ret;
    }

    /**
     * 下载 IP 库
     *
     * @return void
     */
    public function initQQWry()
    {
        echo ('开始下载纯真 IP 数据库....');
        $qqwry = file_get_contents('https://qqwry.mirror.noc.one/QQWry.Dat?from=sspanel_uim');
        if ($qqwry != '') {
            $fp = fopen(BASE_PATH . '/storage/qqwry.dat', 'wb');
            if ($fp) {
                fwrite($fp, $qqwry);
                fclose($fp);
                echo ('纯真 IP 数据库下载成功！');
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
