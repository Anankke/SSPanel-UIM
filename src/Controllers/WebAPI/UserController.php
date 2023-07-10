<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Models\Node;
use App\Services\DB;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function count;
use function filter_var;
use function is_array;
use function json_decode;
use function time;
use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_IP;

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

        $users_raw = DB::select('
            SELECT
                user.id,
                user.u,
                user.d,
                user.transfer_enable,
                user.node_speedlimit,
                user.node_iplimit,
                user.method,
                user.port,
                user.passwd,
                user.uuid,
                IF(online_log.count IS NULL, 0, online_log.count) AS alive_ip
            FROM
                user LEFT JOIN (
                    SELECT
                        user_id, COUNT(*) AS count
                    FROM
                        online_log
                    WHERE
                        last_time > UNIX_TIMESTAMP() - 90
                    GROUP BY
                        user_id
                ) AS online_log ON online_log.user_id = user.id
            WHERE
                user.is_banned = 0
                AND user.expire_in > CURRENT_TIMESTAMP()
                AND user.class_expire > CURRENT_TIMESTAMP()
                AND (
                    (
                        user.class >= ?
                        AND IF(? = 0, 1, user.node_group = ?)
                    ) OR user.is_admin = 1
                )
        ', [$node->node_class, $node->node_group, $node->node_group]);

        $keys_unset = match ((int) $node->sort) {
            14, 11 => ['u', 'd', 'transfer_enable', 'method', 'port', 'passwd'],
            default => ['u', 'd', 'transfer_enable', 'uuid']
        };

        $users = [];

        foreach ($users_raw as $user_raw) {
            if ($user_raw->transfer_enable <= $user_raw->u + $user_raw->d) {
                if ($_ENV['keep_connect']) {
                    // 流量耗尽用户限速至 1Mbps
                    $user_raw->node_speedlimit = 1;
                } else {
                    continue;
                }
            }

            $user_raw->node_connector = 0;

            foreach ($keys_unset as $key) {
                unset($user_raw->$key);
            }

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
        $stat = $pdo->prepare('
                UPDATE user SET last_use_time = UNIX_TIMESTAMP(),
                u = u + ?,
                d = d + ?,
                transfer_total = transfer_total + ?,
                transfer_today = transfer_today + ? WHERE id = ?
        ');
        $rate = (float) $node->traffic_rate;
        $sum = 0;

        foreach ($data as $log) {
            $u = $log?->u;
            $d = $log?->d;
            $user_id = $log?->user_id;
            if ($user_id) {
                $stat->execute([(int) ($u * $rate), (int) ($d * $rate), (int) ($u + $d), (int) ($u + $d), $user_id]);
            }
            $sum += $u + $d;
        }

        $node->increment('node_bandwidth', $sum);
        $node->online_user = count($data) - 1;
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

        $stat = DB::getPdo()->prepare('
            INSERT INTO online_log (user_id, ip, node_id, first_time, last_time)
                VALUES (?, ?, ?, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
                ON DUPLICATE KEY UPDATE node_id = ?, last_time = UNIX_TIMESTAMP()
        ');

        foreach ($data as $log) {
            $ip = (string) $log?->ip;
            $user_id = (int) $log?->user_id;

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                // convert IPv4 Address to IPv4-mapped IPv6 Address
                $ip = "::ffff:{$ip}";
            } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
                // either IPv4 or IPv6 Address
                continue;
            }

            $stat->execute([$user_id, $ip, $node_id, $node_id]);
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
