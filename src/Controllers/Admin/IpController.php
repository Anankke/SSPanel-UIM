<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Ip;
use App\Models\LoginIp;
use App\Utils\QQWry;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function count;
use function time;

final class IpController extends BaseController
{
    public static array $login_details =
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

    public static array $ip_details =
    [
        'field' => [
            'id' => '事件ID',
            'userid' => '用户ID',
            'user_name' => '用户名',
            'nodeid' => '节点ID',
            'node_name' => '节点名',
            'ip' => 'IP',
            'location' => 'IP归属地',
            'datetime' => '时间',
        ],
    ];

    /**
     * 后台登录记录页面
     *
     * @throws Exception
     */
    public function login(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$login_details)
                ->fetch('admin/log/login.tpl')
        );
    }

    /**
     * 后台登录记录页面 AJAX
     */
    public function ajaxLogin(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $logins = LoginIp::orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = LoginIp::count();

        $QQWry = new QQWry();
        foreach ($logins as $login) {
            $login->user_name = $login->userName();
            $login->location = $login->location($QQWry);
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

    /**
     * 后台在线 IP 页面
     *
     * @throws Exception
     */
    public function alive(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$ip_details)
                ->fetch('admin/log/alive.tpl')
        );
    }

    /**
     * 后台在线 IP 页面 AJAX
     */
    public function ajaxAlive(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $alives = Ip::where('datetime', '>=', time() - 60)->orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = count(Ip::where('datetime', '>=', time() - 60)->orderBy('id', 'desc')->get());

        $QQWry = new QQWry();
        foreach ($alives as $alive) {
            $alive->user_name = $alive->userName();
            $alive->node_name = $alive->nodeName();
            $alive->ip = Tools::getRealIp($alive->ip);
            $alive->location = $alive->location($QQWry);
            $alive->datetime = Tools::toDateTime((int) $alive->datetime);
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'alives' => $alives,
        ]);
    }
}
