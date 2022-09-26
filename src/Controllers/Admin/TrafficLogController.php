<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserHourlyUsage;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class TrafficLogController extends BaseController
{
    /**
     * 后台流量记录页面
     *
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        $table_config = [];
        $table_config['total_column'] = [
            'id' => 'ID',
            'user_id' => '用户ID',
            'traffic' => '累计使用流量/GB',
            'hourly_usage' => '过去一小时使用流量/GB',
            'datetime' => '时间',
        ];
        $table_config['default_show_column'] = ['id', 'user_id', 'traffic', 'hourly_usage', 'datetime'];
        $table_config['ajax_url'] = 'trafficlog/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/trafficlog.tpl')
        );
    }

    /**
     * 后台流量记录页面 AJAX
     *
     * @param array     $args
     */
    public function ajaxTrafficLog(Request $request, Response $response, array $args): ResponseInterface
    {
        $query = UserHourlyUsage::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (\in_array($order_field, ['id'])) {
                    $order_field = 'id';
                }
            }
        );

        $data = [];
        foreach ($query['datas'] as $value) {
            /** @var TrafficLog $value */

            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['user_id'] = $value->user_id;
            $tempdata['traffic'] = Tools::flowToGB($value->traffic);
            $tempdata['hourly_usage'] = Tools::flowToGB($value->hourly_usage);
            $tempdata['datetime'] = Tools::toDateTime($value->datetime);

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => UserHourlyUsage::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
