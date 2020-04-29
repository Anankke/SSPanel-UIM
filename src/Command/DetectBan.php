<?php

namespace App\Command;

use App\Models\{
    User,
    DetectLog,
    DetectBanLog
};

class DetectBan extends Command
{
    public $description = '├─=: php xcat DetectBan      - 审计封禁定时任务' . PHP_EOL;

    /**
     * 审计封禁任务
     */
    public function boot()
    {
        if ($_ENV['enable_auto_detect_ban'] === false) {
            return;
        }
        echo '审计封禁检查开始.' . PHP_EOL;
        $new_logs = DetectLog::where('status', '=', 0)->orderBy('id', 'asc')->take($_ENV['auto_detect_ban_numProcess'])->get();
        if (count($new_logs) != 0) {

            $user_logs = [];
            foreach ($new_logs as $log) {
                // 分类各个用户的记录数量
                if (!in_array($log->user_id, array_keys($user_logs))) {
                    $user_logs[$log->user_id] = 0;
                }
                $user_logs[$log->user_id]++;
                $log->status = 1;
                $log->save();
            }

            foreach ($user_logs as $userid => $value) {
                // 执行封禁
                $user = User::find($userid);
                if ($user == null) {
                    continue;
                }
                $user->all_detect_number += $value;
                $user->save();

                if ($user->enable == 0 || ($user->is_admin && $_ENV['auto_detect_ban_allow_admin'] === true) || in_array($user->id, $_ENV['auto_detect_ban_allow_users'])) {
                    // 如果用户已被封禁
                    // 如果用户是管理员
                    // 如果属于钦定用户
                    // 则跳过
                    continue;
                }

                if ($_ENV['auto_detect_ban_type'] == 1) {
                    $last_DetectBanLog      = DetectBanLog::where('user_id', $userid)->orderBy('id', 'desc')->first();
                    $last_all_detect_number = ($last_DetectBanLog == null ? 0 : (int) $last_DetectBanLog->all_detect_number);
                    $detect_number          = ($user->all_detect_number - $last_all_detect_number);
                    if ($detect_number >= $_ENV['auto_detect_ban_number']) {
                        $last_detect_ban_time               = $user->last_detect_ban_time;
                        $end_time                           = date('Y-m-d H:i:s');
                        $user->enable                       = 0;
                        $user->last_detect_ban_time         = $end_time;
                        $user->save();
                        $DetectBanLog                       = new DetectBanLog();
                        $DetectBanLog->user_name            = $user->user_name;
                        $DetectBanLog->user_id              = $user->id;
                        $DetectBanLog->email                = $user->email;
                        $DetectBanLog->detect_number        = $detect_number;
                        $DetectBanLog->ban_time             = $_ENV['auto_detect_ban_time'];
                        $DetectBanLog->start_time           = strtotime($last_detect_ban_time);
                        $DetectBanLog->end_time             = strtotime($end_time);
                        $DetectBanLog->all_detect_number    = $user->all_detect_number;
                        $DetectBanLog->save();
                    }
                } else {
                    $number = $user->all_detect_number;
                    $tmp = 0;
                    foreach ($_ENV['auto_detect_ban'] as $key => $value) {
                        if ($number >= $key) {
                            if ($key >= $tmp) {
                                $tmp = $key;
                            }
                        }
                    }
                    if ($tmp != 0) {
                        if ($_ENV['auto_detect_ban'][$tmp]['type'] == 'kill') {
                            $user->kill_user();
                        } else {
                            $last_detect_ban_time               = $user->last_detect_ban_time;
                            $end_time                           = date('Y-m-d H:i:s');
                            $user->enable                       = 0;
                            $user->last_detect_ban_time         = $end_time;
                            $user->save();
                            $DetectBanLog                       = new DetectBanLog();
                            $DetectBanLog->user_name            = $user->user_name;
                            $DetectBanLog->user_id              = $user->id;
                            $DetectBanLog->email                = $user->email;
                            $DetectBanLog->detect_number        = $number;
                            $DetectBanLog->ban_time             = $_ENV['auto_detect_ban'][$tmp]['time'];
                            $DetectBanLog->start_time           = strtotime('1989-06-04 00:05:00');
                            $DetectBanLog->end_time             = strtotime($end_time);
                            $DetectBanLog->all_detect_number    = $number;
                            $DetectBanLog->save();
                        }
                    }
                }
            }
        } else {
            echo '- 暂无新记录.' . PHP_EOL;
        }
        echo '审计封禁检查结束.' . PHP_EOL;
    }
}
