<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Node;
use App\Models\Setting;
use App\Utils\QQWry;
use App\Utils\Tools;

final class Tool extends Command
{
    public $description = <<<EOL
├─=: php xcat Tool [选项]
│ ├─ initQQWry               - 下载 IP 解析库
│ ├─ setTelegram             - 设置 Telegram 机器人
│ ├─ resetAllSettings        - 使用默认值覆盖设置中心设置
│ ├─ exportAllSettings       - 导出所有设置
│ ├─ importAllSettings       - 导入所有设置
│ ├─ upgradeDatabase         - 升级(如果不存在的话初始化) 数据库
│ ├─ resetNodePassword       - 重置所有节点通讯密钥
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
                echo '方法不存在.' . PHP_EOL;
            }
        }
    }

    public function initQQWry(): void
    {
        echo '正在下载或更新纯真ip数据库...' . PHP_EOL;
        $path = BASE_PATH . '/storage/qqwry.dat';
        $qqwry = file_get_contents('https://qqwry.mirror.noc.one/QQWry.Dat?from=sspanel_uim');
        if ($qqwry !== '') {
            if (is_file($path)) {
                rename($path, $path . '.bak');
            }
            $fp = fopen($path, 'wb');
            if ($fp) {
                fwrite($fp, $qqwry);
                fclose($fp);
                echo '纯真ip数据库下载成功.' . PHP_EOL;
                $iplocation = new QQWry();
                $location = $iplocation->getlocation('8.8.8.8');
                $Userlocation = $location['country'];
                if (iconv('gbk', 'utf-8//IGNORE', $Userlocation) !== '美国') {
                    unlink($path);
                    if (is_file($path . '.bak')) {
                        rename($path . '.bak', $path);
                    }
                }
            } else {
                echo '纯真ip数据库保存失败，请检查权限' . PHP_EOL;
            }
        } else {
            echo '纯真ip数据库下载失败，请检查下载地址' . PHP_EOL;
        }
    }

    public function setTelegram(): void
    {
        $WebhookUrl = $_ENV['baseUrl'] . '/telegram_callback?token=' . $_ENV['telegram_request_token'];
        $telegram = new \Telegram\Bot\Api($_ENV['telegram_token']);
        $telegram->removeWebhook();
        if ($telegram->setWebhook(['url' => $WebhookUrl])) {
            echo 'Bot @' . $telegram->getMe()->getUsername() . ' 设置成功！' . PHP_EOL;
        } else {
            echo '设置失败！' . PHP_EOL;
        }
    }

    public function resetAllSettings(): void
    {
        $settings = Setting::all();

        foreach ($settings as $setting) {
            $setting->value = $setting->default;
            $setting->save();
        }

        echo '已使用默认值覆盖所有设置.' . PHP_EOL;
    }

    public function exportAllSettings(): void
    {
        $settings = Setting::all();
        foreach ($settings as $setting) {
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

    public function importAllSettings(): void
    {
        $json_settings = file_get_contents('./config/settings.json');
        $settings = json_decode($json_settings, true);
        $counter = 0;

        foreach ($settings as $item) {
            $itemName = $item['item'];

            $query = Setting::where('item', '=', $item['item'])->first();

            if ($query === null) {
                $new_item = new Setting();
                $new_item->id = null;
                $new_item->item = $item['item'];
                $new_item->value = $item['value'];
                $new_item->class = $item['class'];
                $new_item->is_public = $item['is_public'];
                $new_item->type = $item['type'];
                $new_item->default = $item['default'];
                $new_item->mark = $item['mark'];
                $new_item->save();

                echo "添加新设置：${itemName}" . PHP_EOL;
                $counter += 1;
            }
        }

        if ($counter !== 0) {
            echo "总计添加了 ${counter} 条新设置." . PHP_EOL;
        } else {
            echo '没有任何新设置需要添加.' . PHP_EOL;
        }
    }

    public function upgradeDatabase(): void
    {
        $phinx = new \Phinx\Console\PhinxApplication();
        $phinx->run();
    }

    public function resetNodePassword(): void
    {
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $node->password = Tools::genRandomChar(32);
            $node->save();
        }
        echo '已重置所有节点密码.' . PHP_EOL;
    }
}
