<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Node;
use App\Utils\{
    Tools,
    Radius,
    Telegram,
    CloudflareDriver,
    DatatablesHelper
};
use App\Services\Config;
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class NodeController extends AdminController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args): ResponseInterface
    {
        $table_config['total_column'] = array(
            'op'                      => '操作',
            'id'                      => 'ID',
            'name'                    => '节点名称',
            'type'                    => '显示与隐藏',
            'sort'                    => '类型',
            'server'                  => '节点地址',
            'outaddress'              => '出口地址',
            'node_ip'                 => '节点IP',
            'info'                    => '节点信息',
            'status'                  => '状态',
            'traffic_rate'            => '流量比率',
            'node_group'              => '节点群组',
            'node_class'              => '节点等级',
            'node_speedlimit'         => '节点限速/Mbps',
            'node_bandwidth'          => '已走流量/GB',
            'node_bandwidth_limit'    => '流量限制/GB',
            'bandwidthlimit_resetday' => '流量重置日',
            'node_heartbeat'          => '上一次活跃时间',
            'custom_method'           => '自定义加密',
            'custom_rss'              => '自定义协议以及混淆',
            'mu_only'                 => '只启用单端口多用户'
        );
        $table_config['default_show_column'] = array('op', 'id', 'name', 'sort');
        $table_config['ajax_url'] = 'node/ajax';

        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/node/index.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function create($request, $response, $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->display('admin/node/create.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function add($request, $response, $args): ResponseInterface
    {
        $node                   = new Node();
        $node->name             = $request->getParam('name');
        $node->server           = trim($request->getParam('server'));
        $node->method           = $request->getParam('method');
        $node->custom_method    = $request->getParam('custom_method');
        $node->custom_rss       = $request->getParam('custom_rss');
        $node->mu_only          = $request->getParam('mu_only');
        $node->traffic_rate     = $request->getParam('rate');
        $node->info             = $request->getParam('info');
        $node->type             = $request->getParam('type');
        $node->node_group       = $request->getParam('group');
        $node->node_speedlimit  = $request->getParam('node_speedlimit');
        $node->status           = $request->getParam('status');
        $node->sort             = $request->getParam('sort');

        $req_node_ip = trim($request->getParam('node_ip'));
        if ($req_node_ip == '') {
            $req_node_ip = $node->server;
        }

        $nodeSort = [2, 5, 9, 999];     // 无需更新 IP 的节点类型
        if (!in_array($node->sort, $nodeSort)) {
            $server_list = explode(';', $node->server);
            if (!Tools::is_ip($server_list[0])) {
                $node->node_ip = gethostbyname($server_list[0]);
            } else {
                $node->node_ip = $req_node_ip;
            }
            if ($node->node_ip == '') {
                return $response->withJson(
                    [
                        'ret' => 0,
                        'msg' => '获取节点IP失败，请检查您输入的节点地址是否正确！'
                    ]
                );
            }
        } else {
            $node->node_ip = '';
        }

        if ($node->sort == 1) {
            Radius::AddNas($node->node_ip, $request->getParam('server'));
        }
        $node->node_class                 = $request->getParam('class');
        $node->node_bandwidth_limit       = $request->getParam('node_bandwidth_limit') * 1024 * 1024 * 1024;
        $node->bandwidthlimit_resetday    = $request->getParam('bandwidthlimit_resetday');

        $node->save();

        if ($_ENV['cloudflare_enable'] == true) {
            $domain_name = explode('.' . $_ENV['cloudflare_name'], $node->server);
            CloudflareDriver::updateRecord($domain_name[0], $node->node_ip);
        }

        if (Config::getconfig('Telegram.bool.AddNode')) {
            Telegram::Send(
                str_replace(
                    '%node_name%',
                    $request->getParam('name'),
                    Config::getconfig('Telegram.string.AddNode')
                )
            );
        }

        return $response->withJson(
            [
                'ret' => 1,
                'msg' => '节点添加成功'
            ]
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function edit($request, $response, $args): ResponseInterface
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
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function update($request, $response, $args): ResponseInterface
    {
        $id                     = $args['id'];
        $node                   = Node::find($id);
        $node->name             = $request->getParam('name');
        $node->node_group       = $request->getParam('group');
        $node->server           = trim($request->getParam('server'));
        $node->method           = $request->getParam('method');
        $node->custom_method    = $request->getParam('custom_method');
        $node->custom_rss       = $request->getParam('custom_rss');
        $node->mu_only          = $request->getParam('mu_only');
        $node->traffic_rate     = $request->getParam('rate');
        $node->info             = $request->getParam('info');
        $node->node_speedlimit  = $request->getParam('node_speedlimit');
        $node->type             = $request->getParam('type');
        $node->sort             = $request->getParam('sort');

        $req_node_ip = trim($request->getParam('node_ip'));
        if ($req_node_ip == '') {
            $req_node_ip = $node->server;
        }

        $success = true;
        $nodeSort = [2, 5, 9, 999];     // 无需更新 IP 的节点类型
        if (!in_array($node->sort, $nodeSort)) {
            $server_list = explode(';', $node->server);
            if (!Tools::is_ip($server_list[0])) {
                $success = $node->changeNodeIp($server_list[0]);
            } else {
                $success = $node->changeNodeIp($req_node_ip);
            }
        } else {
            $node->node_ip = '';
        }

        if (!$success) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '更新节点IP失败，请检查您输入的节点地址是否正确！'
                ]
            );
        }

        if (in_array($node->sort, array(0, 10, 11, 12))) {
            Tools::updateRelayRuleIp($node);
        }

        if ($node->sort == 1) {
            $SS_Node = Node::where('sort', '=', 0)->where('server', '=', $request->getParam('server'))->first();
            if ($SS_Node != null) {
                if ($SS_Node->node_heartbeat == 0 || time() - $SS_Node->node_heartbeat < 300) {
                    Radius::AddNas(gethostbyname($request->getParam('server')), $request->getParam('server'));
                }
            } else {
                Radius::AddNas(gethostbyname($request->getParam('server')), $request->getParam('server'));
            }
        }

        $node->status                     = $request->getParam('status');
        $node->node_class                 = $request->getParam('class');
        $node->node_bandwidth_limit       = $request->getParam('node_bandwidth_limit') * 1024 * 1024 * 1024;
        $node->bandwidthlimit_resetday    = $request->getParam('bandwidthlimit_resetday');

        $node->save();

        if (Config::getconfig('Telegram.bool.UpdateNode')) {
            Telegram::Send(
                str_replace(
                    '%node_name%',
                    $request->getParam('name'),
                    Config::getconfig('Telegram.string.UpdateNode')
                )
            );
        }

        return $response->withJson(
            [
                'ret' => 1,
                'msg' => '修改成功'
            ]
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function delete($request, $response, $args): ResponseInterface
    {
        $id = $request->getParam('id');
        $node = Node::find($id);
        if ($node->sort == 1) {
            Radius::DelNas($node->node_ip);
        }

        if (!$node->delete()) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '删除失败'
                ]
            );
        }

        if (Config::getconfig('Telegram.bool.DeleteNode')) {
            Telegram::Send(
                str_replace(
                    '%node_name%',
                    $node->name,
                    Config::getconfig('Telegram.string.DeleteNode')
                )
            );
        }

        return $response->withJson(
            [
                'ret' => 1,
                'msg' => '删除成功'
            ]
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax($request, $response, $args): ResponseInterface
    {
        //得到排序的方式
        $order        = $request->getParam('order')[0]['dir'];
        //得到排序字段的下标
        $order_column = $request->getParam('order')[0]['column'];
        //根据排序字段的下标得到排序字段
        $order_field  = $request->getParam('columns')[$order_column]['data'];
        $limit_start  = $request->getParam('start');
        $limit_length = $request->getParam('length');
        $search       = $request->getParam('search')['value'];

        if ($order_field == 'outaddress' || $order_field == 'op') {
            $order_field = 'server';
        }

        $nodes          = [];
        $query = Node::query();
        if ($search) {
            $v          = (int) (new DatatablesHelper())->query('select version()')[0]['version()'];
            $like_str   = ($v < 8 ? 'LIKE' : 'LIKE binary');
            $query->where('id', 'LIKE', "%$search%")
                ->orwhere('name', 'LIKE', "%$search%")
                ->orwhere('type', 'LIKE', "%$search%")
                ->orwhere('sort', 'LIKE', "%$search%")
                ->orwhere('server', 'LIKE', "%$search%")
                ->orwhere('node_ip', 'LIKE', "%$search%")
                ->orwhere('info', 'LIKE', "%$search%")
                ->orwhere('status', 'LIKE', "%$search%")
                ->orwhere('traffic_rate', 'LIKE', "%$search%")
                ->orwhere('node_group', 'LIKE', "%$search%")
                ->orwhere('node_class', 'LIKE', "%$search%")
                ->orwhere('node_speedlimit', 'LIKE', "%$search%")
                ->orwhere('node_bandwidth', 'LIKE', "%$search%")
                ->orwhere('node_bandwidth_limit', 'LIKE', "%$search%")
                ->orwhere('bandwidthlimit_resetday', 'LIKE', "%$search%")
                ->orwhere('node_heartbeat', $like_str, "%$search%")
                ->orwhere('custom_method', 'LIKE', "%$search%")
                ->orwhere('custom_rss', 'LIKE', "%$search%")
                ->orwhere('mu_only', 'LIKE', "%$search%");
        }
        $query_count = clone $query;
        $nodes = $query->orderByRaw($order_field . ' ' . $order)
            ->skip($limit_start)->limit($limit_length)
            ->get();
        $count_filtered = $query_count->count();

        $data = [];
        foreach ($nodes as $node) {
            $tempdata = [];
            $tempdata['op']   = '<a class="btn btn-brand" ' . ($node->sort == 999 ? 'disabled' : 'href="/admin/node/' . $node->id . '/edit"') . '>编辑</a>
                <a class="btn btn-brand-accent" ' . ($node->sort == 999 ? 'disabled' : 'id="delete" value="' . $node->id . '" href="javascript:void(0);" onClick="delete_modal_show(\'' . $node->id . '\')"') . '>删除</a>';
            $tempdata['id']   = $node->id;
            $tempdata['name'] = $node->name;
            $tempdata['type'] = ((bool) $node->type ? '显示' : '隐藏');
            switch ($node->sort) {
                case 0:
                    $sort = 'Shadowsocks';
                    break;
                case 1:
                    $sort = 'VPN/Radius基础';
                    break;
                case 2:
                    $sort = 'SSH';
                    break;
                case 5:
                    $sort = 'Anyconnect';
                    break;
                case 9:
                    $sort = 'Shadowsocks - 单端口多用户';
                    break;
                case 10:
                    $sort = 'Shadowsocks - 中转';
                    break;
                case 11:
                    $sort = 'V2Ray 节点';
                    break;
                case 12:
                    $sort = 'V2Ray - 中转';
                    break;
                case 13:
                    $sort = 'Shadowsocks - V2Ray-Plugin&Obfs';
                    break;
                case 14:
                    $sort = 'Trojan';
                    break;
                default:
                    $sort = '系统保留';
            }
            $tempdata['sort']                       = $sort;
            $tempdata['server']                     = $node->server;
            $tempdata['outaddress']                 = $node->getOutServer();
            $tempdata['node_ip']                    = $node->node_ip;
            $tempdata['info']                       = $node->info;
            $tempdata['status']                     = $node->status;
            $tempdata['traffic_rate']               = $node->traffic_rate;
            $tempdata['node_group']                 = $node->node_group;
            $tempdata['node_class']                 = $node->node_class;
            $tempdata['node_speedlimit']            = $node->node_speedlimit;
            $tempdata['node_bandwidth']             = Tools::flowToGB($node->node_bandwidth);
            $tempdata['node_bandwidth_limit']       = Tools::flowToGB($node->node_bandwidth_limit);
            $tempdata['bandwidthlimit_resetday']    = $node->bandwidthlimit_resetday;
            $tempdata['node_heartbeat']             = date('Y-m-d H:i:s', $node->node_heartbeat);
            $tempdata['custom_method']              = ((bool) $node->custom_method ? '启用' : '关闭');
            $tempdata['custom_rss']                 = ((bool) $node->custom_rss ? '启用' : '关闭');
            $tempdata['mu_only']                    = ($node->mu_only == 1 ? '启用' : '关闭');

            $data[] = $tempdata;
        }
        $info = [
            'draw'            => $request->getParam('draw'), // ajax请求次数，作为标识符
            'recordsTotal'    => Node::count(),
            'recordsFiltered' => $count_filtered,
            'data'            => $data,
        ];

        return $response->withJson($info);
    }
}
