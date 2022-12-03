<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Ip;
use App\Models\LoginIp;
use App\Utils\QQWry;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Slim\Http\Request;
use Slim\Http\Response;

final class IpController extends BaseController
{
    public static $login_details =
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

    public static $ip_details =
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
     * @param array     $args
     */
    public function login(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$login_details)
                ->display('admin/ip/login.tpl')
        );
    }

    /**
     * 后台登录记录页面 AJAX
     *
     * @param array     $args
     */
    public function ajaxLogin(Request $request, Response $response, array $args)
    {
        $logins = LoginIp::orderBy('id', 'desc')->get();

        $QQWry = new QQWry();
        foreach ($logins as $login) {
            $login->user_name = $login->userName();
            $login->location = $login->location($QQWry);
            $login->datetime = Tools::toDateTime((int) $login->datetime);
            $login->type = $login->type();
        }

        return $response->withJson([
            'logins' => $logins,
        ]);
    }

    /**
     * 后台在线 IP 页面
     *
     * @param array     $args
     */
    public function alive(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$ip_details)
                ->display('admin/ip/alive.tpl')
        );
    }

    /**
     * 后台在线 IP 页面 AJAX
     *
     * @param array     $args
     */
    public function ajaxAlive(Request $request, Response $response, array $args)
    {
        $alives = Ip::orderBy('id', 'desc')->get();

        $QQWry = new QQWry();
        foreach ($alives as $alive) {
            $alive->user_name = $alive->userName();
            $alive->node_name = $alive->nodeName();
            $alive->ip = Tools::getRealIp($alive->ip);
            $alive->location = $alive->location($QQWry);
            $alive->datetime = Tools::toDateTime((int) $alive->datetime);
        }

        return $response->withJson([
            'alives' => $alives,
        ]);
    }
}
