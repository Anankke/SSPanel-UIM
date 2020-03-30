<?php

namespace App\Controllers\User;

use App\Controllers\UserController;
use App\Models\{
    Node,
    User,
    Relay
};
use App\Services\Config;
use App\Utils\Tools;
use ArrayObject;
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class RelayController extends UserController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args): ResponseInterface
    {
        $pageNum = 1;
        $user    = $this->user;
        if (isset($request->getQueryParams()['page'])) {
            $pageNum = $request->getQueryParams()['page'];
        }
        $logs          = Relay::where('user_id', $user->id)->orwhere('user_id', 0)->paginate(15, ['*'], 'page', $pageNum);
        $is_relay_able = Tools::is_protocol_relay($user);

        //链路表部分

        $nodes = Node::where(
            static function ($query) use ($user) {
                $query->Where('node_group', '=', $user->node_group)
                    ->orWhere('node_group', '=', 0);
            }
        )
            ->where('type', 1)
            ->where(
                static function ($query) {
                    $query->Where('sort', 10)
                        ->orWhere('sort', 12);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->orderBy('name')
            ->get();

        $pathset     = new ArrayObject();
        $relay_rules = Relay::where('user_id', $user->id)->orwhere('user_id', 0)->get();
        $mu_nodes    = Node::where('sort', 9)
            ->where('node_class', '<=', $user->class)
            ->where('type', '1')
            ->where(
                static function ($query) use ($user) {
                    $query->where('node_group', '=', $user->node_group)
                        ->orWhere('node_group', '=', 0);
                }
            )->get();

        foreach ($nodes as $node) {
            if ($node->mu_only == 0 || $node->mu_only == -1) {
                $relay_rule = Tools::pick_out_relay_rule($node->id, $user->port, $relay_rules);

                if ($relay_rule != null) {
                    $pathset = Tools::insertPathRule($relay_rule, $pathset, $user->port);
                }
            }

            if ($node->custom_rss == 1 || $node->mu_only == -1) {
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
                            $path->path = '<font color="#FF0000">' . $single_path->begin_node->name . '</font>' . ' → ' . $path->path;
                            $path->status = '阻断';
                        } else {
                            $path->path = $single_path->begin_node->name . ' → ' . $path->path;
                            $path->status = '通畅';
                        }

                        $pathset->offsetUnset($index);
                        continue;
                    }

                    if ($path->end_node->id == $single_path->begin_node->id) {
                        $path->end_node = $single_path->end_node;
                        if ($single_path->end_node->isNodeAccessable() == false) {
                            $path->path = $path->path . ' → ' . '<font color="#FF0000">' . $single_path->end_node->name . '</font>';
                            $path->status = '阻断';
                        } else {
                            $path->path = $path->path . ' → ' . $single_path->end_node->name;
                        }

                        $pathset->offsetUnset($index);
                        continue;
                    }
                }
            }
        }

        if ($request->getParam('json') == 1) {
            $res['ret'] = 1;
            foreach ($logs as $log) {
                $log->name          = $log->source_node_id == 0 ? '所有节点' : $log->Source_Node()->name;
                $log->dist_name     = $log->Dist_Node() == null ? '不进行中转' : $log->Dist_Node()->name;
                $log->port          = $log->port == 0 ? '所有端口' : $log->port;
                $log->source_class  = $log->Source_Node()->node_class;
                $log->dist_class    = $log->Dist_Node()->node_class;
            }
            $res['rules'] = $logs;
            $res['relay_able_protocol_list'] = Config::getSupportParam('relay_able_protocol');
            $res['is_relay_able'] = $is_relay_able;
            $res['pathset'] = $pathset;
            return $response->withJson($res);
        }

        return $response->write(
            $this->view()
                ->assign('rules', $logs)
                ->assign('relay_able_protocol_list', Config::getSupportParam('relay_able_protocol'))
                ->assign('is_relay_able', $is_relay_able)
                ->assign('pathset', $pathset)
                ->display('user/relay/index.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function create($request, $response, $args): ResponseInterface
    {
        $user         = $this->user;
        $source_nodes = Node::where(
            static function ($query) use ($user) {
                $query->Where('node_group', '=', $user->node_group)
                    ->orWhere('node_group', '=', 0);
            }
        )
            ->where('type', 1)
            ->where(
                static function ($query) {
                    $query->Where('sort', 10);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->orderBy('name')
            ->get();

        $dist_nodes = Node::where(
            static function ($query) use ($user) {
                $query->Where('node_group', '=', $user->node_group)
                    ->orWhere('node_group', '=', 0);
            }
        )
            ->where('type', 1)
            ->where(
                static function ($query) {
                    $query->Where('sort', 0)
                        ->orWhere('sort', 10);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->orderBy('name')
            ->get();

        $ports_raw = Node::where(
            static function ($query) use ($user) {
                $query->Where('node_group', '=', $user->node_group)
                    ->orWhere('node_group', '=', 0);
            }
        )
            ->where('type', 1)
            ->where('sort', 9)
            ->where('node_class', '<=', $user->class)
            ->orderBy('name')
            ->get();

        $ports = [];
        foreach ($ports_raw as $port_raw) {
            $mu_user = User::where('port', $port_raw->server)->first();
            if ($mu_user->is_multi_user == 1) {
                $ports[] = $port_raw->server;
            }
        }

        $ports[] = $user->port;
        $ports   = array_unique($ports);

        if ($request->getParam('json') == 1) {
            $res['ret']           = 1;
            $res['source_nodes']  = $source_nodes;
            $res['dist_nodes']    = $dist_nodes;
            $res['ports']         = $ports;
            return $response->withJson($res);
        }

        return $response->write(
            $this->view()
                ->assign('source_nodes', $source_nodes)
                ->assign('dist_nodes', $dist_nodes)
                ->assign('ports', $ports)
                ->display('user/relay/add.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function add($request, $response, $args): ResponseInterface
    {
        $user           = $this->user;
        $dist_node_id   = $request->getParam('dist_node');
        $source_node_id = $request->getParam('source_node');
        $port           = $request->getParam('port');
        $priority       = $request->getParam('priority');

        $source_node = Node::where('id', $source_node_id)
            ->where(
                static function ($query) use ($user) {
                    $query->Where('node_group', '=', $user->node_group)
                        ->orWhere('node_group', '=', 0);
                }
            )
            ->where('type', 1)
            ->where(
                static function ($query) {
                    $query->Where('sort', 10);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->first();
        if ($source_node == null) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '起源节点错误'
                ]
            );
        }
        if ($source_node->sort == 12) {
            $rules = Relay::Where('source_node_id', $source_node_id)->get();
            foreach ($rules as $rule) {
                if ($rule['user_id'] == 0 || $rule['user_id'] = $user->id) {
                    return $response->withJson(
                        [
                            'ret' => 0,
                            'msg' => 'V2Ray 中转一个起点一个 Rule'
                        ]
                    );
                }
            }
        }
        $dist_node = Node::where('id', $dist_node_id)
            ->where(
                static function ($query) use ($user) {
                    $query->Where('node_group', '=', $user->node_group)
                        ->orWhere('node_group', '=', 0);
                }
            )
            ->where('type', 1)
            ->where(
                static function ($query) {
                    $query->Where('sort', 0)
                        ->orWhere('sort', 10)
                        ->orWhere('sort', 11);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->first();

        if ($dist_node_id == -1) {
            $dist_node          = new Node();
            $dist_node->id      = -1;
            $dist_node->node_ip = '0.0.0.0';
            $dist_node->sort    = 10;
        }

        if ($dist_node == null) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '目标节点错误'
                ]
            );
        }

        $port_raw = Node::where('server', $port)->where(
            static function ($query) use ($user) {
                $query->Where('node_group', '=', $user->node_group)
                    ->orWhere('node_group', '=', 0);
            }
        )
            ->where('type', 1)
            ->where('sort', 9)
            ->where('node_class', '<=', $user->class)
            ->first();
        if (($port_raw == null && $port != $user->port)) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '端口错误'
                ]
            );
        }

        if (!Tools::is_protocol_relay($user)) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '为了中转的稳定，您需要在<a href="/user/edit">资料编辑</a>处设置协议为 auth_aes128_md5 或 auth_aes128_sha1 后方可设置中转规则！'
                ]
            );
        }

        $rule                 = new Relay();
        $rule->user_id        = $user->id;
        $rule->dist_node_id   = $dist_node_id;

        $dist_node_ip         = Tools::getRelayNodeIp($source_node, $dist_node);
        $rule->dist_ip        = $dist_node_ip;

        $rule->source_node_id = $source_node_id;
        $rule->port           = $port;
        $rule->priority       = min($priority, 99998);

        $ruleset              = Relay::where('user_id', $user->id)->orwhere('user_id', 0)->get();
        $maybe_rule_id        = Tools::has_conflict_rule($rule, $ruleset, 0, $rule->source_node_id);
        if ($maybe_rule_id != 0) {
            $rs['ret'] = 0;
            $rs['msg'] = '您即将添加的规则与规则 ID:' . $maybe_rule_id . ' 冲突！';
            if ($maybe_rule_id == -1) {
                $rs['msg'] = '您即将添加的规则可能会造成冲突！';
            }
            return $response->withJson($rs);
        }

        if (!$rule->save()) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '添加失败'
                ]
            );
        }
        return $response->withJson(
            [
                'ret' => 1,
                'msg' => '添加成功'
            ]
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function edit($request, $response, $args): ?ResponseInterface
    {
        $id   = $args['id'];
        $user = $this->user;
        $rule = Relay::where('id', $id)->where('user_id', $user->id)->first();

        if ($rule == null) {
            return null;
        }

        $source_nodes = Node::where(
            static function ($query) use ($user) {
                $query->Where('node_group', '=', $user->node_group)
                    ->orWhere('node_group', '=', 0);
            }
        )
            ->where('type', 1)
            ->where(
                static function ($query) {
                    $query->Where('sort', 10);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->orderBy('name')
            ->get();

        $dist_nodes = Node::where(
            static function ($query) use ($user) {
                $query->Where('node_group', '=', $user->node_group)
                    ->orWhere('node_group', '=', 0);
            }
        )
            ->where('type', 1)
            ->where(
                static function ($query) {
                    $query->Where('sort', 0)
                        ->orWhere('sort', 10)
                        ->orWhere('sort', 11);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->orderBy('name')
            ->get();

        $ports_raw = Node::where(
            static function ($query) use ($user) {
                $query->Where('node_group', '=', $user->node_group)
                    ->orWhere('node_group', '=', 0);
            }
        )
            ->where('type', 1)
            ->where('sort', 9)
            ->where('node_class', '<=', $user->class)
            ->orderBy('name')
            ->get();

        $ports = [];
        foreach ($ports_raw as $port_raw) {
            $mu_user = User::where('port', $port_raw->server)->first();
            if ($mu_user->is_multi_user == 1) {
                $ports[] = $port_raw->server;
            }
        }

        $ports[] = $user->port;
        $ports   = array_unique($ports);

        if ($request->getParam('json') == 1) {
            $res['ret']           = 1;
            $res['source_nodes']  = $source_nodes;
            $res['dist_nodes']    = $dist_nodes;
            $res['ports']         = $ports;
            $res['rule']          = $rule;
            return $response->withJson($res);
        }

        return $response->write(
            $this->view()
                ->assign('rule', $rule)
                ->assign('source_nodes', $source_nodes)
                ->assign('dist_nodes', $dist_nodes)
                ->assign('ports', $ports)
                ->display('user/relay/edit.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function update($request, $response, $args): ?ResponseInterface
    {
        $id   = $args['id'];
        $user = $this->user;
        $rule = Relay::where('id', $id)->where('user_id', $user->id)->first();

        if ($rule == null) {
            return null;
        }

        $dist_node_id   = $request->getParam('dist_node');
        $source_node_id = $request->getParam('source_node');
        $port           = $request->getParam('port');
        $priority       = $request->getParam('priority');

        $source_node = Node::where('id', $source_node_id)
            ->where(
                static function ($query) use ($user) {
                    $query->Where('node_group', '=', $user->node_group)
                        ->orWhere('node_group', '=', 0);
                }
            )
            ->where('type', 1)->where(
                static function ($query) {
                    $query->Where('sort', 10);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->first();
        if ($source_node == null) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '起源节点错误'
                ]
            );
        }

        $dist_node = Node::where('id', $dist_node_id)
            ->where(
                static function ($query) use ($user) {
                    $query->Where('node_group', '=', $user->node_group)
                        ->orWhere('node_group', '=', 0);
                }
            )
            ->where('type', 1)->where(
                static function ($query) {
                    $query->Where('sort', 0)
                        ->orWhere('sort', 10)
                        ->orWhere('sort', 11);
                }
            )
            ->where('node_class', '<=', $user->class)
            ->first();

        if ($dist_node_id == -1) {
            $dist_node          = new Node();
            $dist_node->id      = -1;
            $dist_node->node_ip = '0.0.0.0';
            $dist_node->sort    = 10;
        }

        if ($dist_node == null) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '目标节点错误'
                ]
            );
        }

        $port_raw = Node::where('server', $port)
            ->where(
                static function ($query) use ($user) {
                    $query->Where('node_group', '=', $user->node_group)
                        ->orWhere('node_group', '=', 0);
                }
            )
            ->where('type', 1)
            ->where('sort', 9)
            ->where('node_class', '<=', $user->class)
            ->first();
        $v2ray_port_raw = '';
        if ($dist_node->sort == 12 || $dist_node->sort == 11) {
            $node_explode = Tools::ssv2Array($dist_node->server);
            $v2ray_port_raw = $node_explode['port'];
        }
        if (($port_raw == null && $port != $user->port && $v2ray_port_raw == '') || ($v2ray_port_raw != '' && ($port != $user->port && $port != $v2ray_port_raw))) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '端口错误'
                ]
            );
        }

        if (!Tools::is_protocol_relay($user)) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '为了中转的稳定，您需要在<a href="/user/edit">资料编辑</a>处设置协议为 auth_aes128_md5 或 auth_aes128_sha1 后方可设置中转规则！'
                ]
            );
        }

        $rule->user_id        = $user->id;
        $rule->dist_node_id   = $dist_node_id;

        $dist_node_ip         = Tools::getRelayNodeIp($source_node, $dist_node);
        $rule->dist_ip        = $dist_node_ip;

        $rule->source_node_id = $source_node_id;
        $rule->port           = $port;
        $rule->priority       = min($priority, 99998);

        $ruleset              = Relay::where('user_id', $user->id)->orwhere('user_id', 0)->get();
        $maybe_rule_id        = Tools::has_conflict_rule($rule, $ruleset, $rule->id, $rule->source_node_id);
        if ($maybe_rule_id != 0) {
            $rs['ret'] = 0;
            $rs['msg'] = '您即将添加的规则与规则 ID:' . $maybe_rule_id . ' 冲突！';
            if ($maybe_rule_id == -1) {
                $rs['msg'] = '您即将添加的规则可能会造成冲突！';
            }
            return $response->withJson($rs);
        }

        if (!$rule->save()) {
            return $response->withJson(
                [
                    'ret' => 0,
                    'msg' => '修改失败'
                ]
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
    public function delete($request, $response, $args): ?ResponseInterface
    {
        $id   = $request->getParam('id');
        $user = $this->user;
        $rule = Relay::where('id', $id)->where('user_id', $user->id)->first();

        if ($rule == null) {
            return null;
        }
        if (!$rule->delete()) {
            $rs = [
                'ret' => 0,
                'msg' => '删除失败'
            ];
        } else {
            $rs = [
                'ret' => 1,
                'msg' => '删除成功'
            ];
        }
        return $response->withJson($rs);
    }
}
