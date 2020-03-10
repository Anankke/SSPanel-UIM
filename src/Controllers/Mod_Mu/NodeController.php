<?php


namespace App\Controllers\Mod_Mu;

use App\Controllers\BaseController;
use App\Models\{
    Node,
    NodeInfoLog
};
use App\Services\Config;

class NodeController extends BaseController
{
    public function info($request, $response, $args)
    {
        $node_id = $args['id'];
        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $load = $request->getParam('load');
        $uptime = $request->getParam('uptime');
        $log = new NodeInfoLog();
        $log->node_id = $node_id;
        $log->load = $load;
        $log->uptime = $uptime;
        $log->log_time = time();
        if (!$log->save()) {
            $res = [
                'ret' => 0,
                'data' => 'update failed',
            ];
            return $this->echoJson($response, $res);
        }
        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $this->echoJson($response, $res);
    }

    public function get_info($request, $response, $args)
    {
        $node_id = $args['id'];
        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);
        if ($node == null) {
            $res = [
                'ret' => 0
            ];
            return $this->echoJson($response, $res);
        }
        if (in_array($node->sort, [0, 10])) {
            $node_explode = explode(';', $node->server);
            $node_server = $node_explode[0];
        } else {
            $node_server = $node->server;
        }
        $res = [
            'ret' => 1,
            'data' => [
                'node_group' => $node->node_group,
                'node_class' => $node->node_class,
                'node_speedlimit' => $node->node_speedlimit,
                'traffic_rate' => $node->traffic_rate,
                'mu_only' => $node->mu_only,
                'sort' => $node->sort,
                'server' => $node_server,
                'type' => 'ss-panel-v3-mod_Uim'
            ],
        ];
        return $this->echoJson($response, $res);
    }

    public function get_all_info($request, $response, $args)
    {
        $nodes = Node::where('node_ip', '<>', null)->where(
            static function ($query) {
                $query->where('sort', '=', 0)
                    ->orWhere('sort', '=', 10)
                    ->orWhere('sort', '=', 12)
                    ->orWhere('sort', '=', 13);
            }
        )->get();
        $res = [
            'ret' => 1,
            'data' => $nodes
        ];
        return $this->echoJson($response, $res);
    }

    public function getConfig($request, $response, $args)
    {
        $data = $request->getParsedBody();
        switch ($data['type']) {
            case ('database'):
                $db_config = Config::getDbConfig();
                $db_config['host'] = $this->getServerIP();
                $res = [
                    'ret' => 1,
                    'data' => $db_config,
                ];
                break;
            case ('webapi'):
                $webapiConfig = [];
                #todo
        }
        return $this->echoJson($response, $res);
    }

    private function getServerIP()
    {
        if (isset($_SERVER)) {
            if ($_SERVER['SERVER_ADDR']) {
                $serverIP = $_SERVER['SERVER_ADDR'];
            } else {
                $serverIP = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $serverIP = getenv('SERVER_ADDR');
        }
        return $serverIP;
    }
}
