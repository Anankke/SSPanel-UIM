<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\DB;
use App\Utils\Tools;
use Exception;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function array_map;
use function array_slice;
use function count;
use function str_replace;

final class OnlineIpController extends BaseController
{
    public static array $details =
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
     * 后台在线 IP 页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/log/online.tpl')
        );
    }

    /**
     * 后台在线 IP 页面 AJAX
     *
     * @throws InvalidDatabaseException
     */
    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
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
                    'location' => Tools::getIpLocation(str_replace('::ffff:', '', $val->ip)),
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
