<?php

namespace App\Controllers\Mu;

use App\Models\Node;
use App\Models\TrafficLog;
use App\Models\User;
use App\Controllers\BaseController;
use App\Utils\Tools;

class UserController extends BaseController
{
    // User List
    public function index($request, $response, $args)
    {
        $node = Node::where("node_ip", "=", $_SERVER["REMOTE_ADDR"])->where(
            function ($query) {
                $query->where("sort", "=", 0)
                    ->orWhere("sort", "=", 10);
            }
        )->first();
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
            if ($node->node_bandwidth_limit<$node->node_bandwidth) {
                $users=null;

                $res = [
                    "ret" => 1,
                    "data" => $users
                ];
                return $this->echoJson($response, $res);
            }
        }

        $key_list = array('method', 'id', 'port', 'passwd', 'u', 'd', 'enable',
                          't', 'transfer_enable', 'switch');

        $users_output = array();

        foreach ($users as $user_raw) {
            if ($user_raw->transfer_enable > $user_raw->u + $user_raw->d) {
                $user_raw = Tools::keyFilter($user_raw, $key_list);
                array_push($users_output, $user_raw);
            }
        }

        $res = [
            "ret" => 1,
            "msg" => "ok",
            "data" => $users
        ];
        return $this->echoJson($response, $res);
    }

    //   Update Traffic
    public function addTraffic($request, $response, $args)
    {
        $id = $args['id'];
        $u = $request->getParam('u');
        $d = $request->getParam('d');
        $nodeId = $request->getParam('node_id');
        $node = Node::find($nodeId);

        $node->node_bandwidth=$node->node_bandwidth+$d+$u;

        $node->save();


        $rate = $node->traffic_rate;
        $user = User::find($id);

        $user->t = time();
        $user->u = $user->u + ($u * $rate);
        $user->d = $user->d + ($d * $rate);
        if (!$user->save()) {
            $res = [
                "ret" => 0,
                "msg" => "update failed",
            ];
            //return $this->echoJson($response, $res);
        }
        // log
        $traffic = new TrafficLog();
        $traffic->user_id = $id;
        $traffic->u = $u;
        $traffic->d = $d;
        $traffic->node_id = $nodeId;
        $traffic->rate = $rate;
        $traffic->traffic = Tools::flowAutoShow(($u + $d) * $rate);
        $traffic->log_time = time();
        $traffic->save();

        $res = [
            "ret" => 1,
            "msg" => "ok",
        ];
        return $this->echoJson($response, $res);
    }
}
