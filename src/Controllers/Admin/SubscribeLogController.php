<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserSubscribeLog;
use App\Utils\Tools;
use Exception;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class SubscribeLogController extends BaseController
{
    public static array $details =
    [
        'field' => [
            'id' => '事件ID',
            'user_name' => '用户名',
            'user_id' => '用户ID',
            'email' => '用户邮箱',
            'subscribe_type' => '获取的订阅类型',
            'request_ip' => '请求IP',
            'location' => 'IP归属地',
            'request_time' => '请求时间',
            'request_user_agent' => '客户端标识符',
        ],
    ];

    /**
     * 后台订阅记录页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/subscribe.tpl')
        );
    }

    /**
     * 后台订阅记录页面 AJAX
     *
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $subscribes = UserSubscribeLog::orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = UserSubscribeLog::count();

        foreach ($subscribes as $subscribe) {
            $subscribe->location = Tools::getIpLocation($subscribe->request_ip);
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'subscribes' => $subscribes,
        ]);
    }
}
