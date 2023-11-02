<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use Exception;

final class RegController extends BaseController
{
    private static array $update_field = [
        'reg_mode',
        'reg_email_verify',
        'sign_up_for_daily_report',
        'random_group',
        'min_port',
        'max_port',
        'sign_up_for_free_traffic',
        'free_user_reset_day',
        'free_user_reset_bandwidth',
        'sign_up_for_class',
        'sign_up_for_class_time',
        'sign_up_for_method',
        'sign_up_for_invitation_codes',
        'connection_ip_limit',
        'connection_rate_limit',
        'reg_forbidden_ip',
        'reg_forbidden_port',
    ];

    /**
     * @throws Exception
     */
    public function index($request, $response, $args)
    {
        $settings = Config::getClass('reg');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/reg.tpl')
        );
    }

    public function save($request, $response, $args)
    {
        foreach (self::$update_field as $item) {
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
