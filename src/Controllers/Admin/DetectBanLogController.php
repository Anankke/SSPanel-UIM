<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DetectBanLog;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class DetectBanLogController extends BaseController
{
    /**
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'id' => 'ID',
                    'user_id' => '用户ID',
                    'user_name' => '用户名',
                    'email' => '用户邮箱',
                    'detect_number' => '违规次数',
                    'ban_time' => '封禁时长(分钟)',
                    'start_time' => '统计开始时间',
                    'end_time' => '统计结束以及封禁开始时间',
                    'ban_end_time' => '封禁结束时间',
                    'all_detect_number' => '累计违规次数',
                ], 'ban/ajax'))
                ->display('admin/detect/ban.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ajaxLog(Request $request, Response $response, array $args): ResponseInterface
    {
        $query = DetectBanLog::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['ban_end_time'])) {
                    $order_field = 'end_time';
                }
            }
        );

        $data = [];
        foreach ($query['datas'] as $value) {
            /** @var DetectBanLog $value */

            if ($value->user() === null) {
                DetectBanLog::userIsNull($value);
                continue;
            }
            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['user_id'] = $value->user_id;
            $tempdata['user_name'] = $value->user_name;
            $tempdata['email'] = $value->email;
            $tempdata['detect_number'] = $value->detect_number;
            $tempdata['ban_time'] = $value->ban_time;
            $tempdata['start_time'] = $value->startTime();
            $tempdata['end_time'] = $value->endTime();
            $tempdata['ban_end_time'] = $value->banEndTime();
            $tempdata['all_detect_number'] = $value->all_detect_number;

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => DetectBanLog::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
