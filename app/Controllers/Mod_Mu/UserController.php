<?php

namespace App\Controllers\Mod_Mu;

use App\Models\Node;
use App\Models\TrafficLog;
use App\Models\User;
use App\Models\NodeOnlineLog;
use App\Models\Ip;
use App\Models\DetectLog;
use App\Controllers\BaseController;
use App\Utils\Tools;

class UserController extends BaseController
{
    // User List
    public function index($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $node_id = $params['node_id'];
		$node=new Node();
		if($node_id=='0'){
			$node = Node::where("node_ip",$_SERVER["REMOTE_ADDR"])->first();
			$node_id=$node->id;
		}
		else{
			$node = Node::where("id", "=", $node_id)->first();
			if ($node == null) {
				$res = [
					"ret" => 0
				];
				return $this->echoJson($response, $res);
			}
		}
        $node->node_heartbeat=time();
        $node->save();

        if ($node->node_group!=0) {
            $users_raw = User::where(
                function ($query) use ($node){
                    $query->where(
                      function ($query1) use ($node){
                          $query1->where("class", ">=", $node->node_class)
                              ->where("node_group", "=", $node->node_group);
                      }
                    )->orwhere('is_admin', 1);
                }
            )
            ->where("enable", 1)->where("expire_in", ">", date("Y-m-d H:i:s"))->get();
        } else {
            $users_raw = User::where(
                function ($query) use ($node){
                    $query->where(
                      function ($query1) use ($node){
                          $query1->where("class", ">=", $node->node_class);
                      }
                    )->orwhere('is_admin', 1);
                }
            )->where("enable", 1)->where("expire_in", ">", date("Y-m-d H:i:s"))->get();
        }
        if ($node->node_bandwidth_limit!=0) {
            if ($node->node_bandwidth_limit < $node->node_bandwidth) {
                $users=null;

                $res = [
                    "ret" => 1,
                    "data" => $users
                ];
                return $this->echoJson($response, $res);
            }
        }

        $users = array();

        $key_list = array('email', 'method', 'obfs', 'obfs_param', 'protocol', 'protocol_param',
                'forbidden_ip', 'forbidden_port', 'node_speedlimit', 'disconnect_ip',
                'is_multi_user', 'id', 'port', 'passwd', 'u', 'd');

        foreach ($users_raw as $user_raw) {
            if ($user_raw->transfer_enable > $user_raw->u + $user_raw->d) {
                $user_raw = Tools::keyFilter($user_raw, $key_list);
                $user_raw->uuid = $user_raw->getUuid();
                array_push($users, $user_raw);
            }
        }

        $res = [
            "ret" => 1,
            "data" => $users
        ];
        return $this->echoJson($response, $res);
    }

    //   Update Traffic
    public function addTraffic($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $this_time_total_bandwidth = 0;
        $node_id = $params['node_id'];
		if($node_id=='0'){
			$node = Node::where("node_ip",$_SERVER["REMOTE_ADDR"])->first();
			$node_id=$node->id;
		}
        $node = Node::find($node_id);

        if ($node == null) {
            $res = [
                "ret" => 0
            ];
            return $this->echoJson($response, $res);
        }

        if (count($data) > 0) {
            foreach ($data as $log) {
                $u = $log['u'];
                $d = $log['d'];
                $user_id = $log['user_id'];

                $user = User::find($user_id);

                if($user == NULL) {
                    continue;
                }

                $user->t = time();
                $user->u += $u * $node->traffic_rate;
                $user->d += $d * $node->traffic_rate;
                $this_time_total_bandwidth += $u + $d;
                if (!$user->save()) {
                    $res = [
                        "ret" => 0,
                        "data" => "update failed",
                    ];
                    return $this->echoJson($response, $res);
                }

                // log
                $traffic = new TrafficLog();
                $traffic->user_id = $user_id;
                $traffic->u = $u;
                $traffic->d = $d;
                $traffic->node_id = $node_id;
                $traffic->rate = $node->traffic_rate;
                $traffic->traffic = Tools::flowAutoShow(($u + $d) * $node->traffic_rate);
                $traffic->log_time = time();
                $traffic->save();
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
            "ret" => 1,
            "data" => "ok",
        ];
        return $this->echoJson($response, $res);
    }

    public function addAliveIp($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $node_id = $params['node_id'];
		if($node_id=='0'){
			$node = Node::where("node_ip",$_SERVER["REMOTE_ADDR"])->first();
			$node_id=$node->id;
		}
        $node = Node::find($node_id);

        if ($node == null) {
            $res = [
                "ret" => 0
            ];
            return $this->echoJson($response, $res);
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
            "ret" => 1,
            "data" => "ok",
        ];
        return $this->echoJson($response, $res);
    }

    public function addDetectLog($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $node_id = $params['node_id'];
		if($node_id=='0'){
			$node = Node::where("node_ip",$_SERVER["REMOTE_ADDR"])->first();
			$node_id=$node->id;
		}
        $node = Node::find($node_id);

        if ($node == null) {
            $res = [
                "ret" => 0
            ];
            return $this->echoJson($response, $res);
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
            "ret" => 1,
            "data" => "ok",
        ];
        return $this->echoJson($response, $res);
    }
}
