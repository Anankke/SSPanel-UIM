<?php

namespace App\Controllers\Admin;

use App\Models\Node;
use App\Utils\Radius;
use App\Utils\Telegram;
use App\Utils\Tools;
use App\Controllers\AdminController;
use App\Utils\CloudflareDriver;
use App\Services\Config;
use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

class NodeController extends AdminController
{
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = Array("op" => "操作", "id" => "ID", "name" => "节点名称",
            "type" => "显示与隐藏", "sort" => "类型",
            "server" => "节点地址", "node_ip" => "节点IP",
            "info" => "节点信息",
            "status" => "状态", "traffic_rate" => "流量比率", "node_group" => "节点群组",
            "node_class" => "节点等级", "node_speedlimit" => "节点限速/Mbps",
            "node_bandwidth" => "已走流量/GB", "node_bandwidth_limit" => "流量限制/GB",
            "bandwidthlimit_resetday" => "流量重置日", "node_heartbeat" => "上一次活跃时间",
            "custom_method" => "自定义加密", "custom_rss" => "自定义协议以及混淆",
            "mu_only" => "只启用单端口多用户");
        $table_config['default_show_column'] = Array("op", "id", "name", "sort");
        $table_config['ajax_url'] = 'node/ajax';

        return $this->view()->assign('table_config', $table_config)->display('admin/node/index.tpl');
    }

    public function create($request, $response, $args)
    {
        return $this->view()->display('admin/node/create.tpl');
    }

    public function add($request, $response, $args)
    {
        $node = new Node();
        $node->name = $request->getParam('name');
        $node->server = trim($request->getParam('server'));
        $node->method = $request->getParam('method');
        $node->custom_method = $request->getParam('custom_method');
        $node->custom_rss = $request->getParam('custom_rss');
        $node->mu_only = $request->getParam('mu_only');
        $node->traffic_rate = $request->getParam('rate');
        $node->info = $request->getParam('info');
        $node->type = $request->getParam('type');
        $node->node_group = $request->getParam('group');
        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->status = $request->getParam('status');
        $node->sort = $request->getParam('sort');

        $req_node_ip = trim($request->getParam('node_ip'));
        if ($req_node_ip == "") {
            $req_node_ip = $node->server;
        }

        if (in_array($node->sort, array(0, 1, 10, 11, 12, 13))) {
            $server_list = explode(";", $node->server);
            if (!Tools::is_ip($server_list[0])) {
                $node->node_ip = gethostbyname($server_list[0]);
            } else {
                $node->node_ip = $req_node_ip;
            }
            if ($node->node_ip == "") {
                $rs['ret'] = 0;
                $rs['msg'] = "获取节点IP失败，请检查您输入的节点地址是否正确！";
                return $response->getBody()->write(json_encode($rs));
            }
        } else {
            $node->node_ip = "";
        }

        if ($node->sort == 1) {
            Radius::AddNas($node->node_ip, $request->getParam('server'));
        }
        $node->node_class = $request->getParam('class');
        $node->node_bandwidth_limit = $request->getParam('node_bandwidth_limit') * 1024 * 1024 * 1024;
        $node->bandwidthlimit_resetday = $request->getParam('bandwidthlimit_resetday');

        $node->save();

        $domain_name = explode('.' . Config::get('cloudflare_name'), $node->server);
        if (Config::get('cloudflare_enable') == 'true') {
            CloudflareDriver::updateRecord($domain_name[0], $node->node_ip);
        }

        Telegram::Send("新节点添加~" . $request->getParam('name'));

        $rs['ret'] = 1;
        $rs['msg'] = "节点添加成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $node = Node::find($id);
        if ($node == null) {
        }
        return $this->view()->assign('node', $node)->display('admin/node/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $node = Node::find($id);

        $node->name = $request->getParam('name');
        $node->node_group = $request->getParam('group');
        $node->server = trim($request->getParam('server'));
        $node->method = $request->getParam('method');
        $node->custom_method = $request->getParam('custom_method');
        $node->custom_rss = $request->getParam('custom_rss');
        $node->mu_only = $request->getParam('mu_only');
        $node->traffic_rate = $request->getParam('rate');
        $node->info = $request->getParam('info');
        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->type = $request->getParam('type');
        $node->sort = $request->getParam('sort');

        $req_node_ip = trim($request->getParam('node_ip'));
        if ($req_node_ip == "") {
            $req_node_ip = $node->server;
        }

        $success = true;
        if (in_array($node->sort, array(0, 1, 10, 11, 12, 13))) {
            $server_list = explode(";", $node->server);
            if (!Tools::is_ip($server_list[0])) {
                $success = $node->changeNodeIp($server_list[0]);
            } else {
                $success = $node->changeNodeIp($req_node_ip);
            }
        } else {
            $node->node_ip = "";
        }

        if (!$success) {
            $rs['ret'] = 0;
            $rs['msg'] = "更新节点IP失败，请检查您输入的节点地址是否正确！";
            return $response->getBody()->write(json_encode($rs));
        }

        if (in_array($node->sort, array(0, 10, 11, 12, 13))) {
            Tools::updateRelayRuleIp($node);
        }

        if ($node->sort == 1) {
            $SS_Node = Node::where('sort', '=', 0)->where('server', '=', $request->getParam('server'))->first();
            if ($SS_Node != null) {
                if (time() - $SS_Node->node_heartbeat < 300 || $SS_Node->node_heartbeat == 0) {
                    Radius::AddNas(gethostbyname($request->getParam('server')), $request->getParam('server'));
                }
            } else {
                Radius::AddNas(gethostbyname($request->getParam('server')), $request->getParam('server'));
            }
        }

        $node->status = $request->getParam('status');
        $node->node_class = $request->getParam('class');
        $node->node_bandwidth_limit = $request->getParam('node_bandwidth_limit') * 1024 * 1024 * 1024;
        $node->bandwidthlimit_resetday = $request->getParam('bandwidthlimit_resetday');

        $node->save();

        Telegram::Send("节点信息被修改~" . $request->getParam('name'));

        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($rs));
    }


    public function delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $node = Node::find($id);
        if ($node->sort == 1) {
            Radius::DelNas($node->node_ip);
        }

        $name = $node->name;

        if (!$node->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }

        Telegram::Send("节点被删除~" . $name);

        $rs['ret'] = 1;
        $rs['msg'] = "删除成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function ajax($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());


        $total_column = Array("op" => "操作", "id" => "ID", "name" => "节点名称",
            "type" => "显示与隐藏", "sort" => "类型",
            "server" => "节点地址", "node_ip" => "节点IP",
            "info" => "节点信息",
            "status" => "状态", "traffic_rate" => "流量比率", "node_group" => "节点群组",
            "node_class" => "节点等级", "node_speedlimit" => "节点限速/Mbps",
            "node_bandwidth" => "已走流量/GB", "node_bandwidth_limit" => "流量限制/GB",
            "bandwidthlimit_resetday" => "流量重置日", "node_heartbeat" => "上一次活跃时间",
            "custom_method" => "自定义加密", "custom_rss" => "自定义协议以及混淆",
            "mu_only" => "只启用单端口多用户");
        $key_str = '';
        foreach ($total_column as $single_key => $single_value) {
            if ($single_key == 'op') {
                $key_str .= 'id as op';
                continue;
            }

            $key_str .= ',' . $single_key;
        }
        $datatables->query('Select ' . $key_str . ' from ss_node');

        $datatables->edit('op', function ($data) {
            return '<a class="btn btn-brand" ' . ($data['sort'] == 999 ? 'disabled' : 'href="/admin/node/' . $data['id'] . '/edit"') . '>编辑</a>
                    <a class="btn btn-brand-accent" ' . ($data['sort'] == 999 ? 'disabled' : 'id="delete" value="' . $data['id'] . '" href="javascript:void(0);" onClick="delete_modal_show(\'' . $data['id'] . '\')"') . '>删除</a>';
        });

        $datatables->edit('node_bandwidth', function ($data) {
            return Tools::flowToGB($data['node_bandwidth']);
        });

        $datatables->edit('node_bandwidth_limit', function ($data) {
            return Tools::flowToGB($data['node_bandwidth_limit']);
        });

        $datatables->edit('sort', function ($data) {
            $sort = '';
            switch ($data['sort']) {
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
                    $sort = 'Shadowsocks - V2Ray-Plugin';
                    break;
                default:
                    $sort = '系统保留';
            }
            return $sort;
        });

        $datatables->edit('type', function ($data) {
            return $data['type'] == 1 ? '显示' : '隐藏';
        });

        $datatables->edit('custom_method', function ($data) {
            return $data['custom_method'] == 1 ? '启用' : '关闭';
        });

        $datatables->edit('custom_rss', function ($data) {
            return $data['custom_rss'] == 1 ? '启用' : '关闭';
        });

        $datatables->edit('mu_only', function ($data) {
            return $data['mu_only'] == 1 ? '启用' : '关闭';
        });

        $datatables->edit('node_heartbeat', function ($data) {
            return date('Y-m-d H:i:s', $data['node_heartbeat']);
        });

        $datatables->edit('DT_RowId', function ($data) {
            return 'row_1_' . $data['id'];
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
