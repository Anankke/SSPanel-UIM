<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoginIp;
use App\Services\DB;
use App\Utils\Tools;
use Exception;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function array_map;
use function array_slice;
use function count;
use function str_replace;

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
                'user_id' => '用户ID',
                'user_name' => '用户名',
                'node_id' => '节点ID',
                'node_name' => '节点名',
                'ip' => 'IP',
                'location' => 'IP归属地',
                'first_time' => '首次连接',
                'last_time' => '最后连接',
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
     *
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function ajaxLogin(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $logins = LoginIp::orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = LoginIp::count();

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

    /**
     * 后台在线 IP 页面
     *
     * @throws Exception
     */
    public function online(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$ip_details)
                ->fetch('admin/log/online.tpl')
        );
    }

    /**
     * 后台在线 IP 页面 AJAX
     *
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function ajaxOnline(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $data = $request->getParsedBody();
        $length = (int) ($data['length'] ?? 0);
        $start = (int) ($data['start'] ?? 0);
        $draw = $data['draw'] ?? null;

        $logs = DB::select('
            SELECT
                online_log.id,
                online_log.user_id,
                user.user_name,
                online_log.node_id,
                node.name AS node_name,
                online_log.ip,
                online_log.first_time,
                online_log.last_time
            FROM
                online_log
                LEFT JOIN user ON user.id = online_log.user_id
                LEFT JOIN node ON node.id = online_log.node_id
            WHERE
                last_time > UNIX_TIMESTAMP() - 90
        ');

        $count = count($logs);
        $data = array_map(
            static function ($val) {
                return [
                    'id' => $val->id,
                    'user_id' => $val->user_id,
                    'user_name' => $val->user_name,
                    'node_id' => $val->node_id,
                    'node_name' => $val->node_name,
                    'ip' => str_replace('::ffff:', '', $val->ip),
                    'location' => Tools::getIpLocation($val->ip),
                    'first_time' => Tools::toDateTime($val->first_time),
                    'last_time' => Tools::toDateTime($val->last_time),
                ];
            },
            array_slice($logs, $start, $length)
        );

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'onlines' => $data,
        ]);
    }
}
