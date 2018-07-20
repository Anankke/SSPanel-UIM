<?php

namespace App\Controllers\Mod_Mu;

use App\Models\DetectRule;
use App\Models\Relay;
use App\Models\BlockIp;
use App\Models\UnblockIp;
use App\Models\Speedtest;
use App\Models\Node;
use App\Models\Auto;
use App\Controllers\BaseController;
use App\Utils\Tools;

class FuncController extends BaseController
{
    public function ping($request, $response, $args)
    {
        $res = [
            "ret" => 1,
            "data" => 'pong'
        ];
        return $this->echoJson($response, $res);
    }

    public function get_detect_logs($request, $response, $args)
    {
        $rules = DetectRule::all();

        $res = [
            "ret" => 1,
            "data" => $rules
        ];
        return $this->echoJson($response, $res);
    }

    public function get_relay_rules($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $node_id = $params['node_id'];
		if($node_id=='0'){
			$node = Node::where("node_ip",$_SERVER["REMOTE_ADDR"])->first();
			$node_id=$node->id;
		}
        $rules = Relay::Where('source_node_id', $node_id)->get();

        $res = [
            "ret" => 1,
            "data" => $rules
        ];
        return $this->echoJson($response, $res);
    }

    public function get_blockip($request, $response, $args)
    {
        $block_ips = BlockIp::Where('datetime', '>', time() - 60)->get();

        $res = [
            "ret" => 1,
            "data" => $block_ips
        ];
        return $this->echoJson($response, $res);
    }

    public function get_unblockip($request, $response, $args)
    {
        $unblock_ips = UnblockIp::Where('datetime', '>', time() - 60)->get();

        $res = [
            "ret" => 1,
            "data" => $unblock_ips
        ];
        return $this->echoJson($response, $res);
    }

    public function addBlockIp($request, $response, $args)
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

                $exist_ip = BlockIp::where('ip', $ip)->first();
                if ($exist_ip != null) {
                    continue;
                }

                // log
                $ip_block = new BlockIp();
                $ip_block->ip = $ip;
                $ip_block->nodeid = $node_id;
                $ip_block->datetime = time();
                $ip_block->save();
            }
        }

        $res = [
            "ret" => 1,
            "data" => "ok",
        ];
        return $this->echoJson($response, $res);
    }

    public function addSpeedtest($request, $response, $args)
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
                // log
                $speedtest_log = new Speedtest();
                $speedtest_log->telecomping = $log['telecomping'];
                $speedtest_log->telecomeupload = $log['telecomeupload'];
                $speedtest_log->telecomedownload = $log['telecomedownload'];

                $speedtest_log->unicomping = $log['unicomping'];
                $speedtest_log->unicomupload = $log['unicomupload'];
                $speedtest_log->unicomdownload = $log['unicomdownload'];

                $speedtest_log->cmccping = $log['cmccping'];
                $speedtest_log->cmccupload = $log['cmccupload'];
                $speedtest_log->cmccdownload = $log['cmccdownload'];
                $speedtest_log->nodeid = $node_id;
                $speedtest_log->datetime = time();
                $speedtest_log->save();
            }
        }

        $res = [
            "ret" => 1,
            "data" => "ok",
        ];
        return $this->echoJson($response, $res);
    }

    public function get_autoexec($request, $response, $args)
    {
        $params = $request->getQueryParams();

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

        $autos_raw = Auto::where('datetime', '>', time() - 60)->where('type', '1')->get();

        $autos = array();

        foreach ($autos_raw as $auto_raw) {
            $has_exec = Auto::where('sign', $node_id.'-'.$auto_raw->id)->where('type', '2')->first();
            if ($has_exec == null) {
                array_push($autos, $auto_raw);
            }
        }

        $res = [
            "ret" => 1,
            "data" => $autos,
        ];
        return $this->echoJson($response, $res);
    }

    public function addAutoexec($request, $response, $args)
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
                // log
                $auto_log = new Auto();
                $auto_log->value = $log['value'];
                $auto_log->sign = $log['sign'];
                $auto_log->type = $log['type'];
                $auto_log->datetime = time();
                $auto_log->save();
            }
        }

        $res = [
            "ret" => 1,
            "data" => "ok",
        ];
        return $this->echoJson($response, $res);
    }
}
