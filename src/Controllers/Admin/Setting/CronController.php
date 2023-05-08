<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Setting;
use Exception;

final class CronController extends BaseController
{
    public static array $update_field = [
        'daily_job_hour',
        'daily_job_minute',
        'enable_daily_finance_mail',
        'enable_weekly_finance_mail',
        'enable_monthly_finance_mail',
    ];

    /**
     * @throws Exception
     */
    public function cron($request, $response, $args)
    {
        $settings = [];
        $settings_raw = Setting::get(['item', 'value', 'type']);

        foreach ($settings_raw as $setting) {
            if ($setting->type === 'bool') {
                $settings[$setting->item] = (bool) $setting->value;
            } else {
                $settings[$setting->item] = (string) $setting->value;
            }
        }

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/cron.tpl')
        );
    }

    public function saveCron($request, $response, $args)
    {
        $daily_job_hour = (int) $request->getParam('daily_job_hour');
        $daily_job_minute = (int) $request->getParam('daily_job_minute');
        $enable_daily_finance_mail = (bool) $request->getParam('enable_daily_finance_mail');
        $enable_weekly_finance_mail = (bool) $request->getParam('enable_weekly_finance_mail');
        $enable_monthly_finance_mail = (bool) $request->getParam('enable_monthly_finance_mail');

        if ($daily_job_hour < 0 || $daily_job_hour > 23) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '每日任务执行时间的小时数必须在 0-23 之间',
            ]);
        }

        if ($daily_job_minute < 0 || $daily_job_minute > 59) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '每日任务执行时间的分钟数必须在 0-59 之间',
            ]);
        }

        Setting::where('item', '=', 'daily_job_hour')->update([
            'value' => $daily_job_hour,
        ]);

        Setting::where('item', '=', 'daily_job_minute')->update([
            'value' => $daily_job_minute - ($daily_job_minute % 5),
        ]);

        Setting::where('item', '=', 'enable_daily_finance_mail')->update([
            'value' => $enable_daily_finance_mail,
        ]);

        Setting::where('item', '=', 'enable_weekly_finance_mail')->update([
            'value' => $enable_weekly_finance_mail,
        ]);

        Setting::where('item', '=', 'enable_monthly_finance_mail')->update([
            'value' => $enable_monthly_finance_mail,
        ]);

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }
}
