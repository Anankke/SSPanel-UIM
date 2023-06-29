<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Setting;
use App\Services\CronDetect;
use App\Services\CronJob;
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

        $jobs = new CronJob();

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

        // Run traffic log job
        if ($minute === 0 && $_ENV['trafficLog']) {
            $jobs->addTrafficLog();
        }

        // Run daily job
        if ($hour === Setting::obtain('daily_job_hour') &&
            $minute === Setting::obtain('daily_job_minute') &&
            time() - Setting::obtain('last_daily_job_time') > 86399
        ) {
            $jobs->cleanDb();
            $jobs->resetNodeBandwidth();
            $jobs->resetFreeUserTraffic();
            $jobs->sendDailyTrafficReport();

            if (Setting::obtain('enable_detect_inactive_user')) {
                $jobs->detectInactiveUser();
            }

            if (Setting::obtain('telegram_diary')) {
                $jobs->sendTelegramDiary();
            }

            $jobs->resetTodayTraffic();

            if (Setting::obtain('telegram_daily_job')) {
                $jobs->sendTelegramDailyJob();
            }

            Setting::where('item', '=', 'last_daily_job_time')->update([
                'value' => mktime(
                    Setting::obtain('daily_job_hour'),
                    Setting::obtain('daily_job_minute'),
                    0,
                    (int) date('m'),
                    (int) date('d'),
                    (int) date('Y')
                ),
            ]);
        }

        // Daily finance report
        if (Setting::obtain('enable_daily_finance_mail')
            && $hour === 0
            && $minute === 0
        ) {
            $jobs->sendDailyFinanceMail();
        }

        // Weekly finance report
        if (Setting::obtain('enable_weekly_finance_mail')
            && $hour === 0
            && $minute === 0
            && date('w') === '1'
        ) {
            $jobs->sendWeeklyFinanceMail();
        }

        // Monthly finance report
        if (Setting::obtain('enable_monthly_finance_mail')
            && $hour === 0
            && $minute === 0
            && date('d') === '01'
        ) {
            $jobs->sendMonthlyFinanceMail();
        }

        // Detect GFW
        if (Setting::obtain('enable_detect_gfw') && $minute === 0
        ) {
            $detect = new CronDetect();
            $detect->gfw();
        }

        // Detect ban
        if (Setting::obtain('enable_detect_ban') && $minute === 0
        ) {
            $detect = new CronDetect();
            $detect->ban();
        }

        // Run email queue
        $jobs->processEmailQueue();
    }
}
