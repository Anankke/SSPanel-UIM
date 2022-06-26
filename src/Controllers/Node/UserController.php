<?php

declare(strict_types=1);

namespace App\Controllers\Node;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Models\Ip;
use App\Models\Node;
use App\Models\NodeOnlineLog;
use App\Models\User;
use App\Services\DB;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use function in_array;
use function is_array;
use function json_decode;
use function json_encode;
use function sha1;
use function time;

final class UserController extends BaseController
{
    /**
     * GET /mod_mu/users
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     *
     * @return ResponseInterface
     */
    public function index($request, $response, $args): ResponseInterface
    {
        $node_id = $request->getQueryParam('node_id');

        if (!$node_id) {
            $node = Node::where('node_ip', $request->getServerParam('REMOTE_ADDR'))->first();
        } else {
            $node = Node::where('id', '=', $node_id)->first();
            if ($node === null) {
                return $response->withJson([
                    'ret' => 0,
                ]);
            }
        }
        $node->update(['node_heartbeat' => time()]);

        if (($node->node_bandwidth_limit !== 0) && $node->node_bandwidth_limit < $node->node_bandwidth) {
            return $response->withJson([
                'ret' => 1,
                'data' => [],
            ]);
        }

        if (in_array($node->sort, [0, 10]) && $node->mu_only !== -1) {
            $mu_port_migration = $_ENV['mu_port_migration'];
            $muPort = Tools::getMutilUserOutPortArray($node->server);
        } else {
            $mu_port_migration = false;
        }

        $users_raw = User::where('enable', 1)
            ->where('expire_in', '>', date('Y-m-d H:i:s'))
            ->where(static function (Builder $query) use ($node): void {
                $query->whereRaw(
                    'class >= ? AND IF(? = 0, 1, node_group = ?)',
                    [$node->node_class, $node->node_group, $node->node_group]
                )->orWhere('is_admin', 1);
            })
            ->get();

        if (in_array($node->sort, [11, 14])) {
            $key_list = ['node_speedlimit', 'id', 'node_connector', 'uuid', 'alive_ip'];
        } else {
            $key_list = [
                'method', 'obfs', 'obfs_param', 'protocol', 'protocol_param', 'node_speedlimit',
                'is_multi_user', 'id', 'port', 'passwd', 'node_connector', 'alive_ip',
            ];
        }

        $alive_ip = (new \App\Models\Ip())->getUserAliveIpCount();
        $users = [];
        foreach ($users_raw as $user_raw) {
            if (isset($alive_ip[strval($user_raw->id)]) && $user_raw->node_connector !== 0) {
                $user_raw->alive_ip = $alive_ip[strval($user_raw->id)];
            }
            if ($user_raw->transfer_enable <= $user_raw->u + $user_raw->d) {
                if ($_ENV['keep_connect'] === true) {
                    // 流量耗尽用户限速至 1Mbps
                    $user_raw->node_speedlimit = 1;
                } else {
                    continue;
                }
            }
            if ($mu_port_migration === true && $user_raw->is_multi_user !== 0) {
                // 下发偏移后端口
                if ($muPort['type'] === 0) {
                    if (in_array($user_raw->port, array_keys($muPort['port']))) {
                        $user_raw->port = $muPort['port'][$user_raw->port]['backend'];
                    }
                } else {
                    $user_raw->port += $muPort['type'];
                }
            }
            $user_raw = Tools::keyFilter($user_raw, $key_list);
            $users[] = $user_raw;
        }

        $header_etag = $request->getHeaderLine('If-None-Match');

        $body = json_encode([
            'ret' => 1,
            'data' => $users,
        ]);
        $etag = sha1($body);
        if ($header_etag === $etag) {
            return $response->withStatus(304);
        }
        $response->getBody()->write($body);
        return $response->withHeader('ETag', $etag)->withHeader('Content-Type', 'application/json');
    }

    /**
     * POST /mod_mu/users/traffic
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     *
     * @return ResponseInterface
     */
    public function addTraffic($request, $response, $args)
    {
        $data = json_decode($request->getBody()->__toString());
        if (!$data || !is_array($data?->data)) {
            return $response->withJson([
                'ret' => 1,
                'data' => 'ok',
            ]);
        }
        $data = $data->data;

        $node_id = $request->getQueryParam('node_id');
        if (!$node_id) {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);

        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $sum = 0;
        foreach ($data as $log) {
            $u = (int) $log?->u;
            $d = (int) $log?->d;
            $user_id = (int) $log?->user_id;
            if ($user_id) {
                User::where('id', $user_id)->update([
                    't' => time(),
                    'u' => DB::raw("u + ${u}"),
                    'd' => DB::raw("d + ${d}"),
                ]);
            }
            $sum += $u + $d;
        }

        $node->increment('node_bandwidth', $sum);
        NodeOnlineLog::insert([
            'node_id' => $node_id,
            'online_user' => count($data),
            'log_time' => time(),
        ]);

        return $response->withJson([
            'ret' => 1,
            'data' => 'ok',
        ]);
    }

    /**
     * POST /mod_mu/users/aliveip
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     *
     * @return ResponseInterface
     */
    public function addAliveIp($request, $response, $args)
    {
        $data = json_decode($request->getBody()->__toString());
        if (!$data || !is_array($data?->data)) {
            return $response->withJson([
                'ret' => 1,
                'data' => 'ok',
            ]);
        }
        $data = $data->data;

        $node_id = $request->getQueryParam('node_id');
        if (!$node_id) {
            $node_id = Node::where('node_ip', $request->getServerParam('REMOTE_ADDR'))->value('id');
        } elseif (!Node::where('id', $node_id)->exists()) {
            $node_id = null;
        }

        if ($node_id === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        foreach ($data as $log) {
            $ip = (string) $log?->ip;
            $userid = (int) $log?->user_id;

            Ip::insert([
                'userid' => $userid,
                'nodeid' => $node_id,
                'ip' => $ip,
                'datetime' => time(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'data' => 'ok',
        ]);
    }

    /**
     * POST /mod_mu/users/detectlog
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     *
     * @return ResponseInterface
     */
    public function addDetectLog($request, $response, $args)
    {
        $data = json_decode($request->getBody()->__toString());
        if (!$data || !is_array($data?->data)) {
            return $response->withJson([
                'ret' => 1,
                'data' => 'ok',
            ]);
        }
        $data = $data->data;

        $node_id = $request->getQueryParam('node_id');
        if (!$node_id) {
            $node_id = Node::where('node_ip', $request->getServerParam('REMOTE_ADDR'))->value('id');
        } elseif (!Node::where('id', $node_id)->exists()) {
            $node_id = null;
        }

        if ($node_id === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        foreach ($data as $log) {
            $list_id = (int) $log?->list_id;
            $user_id = (int) $log?->user_id;

            DetectLog::insert([
                'user_id' => $user_id,
                'list_id' => $list_id,
                'node_id' => $node_id,
                'datetime' => time(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'data' => 'ok',
        ]);
    }
}
