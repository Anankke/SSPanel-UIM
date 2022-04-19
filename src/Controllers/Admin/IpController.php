<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BlockIp;
use App\Models\Ip;
use App\Models\LoginIp;
use App\Models\UnblockIp;
use App\Utils\QQWry;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Slim\Http\Request;
use Slim\Http\Response;

final class IpController extends BaseController
{
    /**
     * 后台登录记录页面
     *
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'id' => 'ID',
                    'userid' => '用户ID',
                    'user_name' => '用户名',
                    'ip' => 'IP',
                    'location' => '归属地',
                    'datetime' => '时间',
                    'type' => '类型',
                ], 'login/ajax'))
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
        $query = LoginIp::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['user_name'])) {
                    $order_field = 'userid';
                }
                if (in_array($order_field, ['location'])) {
                    $order_field = 'ip';
                }
            }
        );

        $data = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var LoginIp $value */

            if ($value->user() === null) {
                LoginIp::userIsNull($value);
                continue;
            }
            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['userid'] = $value->userid;
            $tempdata['user_name'] = $value->userName();
            $tempdata['ip'] = $value->ip;
            $tempdata['location'] = $value->location($QQWry);
            $tempdata['datetime'] = $value->datetime();
            $tempdata['type'] = $value->type();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => LoginIp::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
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
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'id' => 'ID',
                    'userid' => '用户ID',
                    'user_name' => '用户名',
                    'nodeid' => '节点ID',
                    'node_name' => '节点名',
                    'ip' => 'IP',
                    'location' => '归属地',
                    'datetime' => '时间',
                    'is_node' => '是否为中转连接',
                ], 'alive/ajax'))
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
        $query = Ip::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['user_name'])) {
                    $order_field = 'userid';
                }
                if (in_array($order_field, ['node_name', 'is_node'])) {
                    $order_field = 'nodeid';
                }
                if (in_array($order_field, ['location'])) {
                    $order_field = 'ip';
                }
            },
            static function ($query): void {
                $query->where('datetime', '>=', time() - 60);
            }
        );

        $data = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var Ip $value */

            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['userid'] = $value->userid;
            $tempdata['user_name'] = $value->userName();
            $tempdata['nodeid'] = $value->nodeid;
            $tempdata['node_name'] = $value->nodeName();
            $tempdata['ip'] = Tools::getRealIp($value->ip);
            $tempdata['location'] = $value->location($QQWry);
            $tempdata['datetime'] = $value->datetime();
            $tempdata['is_node'] = $value->isNode();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => Ip::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }

    /**
     * 节点被封IP
     *
     * @param array     $args
     */
    public function block(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'id' => 'ID',
                    'node_name' => '节点名称',
                    'ip' => 'IP',
                    'location' => '归属地',
                    'datetime' => '时间',
                ], 'block/ajax'))
                ->display('admin/ip/block.tpl')
        );
    }

    /**
     * 节点被封IP AJAX
     *
     * @param array     $args
     */
    public function ajaxBlock(Request $request, Response $response, array $args)
    {
        $query = BlockIp::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['node_name'])) {
                    $order_field = 'nodeid';
                }
                if (in_array($order_field, ['location'])) {
                    $order_field = 'ip';
                }
            }
        );

        $data = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var BlockIp $value */

            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['node_name'] = $value->node_name();
            $tempdata['ip'] = $value->ip;
            $tempdata['location'] = $value->location($QQWry);
            $tempdata['datetime'] = $value->datetime();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => BlockIp::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }

    /**
     * 解封IP
     *
     * @param array     $args
     */
    public function doUnblock(Request $request, Response $response, array $args)
    {
        $ip = trim($request->getParam('ip'));
        BlockIp::where('ip', $ip)->delete();
        $UIP = new UnblockIp();
        $UIP->userid = $this->user->id;
        $UIP->ip = $ip;
        $UIP->datetime = time();
        $UIP->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '发送解封命令解封 ' . $ip . ' 成功',
        ]);
    }

    /**
     * 解封IP记录
     *
     * @param array     $args
     */
    public function unblock(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'id' => 'ID',
                    'userid' => '用户ID',
                    'user_name' => '用户名',
                    'ip' => 'IP',
                    'location' => '归属地',
                    'datetime' => '时间',
                ], 'unblock/ajax'))
                ->display('admin/ip/unblock.tpl')
        );
    }

    /**
     * 解封IP记录 AJAX
     *
     * @param array     $args
     */
    public function ajaxUnblock(Request $request, Response $response, array $args)
    {
        $query = UnblockIp::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['user_name'])) {
                    $order_field = 'userid';
                }
                if (in_array($order_field, ['location'])) {
                    $order_field = 'ip';
                }
            }
        );

        $data = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var UnblockIp $value */

            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['userid'] = $value->userid;
            $tempdata['user_name'] = $value->userName();
            $tempdata['ip'] = $value->ip;
            $tempdata['location'] = $value->location($QQWry);
            $tempdata['datetime'] = $value->datetime();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => UnblockIp::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
