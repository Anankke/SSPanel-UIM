<?php

namespace App\Controllers\Mod_Mu;

use App\Controllers\BaseController;
use App\Models\{
    Ip,
    Node,
    User,
    DetectLog,
    NodeOnlineLog
};
use App\Utils\Tools;
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class UserController extends BaseController
{
    /**
     * User List
     *
     * @param \Slim\Http\Request    $request
     * @param \Slim\Http\Response   $response
     * @param array                 $args
     *
     * @return \Slim\Http\Response
     */
    public function index($request, $response, $args): ResponseInterface
    {
        $node_id = $request->getQueryParam('node_id', '0');

        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        } else {
            $node = Node::where('id', '=', $node_id)->first();
            if ($node == null) {
                return $response->withJson([
                    'ret' => 0,
                ]);
            }
        }
        $node->node_heartbeat = time();
        $node->save();

        // 节点流量耗尽则返回 null
        if (($node->node_bandwidth_limit != 0) && $node->node_bandwidth_limit < $node->node_bandwidth) {
            $users = null;
            return $response->withJson([
                'ret'  => 1,
                'data' => $users
            ]);
        }

        if (in_array($node->sort, [0, 10]) && $node->mu_only != -1) {
            $mu_port_migration = $_ENV['mu_port_migration'];
            $muPort = Tools::get_MuOutPortArray($node->server);
        } else {
            $mu_port_migration = false;
        }

        /*
         * 1. 请不要把管理员作为单端口承载用户
         * 2. 请不要把真实用户作为单端口承载用户
         */
        $users_raw = User::where(
            static function ($query) use ($node) {
                $query->where(
                    static function ($query1) use ($node) {
                        if ($node->node_group != 0) {
                            $query1->where('class', '>=', $node->node_class)
                                ->where('node_group', '=', $node->node_group);
                        } else {
                            $query1->where('class', '>=', $node->node_class);
                        }
                    }
                )->orwhere('is_admin', 1);
            }
        )->where('enable', 1)->where('expire_in', '>', date('Y-m-d H:i:s'))->get();

        $users = array();

        if ($node->sort == 14) {
            $key_list = array('node_speedlimit', 'u', 'd', 'transfer_enable', 'id', 'node_connector', 'uuid', 'alive_ip');
        } elseif ($node->sort == 11) {
            $key_list = array('node_speedlimit', 'u', 'd', 'transfer_enable', 'id', 'node_connector', 'uuid', 'alive_ip');
        } else {
            $key_list = array('method', 'obfs', 'obfs_param', 'protocol', 'protocol_param', 'node_speedlimit',
                'is_multi_user', 'u', 'd', 'transfer_enable', 'id', 'port', 'passwd', 'node_connector', 'alive_ip');
        }

        foreach ($users_raw as $user_raw) {
            if ($user_raw->node_connector != 0) {
                $user_raw->alive_ip = (new \App\Models\Ip)->getUserAliveIpCount($user_raw->id);
            }
            if ($user_raw->transfer_enable <= $user_raw->u + $user_raw->d) {
                if ($_ENV['keep_connect'] === true) {
                    // 流量耗尽用户限速至 1Mbps
                    $user_raw->node_speedlimit = 1;
                } else {
                    continue;
                }
            }
            if ($mu_port_migration === true && $user_raw->is_multi_user != 0) {
                // 下发偏移后端口
                if ($muPort['type'] == 0) {
                    if (in_array($user_raw->port, array_keys($muPort['port']))) {
                        $user_raw->port = $muPort['port'][$user_raw->port];
                    }
                } else {
                    $user_raw->port = ($user_raw->port + $muPort['type']);
                }
            }
            $user_raw = Tools::keyFilter($user_raw, $key_list);
            $users[] = $user_raw;
        }

        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($users);
        if ($header_etag == $etag){
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson([
            'ret'  => 1,
            'data' => $users
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function addTraffic($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $this_time_total_bandwidth = 0;
        $node_id = $params['node_id'];
        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);

        if ($node == null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        if (count($data) > 0) {
            foreach ($data as $log) {
                $u = $log['u'];
                $d = $log['d'];
                $user_id = $log['user_id'];
                $user = User::find($user_id);

                if ($user == null) {
                    continue;
                }

                $user->t = time();
                $user->u += $u * $node->traffic_rate;
                $user->d += $d * $node->traffic_rate;
                $this_time_total_bandwidth += $u + $d;
                if (!$user->save()) {
                    $res = [
                        'ret' => 0,
                        'data' => 'update failed',
                    ];
                    return $response->withJson($res);
                }
            }
        }

        $node->node_bandwidth += $this_time_total_bandwidth;
        $node->save();

        $online_log = new NodeOnlineLog();
        $online_log->node_id = $node_id;
        $online_log->online_user = count($data);
        $online_log->log_time = time();
        $online_log->save();

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function addAliveIp($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $node_id = $params['node_id'];
        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);

        if ($node == null) {
            $res = [
                'ret' => 0
            ];
            return $response->withJson($res);
        }
        if (count($data) > 0) {
            foreach ($data as $log) {
                $ip = $log['ip'];
                $userid = $log['user_id'];

                // log
                $ip_log = new Ip();
                $ip_log->userid = $userid;
                $ip_log->nodeid = $node_id;
                $ip_log->ip = $ip;
                $ip_log->datetime = time();
                $ip_log->save();
            }
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function addDetectLog($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $node_id = $params['node_id'];
        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);

        if ($node == null) {
            $res = [
                'ret' => 0
            ];
            return $response->withJson($res);
        }

        if (count($data) > 0) {
            foreach ($data as $log) {
                $list_id = $log['list_id'];
                $user_id = $log['user_id'];

                // log
                $detect_log = new DetectLog();
                $detect_log->user_id = $user_id;
                $detect_log->list_id = $list_id;
                $detect_log->node_id = $node_id;
                $detect_log->datetime = time();
                $detect_log->save();
            }
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }
}
