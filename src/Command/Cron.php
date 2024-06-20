<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Config;
use App\Services\Cron as CronService;
use App\Services\Detect;
use Exception;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function mktime;
use function time;

final class Cron extends Command
{
    public string $description = <<<EOL
├─=: php xcat Cron - 站点定时任务，每五分钟
EOL;

    /**
     * @throws TelegramSDKException
     * @throws Exception
     */
    public function boot(): void
    {
        ini_set('memory_limit', '-1');

        // Log current hour & minute
        $hour = (int) date('H');
        $minute = (int) date('i');

        $jobs = new CronService();

        // Run new shop related jobs
        $jobs->processPendingOrder();
        $jobs->processTabpOrderActivation();
        $jobs->processBandwidthOrderActivation();
        $jobs->processTimeOrderActivation();

        // Run user related jobs
        $jobs->expirePaidUserAccount();
        $jobs->sendPaidUserUsageLimitNotification();

        // Run node related jobs
        $jobs->updateNodeIp();

        if ($_ENV['enable_detect_offline']) {
            $jobs->detectNodeOffline();
        }

        // Run daily job
        if ($hour === Config::obtain('daily_job_hour') &&
            $minute === Config::obtain('daily_job_minute') &&
            time() - Config::obtain('last_daily_job_time') > 86399
        ) {
            $jobs->cleanDb();
            $jobs->resetNodeBandwidth();
            $jobs->resetFreeUserBandwidth();
            $jobs->sendDailyTrafficReport();

            if (Config::obtain('enable_detect_inactive_user')) {
                $jobs->detectInactiveUser();
            }

            if (Config::obtain('remove_inactive_user_link_and_invite')) {
                $jobs->removeInactiveUserLinkAndInvite();
            }

            if (Config::obtain('telegram_diary')) {
                $jobs->sendTelegramDiary();
            }

            $jobs->resetTodayBandwidth();

            if (Config::obtain('telegram_daily_job')) {
                $jobs->sendTelegramDailyJob();
            }

            (new Config())->where('item', 'last_daily_job_time')->update([
                'value' => mktime(
                    Config::obtain('daily_job_hour'),
                    Config::obtain('daily_job_minute'),
                    0,
                    (int) date('m'),
                    (int) date('d'),
                    (int) date('Y')
                ),
            ]);
        }

        // Daily finance report
        if (Config::obtain('enable_daily_finance_mail')
            && $hour === 0
            && $minute === 0
        ) {
            $jobs->sendDailyFinanceMail();
        }

        // Weekly finance report
        if (Config::obtain('enable_weekly_finance_mail')
            && $hour === 0
            && $minute === 0
            && date('w') === '1'
        ) {
            $jobs->sendWeeklyFinanceMail();
        }

        // Monthly finance report
        if (Config::obtain('enable_monthly_finance_mail')
            && $hour === 0
            && $minute === 0
            && date('d') === '01'
        ) {
            $jobs->sendMonthlyFinanceMail();
        }

        // Detect GFW
        if (Config::obtain('enable_detect_gfw') && $minute === 0
        ) {
            $detect = new Detect();
            $detect->gfw();
        }

        // Detect ban
        if (Config::obtain('enable_detect_ban') && $minute === 0
        ) {
            $detect = new Detect();
            $detect->ban();
        }

        // Run email queue
        $jobs->processEmailQueue();
    }
}
