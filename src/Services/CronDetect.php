<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DetectBanLog;
use App\Models\DetectLog;
use App\Models\Node;
use App\Models\Setting;
use App\Models\User;
use App\Utils\Telegram;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function date;
use function file_get_contents;
use function in_array;
use function json_decode;
use function str_replace;
use function strtotime;
use function time;
use const PHP_EOL;

final class CronDetect
{
    /**
     * @throws TelegramSDKException
     */
    public static function gfw(): void
    {
        $nodes = Node::where('type', '=', 1)->where('node_ip', '!=', '')->where('online', '=', 1)->get();
        $adminUser = User::where('is_admin', '=', '1')->get();

        foreach ($nodes as $node) {
            $api_url = str_replace(
                ['{ip}', '{port}'],
                [$node->node_ip, $_ENV['detect_gfw_port']],
                $_ENV['detect_gfw_url']
            );

            $json_tcping = json_decode(file_get_contents($api_url), true);
            $result_tcping = $json_tcping['status'] === 'true';

            if ($result_tcping && ! $node->gfw_block) {
                continue;
            }

            if (! $result_tcping && ! $node->gfw_block) {
                //被墙了
                echo $node->id . ':false' . PHP_EOL;

                foreach ($adminUser as $user) {
                    echo 'Send gfw mail to user: ' . $user->id . '-';
                    $user->sendMail(
                        $_ENV['appName'] . '-系统警告',
                        'warn.tpl',
                        [
                            'text' => '管理员你好，系统发现节点 ' . $node->name . ' 被墙了，请你及时处理。',
                        ],
                        []
                    );
                    $notice_text = str_replace(
                        '%node_name%',
                        $node->name,
                        Setting::obtain('telegram_node_gfwed_text')
                    );
                }

                if (Setting::obtain('telegram_node_gfwed')) {
                    Telegram::send($notice_text);
                }

                $node->gfw_block = true;
                $node->save();

                continue;
            }

            echo $node->id . ':true' . PHP_EOL;

            foreach ($adminUser as $user) {
                echo 'Send gfw mail to user: ' . $user->id . '-';
                $user->sendMail(
                    $_ENV['appName'] . '-系统提示',
                    'warn.tpl',
                    [
                        'text' => '管理员你好，系统发现节点 ' . $node->name . ' 溜出墙了。',
                    ],
                    []
                );
                $notice_text = str_replace(
                    '%node_name%',
                    $node->name,
                    Setting::obtain('telegram_node_ungfwed_text')
                );
            }

            if (Setting::obtain('telegram_node_ungfwed')) {
                Telegram::send($notice_text);
            }

            $node->gfw_block = false;
            $node->save();
        }
    }

    public static function ban(): void
    {
        $new_logs = DetectLog::where('status', '=', 0)->orderBy('id', 'asc')->get();
        $user_logs = [];

        foreach ($new_logs as $log) {
            // 分类各个用户的记录数量
            $user_logs[$log->user_id] = 0;
            $user_logs[$log->user_id]++;
            $log->status = 1;
            $log->save();
        }

        foreach ($user_logs as $userid => $value) {
            // 执行封禁
            $user = User::find($userid);

            if ($user === null) {
                continue;
            }

            if ($user->is_banned === 1 ||
                ($user->is_admin && $_ENV['auto_detect_ban_allow_admin']) ||
                in_array($user->id, $_ENV['auto_detect_ban_allow_users'])) {
                continue;
            }

            $user->all_detect_number += $value;
            $user->save();

            $last_DetectBanLog = DetectBanLog::where('user_id', $userid)->orderBy('id', 'desc')->first();
            $last_all_detect_number = ((int) $last_DetectBanLog?->all_detect_number);
            $detect_number = $user->all_detect_number - $last_all_detect_number;

            if ($detect_number >= $_ENV['auto_detect_ban_number']) {
                $last_detect_ban_time = $user->last_detect_ban_time;
                $end_time = date('Y-m-d H:i:s');
                $user->is_banned = 1;
                $user->banned_reason = 'DetectBan';
                $user->last_detect_ban_time = $end_time;
                $user->save();
                $DetectBanLog = new DetectBanLog();
                $DetectBanLog->user_name = $user->user_name;
                $DetectBanLog->user_id = $user->id;
                $DetectBanLog->email = $user->email;
                $DetectBanLog->detect_number = $detect_number;
                $DetectBanLog->ban_time = $_ENV['auto_detect_ban_time'];
                $DetectBanLog->start_time = strtotime($last_detect_ban_time);
                $DetectBanLog->end_time = strtotime($end_time);
                $DetectBanLog->all_detect_number = $user->all_detect_number;
                $DetectBanLog->save();
            }
        }

        echo date('Y-m-d H:i:s') . ' 审计封禁检查结束' . PHP_EOL;

        // 审计封禁解封
        $banned_users = User::where('is_banned', 1)->where('banned_reason', 'DetectBan')->get();

        foreach ($banned_users as $user) {
            $logs = DetectBanLog::where('user_id', $user->id)->orderBy('id', 'desc')->first();
            if ($logs !== null && ($logs->end_time + $logs->ban_time * 60) <= time()) {
                $user->is_banned = 0;
                $user->banned_reason = '';
                $user->save();
            }
        }

        echo date('Y-m-d H:i:s') . ' 审计解封检查结束' . PHP_EOL;
    }
}
