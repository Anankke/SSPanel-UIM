<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use Exception;

final class RefController extends BaseController
{
    private static array $update_field = [
        'invitation_to_register_balance_reward',
        'invitation_to_register_traffic_reward',
        'invitation_mode',
        'invite_rebate_mode',
        'rebate_ratio',
        'rebate_frequency_limit',
        'rebate_amount_limit',
        'rebate_time_range_limit',
    ];

    /**
     * @throws Exception
     */
    public function index($request, $response, $args)
    {
        $settings = Config::getClass('ref');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/ref.tpl')
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
