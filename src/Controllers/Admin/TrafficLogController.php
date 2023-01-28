<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserHourlyUsage;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\ServerRequest;
use Slim\Http\Response;

final class TrafficLogController extends BaseController
{
    public static $details =
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
     * @param array     $args
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
     *
     * @param array     $args
     */
    public function ajaxTrafficLog(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $trafficlogs = UserHourlyUsage::orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = UserHourlyUsage::count();

        foreach ($trafficlogs as $trafficlog) {
            $trafficlog->traffic = round(Tools::flowToGB($trafficlog->traffic), 2);
            $trafficlog->hourly_usage = round(Tools::flowToGB($trafficlog->hourly_usage), 2);
            $trafficlog->datetime = Tools::toDateTime((int) $trafficlog->datetime);
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'trafficlogs' => $trafficlogs,
        ]);
    }
}
