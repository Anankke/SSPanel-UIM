<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Models\Node;
use App\Services\DB;
use App\Services\DynamicRate;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function count;
use function is_array;
use function json_decode;
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
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node not found.',
            ]);
        }

        if ($node->type === 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node is not enabled.',
            ]);
        }

        $node->update(['node_heartbeat' => time()]);

        if ($node->node_bandwidth_limit !== 0 && $node->node_bandwidth_limit <= $node->node_bandwidth) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node out of bandwidth.',
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
                AND user.class_expire > CURRENT_TIMESTAMP()
                AND (
                    (
                        user.class >= ?
                        AND IF(? = 0, 1, user.node_group = ?)
                    ) OR user.is_admin = 1
                )
        ', [$node->node_class, $node->node_group, $node->node_group]);

        $keys_unset = match ($node->sort) {
            14, 11 => ['u', 'd', 'transfer_enable', 'method', 'port', 'passwd'],
            2 => ['u', 'd', 'transfer_enable', 'method', 'port'],
            1 => ['u', 'd', 'transfer_enable', 'method', 'port', 'uuid'],
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

            if ($node->sort === 1) {
                $method = json_decode($node->custom_config)->method ?? '2022-blake3-aes-128-gcm';

                $pk_len = match ($method) {
                    '2022-blake3-aes-128-gcm' => 16,
                    default => 32,
                };

                $user_raw->passwd = Tools::genSs2022UserPk($user_raw->passwd, $pk_len);
            }

            foreach ($keys_unset as $key) {
                unset($user_raw->$key);
            }

            $users[] = $user_raw;
        }

        return ResponseHelper::successWithDataEtag($request, $response, [
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
                'msg' => 'Invalid data.',
            ]);
        }

        $data = $data->data;
        $node_id = $request->getQueryParam('node_id');
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node not found.',
            ]);
        }

        if ($node->type === 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node is not enabled.',
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

        if ($node->is_dynamic_rate) {
            $dynamic_rate_config = json_decode($node->dynamic_rate_config);

            $dynamic_rate_type = match ($node->dynamic_rate_type) {
                1 => 'linear',
                default => 'logistic',
            };

            $rate = DynamicRate::getRateByTime(
                (float) $dynamic_rate_config?->max_rate,
                (int) $dynamic_rate_config?->max_rate_time,
                (float) $dynamic_rate_config?->min_rate,
                (int) $dynamic_rate_config?->min_rate_time,
                (int) date('H'),
                $dynamic_rate_type
            );
        } else {
            $rate = $node->traffic_rate;
        }

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
            'msg' => 'ok',
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
                'msg' => 'Invalid data.',
            ]);
        }

        $data = $data->data;
        $node_id = $request->getQueryParam('node_id');
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node not found.',
            ]);
        }

        if ($node->type === 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node is not enabled.',
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

            if (Tools::isIPv4($ip)) {
                // convert IPv4 Address to IPv4-mapped IPv6 Address
                $ip = "::ffff:{$ip}";
            } elseif (! Tools::isIPv6($ip)) {
                // either IPv4 or IPv6 Address
                continue;
            }

            $stat->execute([$user_id, $ip, $node_id, $node_id]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => 'ok',
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
                'msg' => 'Invalid data.',
            ]);
        }

        $data = $data->data;
        $node_id = $request->getQueryParam('node_id');
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node not found.',
            ]);
        }

        if ($node->type === 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node is not enabled.',
            ]);
        }

        foreach ($data as $log) {
            $list_id = (int) $log?->list_id;
            $user_id = (int) $log?->user_id;

            (new DetectLog())->insert([
                'user_id' => $user_id,
                'list_id' => $list_id,
                'node_id' => $node_id,
                'datetime' => time(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => 'ok',
        ]);
    }
}
