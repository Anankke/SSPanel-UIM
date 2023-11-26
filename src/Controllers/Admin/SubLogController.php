<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubscribeLog;
use App\Utils\Tools;
use Exception;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class SubLogController extends BaseController
{
    private static array $details =
        [
            'field' => [
                'id' => '事件ID',
                'user_id' => '用户ID',
                'type' => '获取的订阅类型',
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
                ->fetch('admin/log/subscribe.tpl')
        );
    }

    /**
     * 后台订阅记录页面 AJAX
     *
     * @throws InvalidDatabaseException
     */
    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $subscribes = (new SubscribeLog())->orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = (new SubscribeLog())->count();

        foreach ($subscribes as $subscribe) {
            $subscribe->request_time = Tools::toDateTime($subscribe->request_time);
            $subscribe->location = $subscribe->getAttributes();
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'subscribes' => $subscribes,
        ]);
    }
}
