<?php

namespace App\Command;

use App\Utils\QQWry;
use App\Models\Setting;
use App\Utils\DatatablesHelper;

class Tool extends Command
{
    public $description = ''
        . '├─=: php xcat Tool [选项]' . PHP_EOL
        . '│ ├─ initQQWry               - 下载 IP 解析库' . PHP_EOL
        . '│ ├─ setTelegram             - 设置 Telegram 机器人' . PHP_EOL
        . '│ ├─ detectConfigs           - 检查数据库内新增的配置' . PHP_EOL
        . '│ ├─ resetAllSettings        - 使用默认值覆盖设置中心设置' . PHP_EOL
        . '│ ├─ exportAllSettings       - 导出所有设置' . PHP_EOL
        . '│ ├─ importAllSettings       - 导入所有设置' . PHP_EOL;

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
    
    public function initQQWry()
    {
        echo ('正在下载或更新纯真ip数据库...') . PHP_EOL;
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
                echo ('纯真ip数据库下载成功.') . PHP_EOL;
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
                echo ('纯真ip数据库保存失败，请检查权限') . PHP_EOL;
            }
        } else {
            echo ('纯真ip数据库下载失败，请检查下载地址') . PHP_EOL;
        }
    }
    
    public function detectConfigs()
    {
        echo \App\Services\DefaultConfig::detectConfigs();
    }
    
    public function resetAllSettings()
    {
        $settings = Setting::all();
        
        foreach ($settings as $setting)
        {
            $setting->value = $setting->default;
            $setting->save();
        }

        echo '已使用默认值覆盖所有设置.' . PHP_EOL;
    }

    public function exportAllSettings()
    {
        $settings = Setting::all();
        foreach ($settings as $setting)
        {
            // 因为主键自增所以即便设置为 null 也会在导入时自动分配 id
            // 同时避免多位开发者 pull request 时 settings.json 文件 id 重复所可能导致的冲突
            $setting->id = null;
            // 避免开发者调试配置泄露
            $setting->value = $setting->default;
        }
        
        $json_settings = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents('./config/settings.json', $json_settings);

        echo '已导出所有设置.' . PHP_EOL;
    }

    public function importAllSettings()
    {
        $db = new DatatablesHelper();
        
        $json_settings = file_get_contents('./config/settings.json');
        $settings      = json_decode($json_settings, true);
        $number        = count($settings);
        $counter       = '0';
        
        for ($i = 0; $i < $number; $i++)
        {
            $item = $settings[$i]['item'];
            
            if ($db->query("SELECT id FROM config WHERE item = '$item'") == null) {
                $new_item            = new Setting;
                $new_item->id        = null;
                $new_item->item      = $settings[$i]['item'];
                $new_item->value     = $settings[$i]['value'];
                $new_item->class     = $settings[$i]['class'];
                $new_item->is_public = $settings[$i]['is_public'];
                $new_item->type      = $settings[$i]['type'];
                $new_item->default   = $settings[$i]['default'];
                $new_item->mark      = $settings[$i]['mark'];
                $new_item->save();
                
                echo "添加新设置：$item" . PHP_EOL;
                $counter += 1;
            }
        }

        if ($counter != '0') {
            echo "总计添加了 $counter 条新设置." . PHP_EOL;
        } else {
            echo "没有任何新设置需要添加." . PHP_EOL;
        }
    }
}
