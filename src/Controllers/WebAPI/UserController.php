<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Models\DetectLog;
use App\Models\HourlyUsage;
use App\Models\Node;
use App\Models\OnlineLog;
use App\Models\User;
use App\Services\DB;
use App\Services\DynamicRate;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Throwable;
use function count;
use function date;
use function is_array;
use function json_decode;
use function time;

final class UserController extends BaseController
{
    /**
     * GET /mod_mu/users
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node_id = $request->getQueryParam('node_id');
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return ResponseHelper::error($response, 'Node not found.');
        }

        if ($node->type === 0) {
            return ResponseHelper::error($response, 'Node is not enabled.');
        }

        $node->update(['node_heartbeat' => time()]);

        if ($node->node_bandwidth_limit !== 0 && $node->node_bandwidth_limit <= $node->node_bandwidth) {
            return ResponseHelper::error($response, 'Node out of bandwidth.');
        }

        $users_raw = (new User())->where(
            'is_banned',
            0
        )->where(
            'class_expire',
            '>',
            date('Y-m-d H:i:s')
        )->where(
            static function ($query) use ($node): void {
                $query->where('class', '>=', $node->node_class)
                    ->where(static function ($query) use ($node): void {
                        if ($node->node_group !== 0) {
                            $query->where('node_group', $node->node_group);
                        }
                    });
            }
        )->orWhere(
            'is_admin',
            1
        )->get([
            'id',
            'u',
            'd',
            'transfer_enable',
            'node_speedlimit',
            'node_iplimit',
            'method',
            'port',
            'passwd',
            'uuid',
        ]);

        $keys_unset = match ($node->sort) {
            15, 14, 11 => ['u', 'd', 'transfer_enable', 'method', 'port', 'passwd'],
            2 => ['u', 'd', 'transfer_enable', 'method', 'port'],
            1 => ['u', 'd', 'transfer_enable', 'method', 'port', 'uuid'],
            default => ['u', 'd', 'transfer_enable', 'uuid', 'node_iplimit']
        };

        $users = [];
        $aliveIpCounts = [];
        if ($users_raw->isNotEmpty()) {
            $aliveIpCounts = (new OnlineLog())
                ->whereIn('user_id', $users_raw->pluck('id'))
                ->where('last_time', '>', time() - 90)
                ->selectRaw('user_id, COUNT(*) AS alive_ip')
                ->groupBy('user_id')
                ->pluck('alive_ip', 'user_id')
                ->all();
        }

        foreach ($users_raw as $user_raw) {
            $aliveIp = (int) ($aliveIpCounts[$user_raw->id] ?? 0);
            $user_raw->alive_ip = $aliveIp;

            if ($user_raw->transfer_enable <= $user_raw->u + $user_raw->d) {
                if ($_ENV['keep_connect']) {
                    // 流量耗尽用户限速至 1Mbps
                    $user_raw->node_speedlimit = 1;
                } else {
                    continue;
                }
            }

            if ($user_raw->node_iplimit !== 0 &&
                $user_raw->node_iplimit <
                $aliveIp
            ) {
                continue;
            }

            if ($node->sort === 1) {
                $method = json_decode($node->custom_config)->method ?? '2022-blake3-aes-128-gcm';
                $user_pk = Tools::genSs2022UserPk($user_raw->passwd, $method);

                if (! $user_pk) {
                    continue;
                }

                $user_raw->passwd = $user_pk;
            }

            foreach ($keys_unset as $key) {
                unset($user_raw->$key);
            }

            $users[] = $user_raw;
        }

        return ResponseHelper::successWithDataEtag($request, $response, $users);
    }

    /**
     * POST /mod_mu/users/traffic
     */
    public function addTraffic(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $payload = json_decode($request->getBody()->__toString());

        if (! $payload || ! is_array($payload->data)) {
            return ResponseHelper::error($response, 'Invalid data.');
        }

        $data = [];
        foreach ($payload->data as $log) {
            $userId = (int) ($log?->user_id ?? 0);
            $upload = (int) ($log?->u ?? -1);
            $download = (int) ($log?->d ?? -1);
            if ($userId <= 0 || $upload < 0 || $download < 0) {
                return ResponseHelper::error($response, 'Invalid traffic item.');
            }
            $data[] = ['user_id' => $userId, 'u' => $upload, 'd' => $download];
        }

        $reportId = isset($payload->report_id) ? (string) $payload->report_id : null;
        if ($reportId !== null && preg_match('/^[a-f0-9]{32}$/D', $reportId) !== 1) {
            return ResponseHelper::error($response, 'Invalid report id.');
        }

        $node_id = (int) $request->getQueryParam('node_id');
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return ResponseHelper::error($response, 'Node not found.');
        }

        if ($node->type === 0) {
            return ResponseHelper::error($response, 'Node is not enabled.');
        }

        try {
            $duplicate = DB::connection()->transaction(static function () use ($data, $node_id, $reportId): bool {
                if ($reportId !== null) {
                    $inserted = DB::table('xrayr_traffic_reports')->insertOrIgnore([
                        'report_id' => $reportId,
                        'node_id' => $node_id,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    if ($inserted === 0) {
                        return true;
                    }
                }

                $lockedNode = (new Node())->where('id', $node_id)->lockForUpdate()->first();
                if ($lockedNode === null || $lockedNode->type === 0) {
                    throw new \RuntimeException('Node is not enabled.');
                }

                if ($lockedNode->is_dynamic_rate) {
                    $dynamic = json_decode($lockedNode->dynamic_rate_config);
                    $rate = DynamicRate::getRateByTime(
                        (float) $dynamic?->max_rate,
                        (int) $dynamic?->max_rate_time,
                        (float) $dynamic?->min_rate,
                        (int) $dynamic?->min_rate_time,
                        (int) date('H'),
                        (int) $lockedNode->dynamic_rate_type === 1 ? 'linear' : 'logistic'
                    );
                } else {
                    $rate = $lockedNode->traffic_rate;
                }

                $sum = 0;
                $onlineUsers = [];
                $trafficLog = Config::obtain('traffic_log');
                foreach ($data as $log) {
                    $user = (new User())->where('id', $log['user_id'])->lockForUpdate()->first();
                    if ($user === null) {
                        continue;
                    }
                    $billedUpload = $log['u'] * $rate;
                    $billedDownload = $log['d'] * $rate;
                    $user->update([
                        'last_use_time' => time(),
                        'u' => $user->u + $billedUpload,
                        'd' => $user->d + $billedDownload,
                        'transfer_total' => $user->transfer_total + $log['u'] + $log['d'],
                        'transfer_today' => $user->transfer_today + $billedUpload + $billedDownload,
                    ]);
                    if ($trafficLog) {
                        (new HourlyUsage())->add($log['user_id'], $log['u'] + $log['d']);
                    }
                    $sum += $log['u'] + $log['d'];
                    $onlineUsers[$log['user_id']] = true;
                }

                $lockedNode->update([
                    'node_bandwidth' => $lockedNode->node_bandwidth + $sum,
                    'online_user' => count($onlineUsers),
                ]);

                return false;
            });
        } catch (Throwable) {
            return ResponseHelper::error($response, 'Traffic update failed.');
        }

        return ResponseHelper::success($response, $duplicate ? 'duplicate' : 'ok');
    }

    /**
     * POST /mod_mu/users/aliveip
     */
    public function addAliveIp(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $data = json_decode($request->getBody()->__toString());

        if (! $data || ! is_array($data->data)) {
            return ResponseHelper::error($response, 'Invalid data.');
        }

        $data = $data->data;
        $node_id = $request->getQueryParam('node_id');
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return ResponseHelper::error($response, 'Node not found.');
        }

        if ($node->type === 0) {
            return ResponseHelper::error($response, 'Node is not enabled.');
        }

        foreach ($data as $log) {
            $ip = (string) $log?->ip;
            $user_id = (int) $log?->user_id;

            if (Tools::isIPv4($ip)) {
                // convert IPv4 Address to IPv4-mapped IPv6 Address
                $ip = '::ffff:' . $ip;
            } elseif (! Tools::isIPv6($ip)) {
                // either IPv4 or IPv6 Address
                continue;
            }

            (new OnlineLog())->upsert(
                [
                    'user_id' => $user_id,
                    'ip' => $ip,
                    'node_id' => $node_id,
                    'first_time' => time(),
                    'last_time' => time(),
                ],
                ['user_id', 'ip'],
                ['node_id', 'last_time']
            );
        }

        return ResponseHelper::success($response, 'ok');
    }

    /**
     * POST /mod_mu/users/detectlog
     */
    public function addDetectLog(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $data = json_decode($request->getBody()->__toString());

        if (! $data || ! is_array($data->data)) {
            return ResponseHelper::error($response, 'Invalid data.');
        }

        $data = $data->data;
        $node_id = $request->getQueryParam('node_id');
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return ResponseHelper::error($response, 'Node not found.');
        }

        if ($node->type === 0) {
            return ResponseHelper::error($response, 'Node is not enabled.');
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

        return ResponseHelper::success($response, 'ok');
    }
}
