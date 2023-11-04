<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use Exception;

final class CronController extends BaseController
{
    private static array $update_field = [
        'daily_job_hour',
        'daily_job_minute',
        'enable_daily_finance_mail',
        'enable_weekly_finance_mail',
        'enable_monthly_finance_mail',
        'enable_detect_gfw',
        'enable_detect_ban',
        'enable_detect_inactive_user',
        'detect_inactive_user_checkin_days',
        'detect_inactive_user_login_days',
        'detect_inactive_user_use_days',
    ];

    /**
     * @throws Exception
     */
    public function index($request, $response, $args)
    {
        $settings = Config::getClass('cron');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/cron.tpl')
        );
    }

    public function save($request, $response, $args)
    {
        $daily_job_hour = (int) $request->getParam('daily_job_hour');
        $daily_job_minute = (int) $request->getParam('daily_job_minute');

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

        foreach (self::$update_field as $item) {
            if ($item === 'daily_job_minute') {
                Config::set($item, $daily_job_minute - ($daily_job_minute % 5));
                continue;
            }

            if (! Config::set($item, $request->getParam($item))) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '保存 ' . $item . ' 时出错',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }
}
