<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SysLog;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Smarty\Exception;
use function strlen;

final class SysLogController extends BaseController
{
    private static array $details =
        [
            'field' => [
                'op' => '操作',
                'id' => '事件ID',
                'user_id' => '触发用户',
                'ip' => '触发IP',
                'message' => '日志内容',
                'level' => '日志等级',
                'channel' => '日志类别',
                'datetime' => '记录时间',
            ],
        ];

    /**
     * 系统日志页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/syslog/index.tpl')
        );
    }

    /**
     * 系统日志详情页面
     *
     * @throws Exception
     */
    public function detail(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $syslog = (new SysLog())->find($args['id']);

        if ($syslog === null) {
            return $response->withRedirect('/admin/syslog');
        }

        $syslog->level_text = $syslog->level();
        $syslog->context = json_decode($syslog->context);
        $syslog->channel_text = $syslog->channel();
        $syslog->datetime = Tools::toDateTime($syslog->datetime);

        return $response->write(
            $this->view()
                ->assign('syslog', $syslog)
                ->fetch('admin/syslog/view.tpl')
        );
    }

    /**
     * 系统日志页面 AJAX
     */
    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');
        $syslog = SysLog::query();
        $search = $request->getParam('search')['value'];

        if ($search !== '') {
            $syslog->where('user_id', '=', $search)
                ->orWhere('ip', 'LIKE', "%{$search}%")
                ->orWhere('message', 'LIKE', "%{$search}%")
                ->orWhere('level', 'LIKE', "%{$search}%")
                ->orWhere('channel', 'LIKE', "%{$search}%");
        }

        $order = $request->getParam('order')[0]['dir'];

        if ($request->getParam('order')[0]['column'] !== '0') {
            $order_by = $request->getParam('columns')[$request->getParam('order')[0]['column']]['data'];
            $syslog->orderBy($order_by, $order)->orderBy('id', 'desc');
        } else {
            $syslog->orderBy('id', $order);
        }

        $filtered = $syslog->count();
        $total = (new SysLog())->count();
        $syslogs = $syslog->paginate($length, '*', '', $page);

        foreach ($syslogs as $log) {
            $log->op =
                '<a class="btn btn-primary" href="/admin/syslog/' . $log->id . '/view">查看</a>';
            $log->message = strlen($log->message) > 25 ?
                substr($log->message, 0, 25) . '...' : $log->message;
            $log->level = $log->level();
            $log->channel = $log->channel();
            $log->datetime = Tools::toDateTime($log->datetime);
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'syslogs' => $syslogs,
        ]);
    }
}
