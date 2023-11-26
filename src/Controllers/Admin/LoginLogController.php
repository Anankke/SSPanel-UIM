<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoginIp;
use App\Utils\Tools;
use Exception;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class LoginLogController extends BaseController
{
    private static array $details =
        [
            'field' => [
                'id' => '事件ID',
                'userid' => '用户ID',
                'user_name' => '用户名',
                'ip' => 'IP',
                'location' => 'IP归属地',
                'datetime' => '时间',
                'type' => '类型',
            ],
        ];

    /**
     * 后台登录记录页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/log/login.tpl')
        );
    }

    /**
     * 后台登录记录页面 AJAX
     *
     * @throws InvalidDatabaseException
     */
    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $logins = (new LoginIp())->orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = (new LoginIp())->count();

        foreach ($logins as $login) {
            $login->user_name = $login->userName();
            $login->location = Tools::getIpLocation($login->ip);
            $login->datetime = Tools::toDateTime((int) $login->datetime);
            $login->type = $login->type();
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'logins' => $logins,
        ]);
    }
}
