<?php

namespace App\Controllers\Admin;

use App\Models\Relay;
use App\Models\Node;
use App\Models\User;
use App\Utils\Tools;
use App\Services\Auth;
use App\Controllers\AdminController;

use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

class RelayController extends AdminController
{
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array("op" => "操作", "id" => "ID", "user_id" => "用户ID",
            "user_name" => "用户名", "source_node_name" => "起源节点",
            "dist_node_name" => "目标节点", "port" => "端口", "priority" => "优先级");
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            array_push($table_config['default_show_column'], $column);
        }
        $table_config['ajax_url'] = 'relay/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/relay/index.tpl');
    }

    public function create($request, $response, $args)
    {
        $user = Auth::getUser();
        $source_nodes = Node::where(
            function ($query) {
                $query->Where('sort', 10)
                    ->orWhere('sort', 12);
            }
        )->orderBy('name')->get();
        foreach ($source_nodes as $node) {
            if ($node->sort == 12) {
                $node->name = $node->name . " 正在使用V2ray后端 ";
            }
        }
        $dist_nodes = Node::where(
            function ($query) {
                $query->Where('sort', 0)
                    ->orWhere('sort', 10)
                    ->orWhere('sort', 11)
                    ->orWhere('sort', 12);
            }
        )->orderBy('name')->get();

        foreach ($dist_nodes as $node) {
            if ($node->sort == 11 or $node->sort == 12) {
                $node_explode = Tools::ssv2Array($node->server);
                $node->name = $node->name . " 如果是V2ray后端 请设置成 " . $node_explode['port'];
            } else {
                $node->name = $node->name . " 如果是V2ray后端 请不要设置，用户页面设置 ";
            }
        }

        return $this->view()->assign('source_nodes', $source_nodes)->assign('dist_nodes', $dist_nodes)->display('admin/relay/add.tpl');
    }

    public function add($request, $response, $args)
    {
        $dist_node_id = $request->getParam('dist_node');
        $source_node_id = $request->getParam('source_node');
        $port = $request->getParam('port');
        $priority = $request->getParam('priority');
        $user_id = $request->getParam('user_id');

        $source_node = Node::where('id', $source_node_id)->first();
        if ($source_node == null) {
            $rs['ret'] = 0;
            $rs['msg'] = "起源节点错误。";
            return $response->getBody()->write(json_encode($rs));
        }

        if ($source_node->sort == 12) {
            $rules = Relay::Where('source_node_id', $source_node_id)->get();
            if (count($rules) > 0) {
                $rs['ret'] = 0;
                $rs['msg'] = "v2ray中转一个起点一个rule";
                return $response->getBody()->write(json_encode($rs));
            }
        }
        $dist_node = Node::where('id', $dist_node_id)->first();
        if ($dist_node == null) {
            $rs['ret'] = 0;
            $rs['msg'] = "目标节点错误。";
            return $response->getBody()->write(json_encode($rs));
        }

        $rule = new Relay();
        $rule->user_id = $user_id;
        $rule->dist_node_id = $dist_node_id;

        $dist_node_ip = Tools::getRelayNodeIp($source_node, $dist_node);
        $rule->dist_ip = $dist_node_ip;

        $rule->source_node_id = $source_node_id;
        $rule->port = $port;
        $rule->priority = $priority;

        if ($user_id == 0) {
            $ruleset = Relay::all();
        } else {
            $ruleset = Relay::where('user_id', $user_id)->orwhere('user_id', 0)->get();
        }
        $maybe_rule_id = Tools::has_conflict_rule($rule, $ruleset, 0, $rule->source_node_id);
        if ($maybe_rule_id != 0) {
            $rs['ret'] = 0;
            $rs['msg'] = "您即将添加的规则与规则 ID:" . $maybe_rule_id . " 冲突！";
            if ($maybe_rule_id == -1) {
                $rs['msg'] = "您即将添加的规则可能会造成冲突！";
            }
            return $response->getBody()->write(json_encode($rs));
        }

        if (!$rule->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "添加失败";
            return $response->getBody()->write(json_encode($rs));
        }

        $rs['ret'] = 1;
        $rs['msg'] = "添加成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function edit($request, $response, $args)
    {
        $id = $args['id'];

        $user = Auth::getUser();
        $rule = Relay::where('id', $id)->first();

        if ($rule == null) {
            exit(0);
        }

        $source_nodes = Node::where(
            function ($query) {
                $query->Where('sort', 10)
                    ->orWhere('sort', 12);
            }
        )->orderBy('name')->get();
        foreach ($source_nodes as $node) {
            if ($node->sort == 12) {
                $node->name = $node->name . " 正在使用V2ray后端 ";
            }
        }

        $dist_nodes = Node::where(
            function ($query) {
                $query->Where('sort', 0)
                    ->orWhere('sort', 10)
                    ->orWhere('sort', 11)
                    ->orWhere('sort', 12);
            }
        )->orderBy('name')->get();

        foreach ($dist_nodes as $node) {
            if ($node->sort == 11 or $node->sort == 12) {
                $node_explode = Tools::ssv2Array($node->server);
                $node->name = $node->name . " 如果是V2ray后端 请设置成" . $node_explode['port'];
            } else {
                $node->name = $node->name . " 如果是V2ray后端 请不要设置，用户页面设置 ";
            }
        }

        return $this->view()->assign('rule', $rule)->assign('source_nodes', $source_nodes)->assign('dist_nodes', $dist_nodes)->display('admin/relay/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $rule = Relay::where('id', $id)->first();

        if ($rule == null) {
            exit(0);
        }

        $dist_node_id = $request->getParam('dist_node');
        $source_node_id = $request->getParam('source_node');
        $port = $request->getParam('port');
        $user_id = $request->getParam('user_id');
        $priority = $request->getParam('priority');

        $source_node = Node::where('id', $source_node_id)->first();
        if ($source_node == null && $source_node_id != 0) {
            $rs['ret'] = 0;
            $rs['msg'] = "起源节点 ID 错误。";
            return $response->getBody()->write(json_encode($rs));
        }

        $dist_node = Node::where('id', $dist_node_id)->first();
        if ($dist_node == null) {
            $rs['ret'] = 0;
            $rs['msg'] = "目标节点 ID 错误。";
            return $response->getBody()->write(json_encode($rs));
        }

        $rule->user_id = $user_id;
        $rule->dist_node_id = $dist_node_id;

        $dist_node_ip = Tools::getRelayNodeIp($source_node, $dist_node);
        $rule->dist_ip = $dist_node_ip;

        $rule->source_node_id = $source_node_id;
        $rule->port = $port;
        $rule->priority = $priority;

        if ($user_id == 0) {
            $ruleset = Relay::all();
        } else {
            $ruleset = Relay::where('user_id', $user_id)->orwhere('user_id', 0)->get();
        }
        $maybe_rule_id = Tools::has_conflict_rule($rule, $ruleset, $rule->id, $rule->source_node_id);
        if ($maybe_rule_id != 0) {
            $rs['ret'] = 0;
            $rs['msg'] = "您即将添加的规则与规则 ID:" . $maybe_rule_id . " 冲突！";
            if ($maybe_rule_id == -1) {
                $rs['msg'] = "您即将添加的规则可能会造成冲突！";
            }
            return $response->getBody()->write(json_encode($rs));
        }


        if (!$rule->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败";
            return $response->getBody()->write(json_encode($rs));
        }

        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($rs));
    }


    public function delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $user = Auth::getUser();
        $rule = Relay::where('id', $id)->first();

        if ($rule == null) {
            exit(0);
        }

        if (!$rule->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "删除成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function path_search($request, $response, $args)
    {
        $uid = $args["id"];

        $user = User::find($uid);

        if ($user == null) {
            $pathset = new \ArrayObject();
            return $this->view()->assign('pathset', $pathset)->display('admin/relay/search.tpl');
        }

        $nodes = Node::where(
            function ($query) use ($user) {
                $query->Where("node_group", "=", $user->node_group)
                    ->orWhere("node_group", "=", 0);
            }
        )->where(
            function ($query) {
                $query->Where('sort', 10)
                    ->orWhere('sort', 12);
            }
        )->where("node_class", "<=", $user->class)->orderBy('name')->get();

        $pathset = new \ArrayObject();

        $relay_rules = Relay::where('user_id', $user->id)->orwhere('user_id', 0)->get();
        $mu_nodes = Node::where('sort', 9)->where('node_class', '<=', $user->class)->where("type", "1")->where(
            function ($query) use ($user) {
                $query->where("node_group", "=", $user->node_group)
                    ->orWhere("node_group", "=", 0);
            }
        )->get();

        foreach ($nodes as $node) {
            if ($node->mu_only == 0) {
                $relay_rule = Tools::pick_out_relay_rule($node->id, $user->port, $relay_rules);

                if ($relay_rule != null) {
                    $pathset = Tools::insertPathRule($relay_rule, $pathset, $user->port);
                }
            }

            if ($node->custom_rss == 1) {
                foreach ($mu_nodes as $mu_node) {
                    $mu_user = User::where('port', '=', $mu_node->server)->first();

                    if ($mu_user == null) {
                        continue;
                    }

                    if (!($mu_user->class >= $node->node_class && ($node->node_group == 0 || $node->node_group == $mu_user->node_group))) {
                        continue;
                    }

                    if ($mu_user->is_multi_user != 2) {
                        $relay_rule = Tools::pick_out_relay_rule($node->id, $mu_user->port, $relay_rules);

                        if ($relay_rule != null) {
                            $pathset = Tools::insertPathRule($relay_rule, $pathset, $mu_user->port);
                        }
                    }
                }
            }
        }

        foreach ($pathset as $path) {
            foreach ($pathset as $index => $single_path) {
                if ($path != $single_path && $path->port == $single_path->port) {
                    if ($single_path->end_node->id == $path->begin_node->id) {
                        $path->begin_node = $single_path->begin_node;
                        if ($path->begin_node->isNodeAccessable() == false) {
                            $path->path = '<font color="#FF0000">' . $single_path->begin_node->name . '</font>' . " → " . $path->path;
                            $path->status = "阻断";
                        } else {
                            $path->path = $single_path->begin_node->name . " → " . $path->path;
                            $path->status = "通畅";
                        }

                        $pathset->offsetUnset($index);
                        continue;
                    }

                    if ($path->end_node->id == $single_path->begin_node->id) {
                        $path->end_node = $single_path->end_node;
                        if ($single_path->end_node->isNodeAccessable() == false) {
                            $path->path = $path->path . " → " . '<font color="#FF0000">' . $single_path->end_node->name . '</font>';
                            $path->status = "阻断";
                        } else {
                            $path->path = $path->path . " → " . $single_path->end_node->name;
                        }

                        $pathset->offsetUnset($index);
                        continue;
                    }
                }
            }
        }

        return $this->view()->assign('pathset', $pathset)->display('admin/relay/search.tpl');
    }

    public function ajax_relay($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select relay.id as op,relay.id,relay.user_id,user.user_name,source_node.name as source_node_name,dist_node.name as dist_node_name,relay.port,relay.priority from relay,user,ss_node as source_node,ss_node as dist_node WHERE (relay.user_id = user.id or relay.user_id = 0) and source_node.id = relay.source_node_id and dist_node.id = relay.dist_node_id group by id');

        $datatables->edit('op', function ($data) {
            return '<a class="btn btn-brand" href="/admin/relay/' . $data['id'] . '/edit">编辑</a>
                    <a class="btn btn-brand-accent" id="delete" value="' . $data['id'] . '" href="javascript:void(0);" onClick="delete_modal_show(\'' . $data['id'] . '\')">删除</a>';
        });

        $datatables->edit('user_name', function ($data) {
            return ($data['user_id'] == 0 ? '全体用户' : $data['user_name']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}