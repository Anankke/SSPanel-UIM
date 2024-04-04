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
                'ip' => '登录IP',
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
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
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
    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $login_log = LoginIp::query();

        $search = $request->getParam('search')['value'];

        if ($search !== '') {
            $login_log->where('userid', '=', $search)
                ->orWhere('ip', 'LIKE', "%{$search}%");
        }

        $order = $request->getParam('order')[0]['dir'];

        if ($request->getParam('order')[0]['column'] !== '0') {
            $order_by = $request->getParam('columns')[$request->getParam('order')[0]['column']]['data'];

            $login_log->orderBy($order_by, $order)->orderBy('id', 'desc');
        } else {
            $login_log->orderBy('id', $order);
        }

        $filtered = $login_log->count();
        $total = (new LoginIp())->count();

        $logins = $login_log->paginate($length, '*', '', $page);

        foreach ($logins as $login) {
            $login->location = Tools::getIpLocation($login->ip);
            $login->datetime = Tools::toDateTime((int) $login->datetime);
            $login->type = $login->type();
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'logins' => $logins,
        ]);
    }
}
