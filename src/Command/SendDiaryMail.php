<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Ann;
use App\Models\User;
use App\Services\Analytics;
use App\Services\Config;
use App\Utils\Telegram;
use App\Utils\Tools;

final class SendDiaryMail extends Command
{
    public $description = '├─=: php xcat SendDiaryMail  - 每日流量报告' . PHP_EOL;

    public function boot(): void
    {
        $users = User::all();
        $logs = Ann::orderBy('id', 'desc')->get();
        $text1 = '';

        foreach ($logs as $log) {
            if (strpos($log->content, 'Links') === false) {
                $text1 .= $log->content . '<br><br>';
            }
        }

        $lastday_total = 0;

        foreach ($users as $user) {
            $lastday_total += $user->u + $user->d - $user->last_day_t;
            $user->sendDailyNotification($text1);
        }

        $sts = new Analytics();

        if (Config::getconfig('Telegram.bool.Diary')) {
            Telegram::send(
                str_replace(
                    [
                        '%getTodayCheckinUser%',
                        '%lastday_total%',
                    ],
                    [
                        $sts->getTodayCheckinUser(),
                        Tools::flowAutoShow($lastday_total),
                    ],
                    Config::getconfig('Telegram.string.Diary')
                )
            );
        }
    }
}
