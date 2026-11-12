<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OnlineLog;
use App\Utils\DataTable;
use App\Utils\Tools;
use Exception;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function array_keys;
use function time;

final class OnlineLogController extends BaseController
{
    private static array $details =
        [
            'field' => [
                'id' => '事件ID',
                'user_id' => '用户ID',
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
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
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
    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $online_log = OnlineLog::query()->where('last_time', '>', time() - 90);

        $search = $request->getParam('search')['value'];

        if ($search !== '') {
            $online_log->where('user_id', '=', $search)
                ->orWhere('ip', 'LIKE', "%{$search}%")
                ->orWhere('node_id', '=', $search);
        }

        DataTable::applyOrder(
            $online_log,
            $request,
            array_keys(self::$details['field']),
            ['id', 'user_id', 'node_id', 'ip', 'first_time', 'last_time'],
            'last_time'
        );

        $filtered = $online_log->count();
        $total = (new OnlineLog())->count();

        $onlines = $online_log->paginate($length, '*', '', $page);

        foreach ($onlines as $online) {
            $online->node_name = $online->nodeName();
            $online->ip = $online->ip();
            $online->location = Tools::getIpLocation($online->ip);
            $online->first_time = Tools::toDateTime($online->first_time);
            $online->last_time = Tools::toDateTime($online->last_time);
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'onlines' => $onlines,
        ]);
    }
}
