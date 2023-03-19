<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Models\Ip;
use App\Models\Node;
use App\Models\User;
use App\Services\DB;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function count;
use function in_array;
use function is_array;
use function json_decode;
use function strval;
use function time;

final class UserController extends BaseController
{
    /**
     * GET /mod_mu/users
     *
     * @param ServerRequest   $request
     * @param Response  $response
     * @param array     $args
     *
     * @return ResponseInterface
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node_id = $request->getQueryParam('node_id');
        $node = Node::find($node_id);
        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $node->update(['node_heartbeat' => time()]);

        if (($node->node_bandwidth_limit !== 0) && $node->node_bandwidth_limit < $node->node_bandwidth) {
            return $response->withJson([
                'ret' => 1,
                'data' => [],
            ]);
        }

        $users_raw = User::where('is_banned', 0)
            ->where('expire_in', '>', date('Y-m-d H:i:s'))
            ->where(static function (Builder $query) use ($node): void {
                $query->whereRaw(
                    'class >= ? AND IF(? = 0, 1, node_group = ?)',
                    [$node->node_class, $node->node_group, $node->node_group]
                )->orWhere('is_admin', 1);
            })
            ->get();

        if (in_array($node->sort, [11, 14])) {
            $key_list = [
                'id', 'node_connector', 'node_speedlimit', 'node_iplimit', 'uuid', 'alive_ip',
            ];
        } else {
            $key_list = [
                'id', 'node_connector', 'node_speedlimit', 'node_iplimit', 'method', 'port', 'passwd', 'alive_ip',
            ];
        }

        $alive_ip = (new Ip())->getUserAliveIpCount();
        $users = [];
        foreach ($users_raw as $user_raw) {
            if (isset($alive_ip[strval($user_raw->id)]) && $user_raw->node_connector !== 0 && $user_raw->node_iplimit !== 0) {
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

            $user_raw = Tools::keyFilter($user_raw, $key_list);
            $users[] = $user_raw;
        }

        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => $users,
        ]);
    }

    /**
     * POST /mod_mu/users/traffic
     *
     * @param ServerRequest   $request
     * @param Response  $response
     * @param array     $args
     *
     * @return ResponseInterface
     */
    public function addTraffic(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $data = json_decode($request->getBody()->__toString());
        if (! $data || ! is_array($data->data)) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }
        $data = $data->data;

        $node_id = $request->getQueryParam('node_id');
        $node = Node::find($node_id);

        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $pdo = DB::getPdo();
        $stat = $pdo->prepare('UPDATE user SET t = UNIX_TIMESTAMP(), u = u + ?, d = d + ?, transfer_total = transfer_total + ? WHERE id = ?');

        $rate = (float) $node->traffic_rate;
        $sum = 0;
        foreach ($data as $log) {
            $u = $log?->u;
            $d = $log?->d;
            $user_id = $log?->user_id;
            if ($user_id) {
                $stat->execute([(int) ($u * $rate), (int) ($d * $rate), (int) ($u + $d), $user_id]);
            }
            $sum += $u + $d;
        }

        $node->increment('node_bandwidth', $sum);

        $node->online_user = count($data);
        $node->save();

        return $response->withJson([
            'ret' => 1,
            'data' => 'ok',
        ]);
    }

    /**
     * POST /mod_mu/users/aliveip
     *
     * @param ServerRequest   $request
     * @param Response  $response
     * @param array     $args
     *
     * @return ResponseInterface
     */
    public function addAliveIp(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $data = json_decode($request->getBody()->__toString());
        if (! $data || ! is_array($data->data)) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }
        $data = $data->data;

        $node_id = $request->getQueryParam('node_id');

        if ($node_id === null || ! Node::where('id', $node_id)->exists()) {
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
     * @param ServerRequest   $request
     * @param Response  $response
     * @param array     $args
     *
     * @return ResponseInterface
     */
    public function addDetectLog(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $data = json_decode($request->getBody()->__toString());
        if (! $data || ! is_array($data->data)) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }
        $data = $data->data;

        $node_id = $request->getQueryParam('node_id');

        if ($node_id === null || ! Node::where('id', $node_id)->exists()) {
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
