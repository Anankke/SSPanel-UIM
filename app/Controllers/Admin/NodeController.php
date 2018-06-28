<?php

namespace App\Controllers\Admin;

use App\Models\Node;
use App\Utils\Radius;
use App\Utils\Telegram;
use App\Utils\Tools;
use App\Controllers\AdminController;

use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

// for port_group
use App\Models\UserMethod;
use App\Models\User;

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
                            "mu_only" => "只启用单端口多用户", "port_group" => "端口段");
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


        $node->port_group = $request->getParam('port_group');
        $node->min_port = $request->getParam('min_port');
        $node->max_port = $request->getParam('max_port');
        $port_group_array = [
            'min_port' => $node->min_port,
            'max_port' => $node->max_port
        ];

        $node->name =  $request->getParam('name');
        $node->server =  $request->getParam('server');
        $node->method =  $request->getParam('method');
        $node->custom_method =  $request->getParam('custom_method');
        $node->custom_rss =  $request->getParam('custom_rss');
        $node->mu_only =  $request->getParam('mu_only');
        $node->traffic_rate = $request->getParam('rate');
        $node->info = $request->getParam('info');
        $node->type = $request->getParam('type');
        $node->node_group = $request->getParam('group');
        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->status = $request->getParam('status');
        $node->sort = $request->getParam('sort');
        if ($node->sort == 0 || $node->sort == 1 || $node->sort == 10 || $node->sort == 11) {
            if ($request->getParam('node_ip') != '') {
                $node->node_ip = $request->getParam('node_ip');
            } else {
                if ($node->sort == 11) {
                    $server_list = explode(";", $request->getParam('server'));
                    $node->node_ip = gethostbyname($server_list[0]);
                } else {
                    $node->node_ip = gethostbyname($request->getParam('server'));
                }
            }
        } else {
            $node->node_ip="";
        }

        if ($node->sort==1) {
            Radius::AddNas($node->node_ip, $request->getParam('server'));
        }
        $node->node_class=$request->getParam('class');
        $node->node_bandwidth_limit=$request->getParam('node_bandwidth_limit')*1024*1024*1024;
        $node->bandwidthlimit_resetday=$request->getParam('bandwidthlimit_resetday');

        if (!$node->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "添加失败";
            return $response->getBody()->write(json_encode($rs));
        }

        Telegram::Send("新节点添加~".$request->getParam('name'));

        $node = Node::where('server',$request->getParam('server'))->where('name',$request->getParam('name'))->first();
        if ( ($node->sort == 0 || $node->sort == 9 || $node->sort == 10) && ($node->port_group != 0) ) {
            $users = User::all();
            foreach ($users as $user) {
                $newmethod = new UserMethod();
                $newmethod->user_id = $user->id;
                $newmethod->port = Tools::getAvPort_ForPortGroup($port_group_array, $node->id);
                $newmethod->passwd = $user->passwd;
                $newmethod->node_id = $node->id;
                $newmethod->method = $user->method;
                $newmethod->protocol = $user->protocol;
                $newmethod->protocol_param = $user->protocol_param;
                $newmethod->obfs = $user->obfs;
                $newmethod->obfs_param = $user->obfs_param;
                $newmethod->save();
            }
        }

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

        //  准备一份旧数据
        $old_node = clone $node;
        $node->port_group = $request->getParam('port_group');
        $node->min_port = $request->getParam('min_port');
        $node->max_port = $request->getParam('max_port');
        $port_group_array = [
            'min_port' => $node->min_port,
            'max_port' => $node->max_port
        ];

        // 判断是否需要修改user_method内的数据
        // 默认端口段则不修改
        $change_user_method=false;

        // 启用自定义端口段
        if ($node->port_group == 1){
            // 对比新旧自定义端口是否更改
            if ( ! ($old_node->min_port == $node->min_port && $old_node->max_port == $node->max_port) ){
                $change_user_method=true;
            }
        }

        $node->name =  $request->getParam('name');
        $node->node_group =  $request->getParam('group');
        $node->server =  $request->getParam('server');
        $node->method =  $request->getParam('method');
        $node->custom_method =  $request->getParam('custom_method');
        $node->custom_rss =  $request->getParam('custom_rss');
        $node->mu_only =  $request->getParam('mu_only');
        $node->traffic_rate = $request->getParam('rate');
        $node->info = $request->getParam('info');
        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->type = $request->getParam('type');
        $node->sort = $request->getParam('sort');

        if ($node->sort == 0 || $node->sort == 1 || $node->sort == 10 || $node->sort == 11) {
            if ($request->getParam('node_ip') != '') {
                $node->node_ip = $request->getParam('node_ip');
            } else {
                if ($node->isNodeOnline()) {
                    $succ = false;
                    if ($node->sort == 11) {
                        $server_list = explode(";", $request->getParam('server'));
                        $succ = $node->changeNodeIp($server_list[0]);
                    } else {
                        $succ = $node->changeNodeIp($request->getParam('server'));
                    }

                    if (!succ) {
                        $rs['ret'] = 0;
                        $rs['msg'] = "更新节点IP失败，请检查您输入的节点地址是否正确！";
                        return $response->getBody()->write(json_encode($rs));
                    }
                }
            }
        } else {
            $node->node_ip="";
        }

        if ($node->sort == 0 || $node->sort == 10) {
            Tools::updateRelayRuleIp($node);
        }

        if ($node->sort==1) {
            $SS_Node=Node::where('sort', '=', 0)->where('server', '=', $request->getParam('server'))->first();
            if ($SS_Node!=null) {
                if (time()-$SS_Node->node_heartbeat<300||$SS_Node->node_heartbeat==0) {
                    Radius::AddNas(gethostbyname($request->getParam('server')), $request->getParam('server'));
                }
            } else {
                Radius::AddNas(gethostbyname($request->getParam('server')), $request->getParam('server'));
            }
        }

        $node->status = $request->getParam('status');
        $node->node_class=$request->getParam('class');
        $node->node_bandwidth_limit=$request->getParam('node_bandwidth_limit')*1024*1024*1024;
        $node->bandwidthlimit_resetday=$request->getParam('bandwidthlimit_resetday');

        if ( ($node->sort == 0 || $node->sort == 9 || $node->sort == 10) ) {
            // 表中无数据，则新增
            // 有数据，则检查旧端口是否处于端口段内，否，则重置
            if ($change_user_method){
                $users = User::all();
                $log_exist = UserMethod::where('node_id',$node->id)->first();
                if ($log_exist==null) {
                    foreach ($users as $user) {
                        $newmethod = new UserMethod();
                        $newmethod->user_id = $user->id;
                        $newmethod->passwd = $user->passwd;
                        $newmethod->port = Tools::getAvPort_ForPortGroup($port_group_array, $node->id);
                        $newmethod->node_id = $node->id;
                        $newmethod->method = $user->method;
                        $newmethod->protocol = $user->protocol;
                        $newmethod->protocol_param = $user->protocol_param;
                        $newmethod->obfs = $user->obfs;
                        $newmethod->obfs_param = $user->obfs_param;
                        $newmethod->save();
                    }
                } else {
                    foreach ($users as $user) {
                        $log_exist = UserMethod::where('node_id',$node->id)->where('user_id',$user->id)->first();
                        if ($log_exist==null) {
                            $newmethod = new UserMethod();
                            $newmethod->user_id = $user->id;
                            $newmethod->passwd = $user->passwd;
                            $newmethod->port = Tools::getAvPort_ForPortGroup($port_group_array, $node->id);
                            $newmethod->node_id = $node->id;
                            $newmethod->method = $user->method;
                            $newmethod->protocol = $user->protocol;
                            $newmethod->protocol_param = $user->protocol_param;
                            $newmethod->obfs = $user->obfs;
                            $newmethod->obfs_param = $user->obfs_param;
                            $newmethod->save();
                        } else {
                            if ( ! ($log_exist->port <= $port_group_array['max_port'] && $log_exist->port >= $port_group_array['min_port']) ){
                                $log_exist->port=Tools::getAvPort_ForPortGroup($port_group_array, $node->id);
                                $log_exist->save();
                            }
                        }
                    }
                }
            }
        }

        if (!$node->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败";
            return $response->getBody()->write(json_encode($rs));
        }

        Telegram::Send("节点信息被修改~".$request->getParam('name'));

        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($rs));
    }


    public function delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $node = Node::find($id);
        if ($node->sort==1) {
            Radius::DelNas($node->node_ip);
        }

        $name = $node->name;

        if (!$node->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }

        Telegram::Send("节点被删除~".$name);

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
                              "mu_only" => "只启用单端口多用户", "port_group" => "端口段", "min_port" => "最小端口",
                              "max_port"=>"最大端口");
        $key_str = '';
        foreach($total_column as $single_key => $single_value) {
            if($single_key == 'op') {
                $key_str .= 'id as op';
                continue;
            }

            $key_str .= ','.$single_key;
        }
        $datatables->query('Select '.$key_str.' from ss_node');

        $datatables->edit('op', function ($data) {
            return '<a class="btn btn-brand" '.($data['sort'] == 999 ? 'disabled' : 'href="/admin/node/'.$data['id'].'/edit"').'>编辑</a>
                    <a class="btn btn-brand-accent" '.($data['sort'] == 999 ? 'disabled' : 'id="delete" value="'.$data['id'].'" href="javascript:void(0);" onClick="delete_modal_show(\''.$data['id'].'\')"').'>删除</a>';
        });

        $datatables->edit('node_bandwidth', function ($data) {
            return Tools::flowToGB($data['node_bandwidth']);
        });

        $datatables->edit('node_bandwidth_limit', function ($data) {
            return Tools::flowToGB($data['node_bandwidth_limit']);
        });

        $datatables->edit('sort', function ($data) {
            $sort = '';
            switch($data['sort']) {
                case 0:
                  $sort = 'Shadowsocks';
                  break;
                case 1:
                  $sort = 'VPN/Radius基础';
                  break;
                case 2:
                  $sort = 'SSH';
                  break;
                case 3:
                  $sort = 'PAC';
                  break;
                case 4:
                  $sort = 'APN文件外链';
                  break;
                case 5:
                  $sort = 'Anyconnect';
                  break;
                case 6:
                  $sort = 'APN';
                  break;
                case 7:
                  $sort = 'PAC PLUS(Socks 代理生成 PAC文件)';
                  break;
                case 8:
                  $sort = 'PAC PLUS PLUS(HTTPS 代理生成 PAC文件)';
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
            return 'row_1_'.$data['id'];
        });

        $datatables->edit('port_group', function ($data) {
            $port_group = '';
            switch($data['port_group']) {
                case 0:
                  $port_group = '默认';
                  break;
                case 1:
                  $port_group = $data['min_port']." - ".$data['max_port'];
                  break;
            }
            return $port_group;
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
