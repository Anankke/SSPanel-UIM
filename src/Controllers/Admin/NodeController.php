<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Node;
use App\Services\Config;
use App\Utils\CloudflareDriver;
use App\Utils\Telegram;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class NodeController extends BaseController
{
    /**
     * 后台节点页面
     *
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        $table_config = [];
        $table_config['total_column'] = [
            'op' => '操作',
            'id' => 'ID',
            'name' => '节点名称',
            'type' => '显示与隐藏',
            'sort' => '类型',
            'server' => '节点地址',
            'outaddress' => '出口地址',
            'node_ip' => '节点IP',
            'info' => '节点信息',
            'status' => '状态',
            'traffic_rate' => '流量比率',
            'node_group' => '节点群组',
            'node_class' => '节点等级',
            'node_speedlimit' => '节点限速/Mbps',
            'node_bandwidth' => '已走流量/GB',
            'node_bandwidth_limit' => '流量限制/GB',
            'bandwidthlimit_resetday' => '流量重置日',
            'node_heartbeat' => '上一次活跃时间',
            'mu_only' => '只启用单端口多用户',
        ];
        $table_config['default_show_column'] = ['op', 'id', 'name', 'sort'];
        $table_config['ajax_url'] = 'node/ajax';

        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/node/index.tpl')
        );
    }

    /**
     * 后台创建节点页面
     *
     * @param array     $args
     */
    public function create(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->display('admin/node/create.tpl')
        );
    }

    /**
     * 后台添加节点
     *
     * @param array     $args
     */
    public function add(Request $request, Response $response, array $args): ResponseInterface
    {
        $node = new Node();
        $node->name = $request->getParam('name');
        $node->server = trim($request->getParam('server'));
        $node->mu_only = $request->getParam('mu_only');
        $node->traffic_rate = $request->getParam('rate');
        $node->info = $request->getParam('info');
        $node->type = $request->getParam('type');
        $node->node_group = $request->getParam('group');
        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->status = $request->getParam('status');
        $node->sort = $request->getParam('sort');

        if ($request->getParam('custom_config') !== null) {
            $node->custom_config = $request->getParam('custom_config');
        } else {
            $node->custom_config = '{}';
        }

        $req_node_ip = trim($request->getParam('node_ip'));
        $success = true;
        $server_list = explode(';', $node->server);

        if (Tools::isIp($req_node_ip)) {
            $success = $node->changeNodeIp($req_node_ip);
        } else {
            $success = $node->changeNodeIp($server_list[0]);
        }

        if (! $success) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '获取节点IP失败，请检查您输入的节点地址是否正确！',
            ]);
        }

        $node->node_class = $request->getParam('class');
        $node->node_bandwidth_limit = $request->getParam('node_bandwidth_limit') * 1024 * 1024 * 1024;
        $node->bandwidthlimit_resetday = $request->getParam('bandwidthlimit_resetday');

        $node->save();

        if ($_ENV['cloudflare_enable'] === true) {
            $domain_name = explode('.' . $_ENV['cloudflare_name'], $node->server);
            CloudflareDriver::updateRecord($domain_name[0], $node->node_ip);
        }

        if (Config::getconfig('Telegram.bool.AddNode')) {
            try {
                Telegram::send(
                    str_replace(
                        '%node_name%',
                        $request->getParam('name'),
                        Config::getconfig('Telegram.string.AddNode')
                    )
                );
            } catch (Exception $e) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '节点添加成功，但Telegram通知失败',
                    'node_id' => $node->id,
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '节点添加成功',
            'node_id' => $node->id,
        ]);
    }

    /**
     * 后台编辑指定节点页面
     *
     * @param array     $args
     */
    public function edit(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $node = Node::find($id);
        return $response->write(
            $this->view()
                ->assign('node', $node)
                ->display('admin/node/edit.tpl')
        );
    }

    /**
     * 后台更新指定节点内容
     *
     * @param array     $args
     */
    public function update(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $node = Node::find($id);
        $node->name = $request->getParam('name');
        $node->node_group = $request->getParam('group');
        $node->server = trim($request->getParam('server'));
        $node->mu_only = $request->getParam('mu_only');
        $node->traffic_rate = $request->getParam('rate');
        $node->info = $request->getParam('info');
        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->type = $request->getParam('type');
        $node->sort = $request->getParam('sort');

        if ($request->getParam('custom_config') !== null) {
            $node->custom_config = $request->getParam('custom_config');
        } else {
            $node->custom_config = '{}';
        }

        $req_node_ip = trim($request->getParam('node_ip'));

        $success = true;
        $server_list = explode(';', $node->server);

        if (Tools::isIp($req_node_ip)) {
            $success = $node->changeNodeIp($req_node_ip);
        } else {
            $success = $node->changeNodeIp($server_list[0]);
        }

        if (! $success) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '更新节点IP失败，请检查您输入的节点地址是否正确！',
            ]);
        }

        $node->status = $request->getParam('status');
        $node->node_class = $request->getParam('class');
        $node->node_bandwidth_limit = $request->getParam('node_bandwidth_limit') * 1024 * 1024 * 1024;
        $node->bandwidthlimit_resetday = $request->getParam('bandwidthlimit_resetday');

        $node->save();

        if (Config::getconfig('Telegram.bool.UpdateNode')) {
            try {
                Telegram::send(
                    str_replace(
                        '%node_name%',
                        $request->getParam('name'),
                        Config::getconfig('Telegram.string.UpdateNode')
                    )
                );
            } catch (Exception $e) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '修改成功，但Telegram通知失败',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    /**
     * 后台删除指定节点
     *
     * @param array     $args
     */
    public function delete(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $request->getParam('id');
        $node = Node::find($id);

        if (! $node->delete()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '删除失败',
            ]);
        }

        if (Config::getconfig('Telegram.bool.DeleteNode')) {
            try {
                Telegram::send(
                    str_replace(
                        '%node_name%',
                        $request->getParam('name'),
                        Config::getconfig('Telegram.string.DeleteNode')
                    )
                );
            } catch (Exception $e) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '删除成功，但Telegram通知失败',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    /**
     * 后台节点页面 AJAX
     *
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args): ResponseInterface
    {
        $query = Node::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['op'])) {
                    $order_field = 'id';
                }
                if (in_array($order_field, ['outaddress'])) {
                    $order_field = 'server';
                }
            }
        );

        $data = [];
        foreach ($query['datas'] as $value) {
            /** @var Node $value */

            $tempdata = [];
            $tempdata['op'] = '<a class="btn btn-brand" href="/admin/node/' . $value->id . '/edit">编辑</a> <a class="btn btn-brand-accent" id="delete" value="' . $value->id . '" href="javascript:void(0);" onClick="delete_modal_show(\'' . $value->id . '\')">删除</a>';
            $tempdata['id'] = $value->id;
            $tempdata['name'] = $value->name;
            $tempdata['type'] = $value->type();
            $tempdata['sort'] = $value->sort();
            $tempdata['server'] = $value->server;
            $tempdata['outaddress'] = $value->getOutAddress();
            $tempdata['node_ip'] = $value->node_ip;
            $tempdata['info'] = $value->info;
            $tempdata['status'] = $value->status;
            $tempdata['traffic_rate'] = $value->traffic_rate;
            $tempdata['node_group'] = $value->node_group;
            $tempdata['node_class'] = $value->node_class;
            $tempdata['node_speedlimit'] = $value->node_speedlimit;
            $tempdata['node_bandwidth'] = Tools::flowToGB($value->node_bandwidth);
            $tempdata['node_bandwidth_limit'] = Tools::flowToGB($value->node_bandwidth_limit);
            $tempdata['bandwidthlimit_resetday'] = $value->bandwidthlimit_resetday;
            $tempdata['node_heartbeat'] = $value->nodeHeartbeat();
            $tempdata['mu_only'] = $value->muOnly();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => Node::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
