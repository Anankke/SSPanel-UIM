<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserHourlyUsage;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class TrafficLogController extends BaseController
{
    private static array $details =
        [
            'field' => [
                'id' => '记录ID',
                'user_id' => '用户ID',
                'traffic' => '累计流量/GB',
                'hourly_usage' => '过去一小时使用流量/GB',
                'datetime' => '时间',
            ],
        ];

    /**
     * 后台流量记录页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/log/traffic.tpl')
        );
    }

    /**
     * 后台流量记录页面 AJAX
     */
    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $traffic_log = UserHourlyUsage::query();

        $search = $request->getParam('search')['value'];

        if ($search !== '') {
            $traffic_log->where('user_id', '=', $search);
        }

        $order = $request->getParam('order')[0]['dir'];

        if ($request->getParam('order')[0]['column'] !== '0') {
            $order_by = $request->getParam('columns')[$request->getParam('order')[0]['column']]['data'];

            $traffic_log->orderBy($order_by, $order)->orderBy('id', 'desc');
        } else {
            $traffic_log->orderBy('id', $order);
        }

        $filtered = $traffic_log->count();
        $total = (new UserHourlyUsage())->count();

        $trafficlogs = $traffic_log->paginate($length, '*', '', $page);

        foreach ($trafficlogs as $trafficlog) {
            $trafficlog->traffic = Tools::flowToGB($trafficlog->traffic);
            $trafficlog->hourly_usage = Tools::flowToGB($trafficlog->hourly_usage);
            $trafficlog->datetime = Tools::toDateTime((int) $trafficlog->datetime);
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'trafficlogs' => $trafficlogs,
        ]);
    }
}
