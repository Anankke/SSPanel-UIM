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
use function array_keys;
use function date;
use function fclose;
use function file_get_contents;
use function fopen;
use function fwrite;
use function in_array;
use function json_decode;
use function sleep;
use function str_replace;
use function strtotime;
use function time;
use const BASE_PATH;
use const PHP_EOL;

final class CronDetect
{
    /**
     * @throws TelegramSDKException
     */
    public static function gfw(): void
    {
        //节点被墙检测
        $last_time = file_get_contents(BASE_PATH . '/storage/last_detect_gfw_time');
        for ($count = 1; $count <= 12; $count++) {
            if (time() - $last_time >= $_ENV['detect_gfw_interval']) {
                $file_interval = fopen(BASE_PATH . '/storage/last_detect_gfw_time', 'wb');
                fwrite($file_interval, (string) time());
                fclose($file_interval);
                $nodes = Node::all();
                $adminUser = User::where('is_admin', '=', '1')->get();
                foreach ($nodes as $node) {
                    if (
                        $node->node_ip === '' ||
                        $node->node_ip === null ||
                        $node->online === false
                    ) {
                        continue;
                    }
                    $api_url = $_ENV['detect_gfw_url'];
                    $api_url = str_replace(
                        ['{ip}', '{port}'],
                        [$node->node_ip, $_ENV['detect_gfw_port']],
                        $api_url
                    );
                    //因为考虑到有v2ray之类的节点，所以不得不使用ip作为参数
                    $result_tcping = false;
                    $detect_time = $_ENV['detect_gfw_count'];

                    for ($i = 1; $i <= $detect_time; $i++) {
                        $json_tcping = json_decode(file_get_contents($api_url), true);
                        if ($json_tcping['status'] === 'true') {
                            $result_tcping = true;
                            break;
                        }
                    }

                    $notice_text = '';

                    if ($result_tcping === false) {
                        //被墙了
                        echo $node->id . ':false' . PHP_EOL;
                        //判断有没有发送过邮件
                        if ($node->gfw_block) {
                            continue;
                        }

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
                    } else {
                        //没有被墙
                        echo $node->id . ':true' . PHP_EOL;
                        if ($node->gfw_block === false) {
                            continue;
                        }
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
                    }

                    $node->save();
                }
                break;
            }

            echo 'interval skip' . PHP_EOL;
            sleep(3);
        }
    }

    public static function ban(): void
    {
        $new_logs = DetectLog::where('status', '=', 0)
            ->orderBy('id', 'asc')->take($_ENV['auto_detect_ban_numProcess'])->get();

        if (count($new_logs) !== 0) {
            $user_logs = [];

            foreach ($new_logs as $log) {
                // 分类各个用户的记录数量
                if (! in_array($log->user_id, array_keys($user_logs))) {
                    $user_logs[$log->user_id] = 0;
                }
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

                $user->all_detect_number += $value;
                $user->save();

                if ($user->is_banned === 1 ||
                    ($user->is_admin && $_ENV['auto_detect_ban_allow_admin']) ||
                    in_array($user->id, $_ENV['auto_detect_ban_allow_users'])) {
                    // 如果用户已被封禁
                    // 如果用户是管理员
                    // 如果属于钦定用户
                    // 则跳过
                    continue;
                }

                if ($_ENV['auto_detect_ban_type'] === 1) {
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
                } else {
                    $number = $user->all_detect_number;
                    $tmp = 0;

                    foreach ($_ENV['auto_detect_ban'] as $key => $val) {
                        if ($number >= $key && $key >= $tmp) {
                            $tmp = $key;
                        }
                    }

                    if ($tmp !== 0) {
                        if ($_ENV['auto_detect_ban'][$tmp]['type'] === 'kill') {
                            $user->killUser();
                        } else {
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
                            $DetectBanLog->detect_number = $number;
                            $DetectBanLog->ban_time = $_ENV['auto_detect_ban'][$tmp]['time'];
                            $DetectBanLog->start_time = strtotime('1989-06-04 00:05:00');
                            $DetectBanLog->end_time = strtotime($end_time);
                            $DetectBanLog->all_detect_number = $number;
                            $DetectBanLog->save();
                        }
                    }
                }
            }
        } else {
            echo date('Y-m-d H:i:s') . ' 暂无新审计记录' . PHP_EOL;
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
