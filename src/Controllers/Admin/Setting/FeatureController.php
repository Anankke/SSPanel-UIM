<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Setting;
use Exception;
use function json_encode;

final class FeatureController extends BaseController
{
    private static array $update_field = [
        'display_media',
        'display_subscribe_log',
        'display_detect_log',
        'display_docs',
        'display_docs_only_for_paid_user',
    ];

    /**
     * @throws Exception
     */
    public function feature($request, $response, $args)
    {
        $settings = Setting::getClass('feature');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/feature.tpl')
        );
    }

    public function saveFeature($request, $response, $args)
    {
        foreach (self::$update_field as $item) {
            if (! Setting::set($item, $request->getParam($item))) {
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
