<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use Exception;

final class SupportController extends BaseController
{
    private static array $update_field = [
        'live_chat',
        'crisp_id',
        'livechat_license',
        // Ticket
        'enable_ticket',
        'mail_ticket',
        'ticket_limit',
    ];

    /**
     * @throws Exception
     */
    public function index($request, $response, $args)
    {
        $settings = Config::getClass('support');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/support.tpl')
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
