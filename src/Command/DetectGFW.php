<?php

namespace App\Command;

use App\Models\{
    Node,
    User
};
use App\Services\Config;
use App\Utils\Telegram;

class DetectGFW extends Command
{
    public $description = '├─=: php xcat DetectGFW      - 节点被墙检测定时任务' . PHP_EOL;

    public function boot()
    {
        //节点被墙检测
        $last_time = file_get_contents(BASE_PATH . '/storage/last_detect_gfw_time');
        for ($count = 1; $count <= 12; $count++) {
            if (time() - $last_time >= $_ENV['detect_gfw_interval']) {
                $file_interval = fopen(BASE_PATH . '/storage/last_detect_gfw_time', 'wb');
                fwrite($file_interval, time());
                fclose($file_interval);
                $nodes = Node::all();
                $adminUser = User::where('is_admin', '=', '1')->get();
                foreach ($nodes as $node) {
                    if (
                        $node->node_ip == '' ||
                        $node->node_ip == null ||
                        $node->online == false
                    ) {
                        continue;
                    }
                    $api_url = $_ENV['detect_gfw_url'];
                    $api_url = str_replace(
                        array('{ip}', '{port}'),
                        array($node->node_ip, $_ENV['detect_gfw_port']),
                        $api_url
                    );
                    //因为考虑到有v2ray之类的节点，所以不得不使用ip作为参数
                    $result_tcping = false;
                    $detect_time = $_ENV['detect_gfw_count'];
                    for ($i = 1; $i <= $detect_time; $i++) {
                        $json_tcping = json_decode(file_get_contents($api_url), true);
                        if (eval('return ' . $_ENV['detect_gfw_judge'] . ';')) {
                            $result_tcping = true;
                            break;
                        }
                    }
                    if ($result_tcping == false) {
                        //被墙了
                        echo ($node->id . ':false' . PHP_EOL);
                        //判断有没有发送过邮件
                        if ($node->gfw_block == true) {
                            continue;
                        }
                        foreach ($adminUser as $user) {
                            echo 'Send gfw mail to user: ' . $user->id . '-';
                            $user->sendMail(
                                $_ENV['appName'] . '-系统警告',
                                'news/warn.tpl',
                                [
                                    'text' => '管理员您好，系统发现节点 ' . $node->name . ' 被墙了，请您及时处理。'
                                ],
                                []
                            );
                            $notice_text = str_replace(
                                '%node_name%',
                                $node->name,
                                Config::getconfig('Telegram.string.NodeGFW')
                            );
                        }
                        if (Config::getconfig('Telegram.bool.NodeGFW')) {
                            Telegram::Send($notice_text);
                        }
                        $node->gfw_block = true;
                        $node->save();
                    } else {
                        //没有被墙
                        echo ($node->id . ':true' . PHP_EOL);
                        if ($node->gfw_block == false) {
                            continue;
                        }
                        foreach ($adminUser as $user) {
                            echo 'Send gfw mail to user: ' . $user->id . '-';
                            $user->sendMail(
                                $_ENV['appName'] . '-系统提示',
                                'news/warn.tpl',
                                [
                                    'text' => '管理员您好，系统发现节点 ' . $node->name . ' 溜出墙了。'
                                ],
                                []
                            );
                            $notice_text = str_replace(
                                '%node_name%',
                                $node->name,
                                Config::getconfig('Telegram.string.NodeGFW_recover')
                            );
                        }
                        if (Config::getconfig('Telegram.bool.NodeGFW_recover')) {
                            Telegram::Send($notice_text);
                        }
                        $node->gfw_block = false;
                        $node->save();
                    }
                }
                break;
            }

            echo ($node->id . 'interval skip' . PHP_EOL);
            sleep(3);
        }
    }
}
