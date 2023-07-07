<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Link;
use App\Models\Node;
use App\Models\Setting;
use App\Models\UserSubscribeLog;
use App\Services\RateLimit;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use RedisException;
use Symfony\Component\Yaml\Yaml;
use voku\helper\AntiXSS;
use function array_key_exists;
use function array_merge;
use function in_array;
use function json_decode;

/**
 *  SubController
 */
final class SubController extends BaseController
{
    /**
     * @throws RedisException
     */
    public static function getContent($request, $response, $args): ResponseInterface
    {
        $err_msg = '订阅链接无效';

        $subtype = $args['subtype'];
        $subtype_list = ['json', 'clash', 'sip008'];

        if (! $_ENV['Subscribe'] ||
            ! in_array($subtype, $subtype_list) ||
            'https://' . $request->getHeaderLine('Host') !== $_ENV['subUrl']
        ) {
            return ResponseHelper::error($response, $err_msg);
        }

        $antiXss = new AntiXSS();
        $token = $antiXss->xss_clean($args['token']);

        if ($_ENV['enable_rate_limit'] &&
            (! RateLimit::checkIPLimit($request->getServerParam('REMOTE_ADDR')) ||
            ! RateLimit::checkSubLimit($token))
        ) {
            return ResponseHelper::error($response, $err_msg);
        }

        $link = Link::where('token', $token)->first();

        if ($link === null || ! $link->isValid()) {
            return ResponseHelper::error($response, $err_msg);
        }

        $user = $link->user();

        match ($subtype) {
            'json' => $sub_info = self::getJson($user),
            'clash' => $sub_info = self::getClash($user),
            'sip008' => $sub_info = self::getSIP008($user),
        };

        if ($_ENV['subscribeLog']) {
            UserSubscribeLog::addSubscribeLog($user, $subtype, $request->getHeaderLine('User-Agent'));
        }

        if (in_array($subtype, ['json', 'sip008'])) {
            return $response->withJson([
                $sub_info,
            ]);
        }
        $sub_details = ' upload=' . $user->u
        . '; download=' . $user->d
        . '; total=' . $user->transfer_enable
        . '; expire=' . strtotime($user->class_expire);
        return $response->withHeader('Subscription-Userinfo', $sub_details)->write(
            $sub_info
        );
    }

    public static function getJson($user): array
    {
        $nodes = [];
        //篩選出用戶能連接的節點
        $nodes_raw = Node::where('type', 1)
            ->where('node_class', '<=', $user->class)
            ->whereIn('node_group', [0, $user->node_group])
            ->where(static function ($query): void {
                $query->where('node_bandwidth_limit', '=', 0)->orWhereRaw('node_bandwidth < node_bandwidth_limit');
            })
            ->get();

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);
            //檢查是否配置“前端/订阅中下发的服务器地址”
            if (! array_key_exists('server_user', $node_custom_config)) {
                $server = $node_raw->server;
            } else {
                $server = $node_custom_config['server_user'];
            }

            switch ((int) $node_raw->sort) {
                case 0:
                    $plugin = $node_custom_config['plugin'] ?? '';
                    $plugin_option = $node_custom_config['plugin_option'] ?? '';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'ss',
                        'address' => $server,
                        'port' => (int) $user->port,
                        'password' => $user->passwd,
                        'encryption' => $user->method,
                        'plugin' => $plugin,
                        'plugin_option' => $plugin_option,
                        'remark' => $node_raw->info,
                    ];
                    break;
                case 11:
                    $v2_port = $node_custom_config['v2_port'] ?? ($node_custom_config['offset_port_user']
                        ?? ($node_custom_config['offset_port_node'] ?? 443));
                    //默認值有問題的請懂 V2 怎麽用的人來改一改。
                    $alter_id = $node_custom_config['alter_id'] ?? '0';
                    $security = $node_custom_config['security'] ?? 'none';
                    $flow = $node_custom_config['flow'] ?? '';
                    $encryption = $node_custom_config['encryption'] ?? '';
                    $network = $node_custom_config['network'] ?? '';
                    $header = $node_custom_config['header'] ?? ['type' => 'none'];
                    $header_type = $header['type'] ?? '';
                    $host = $node_custom_config['header']['request']['headers']['Host'][0] ?? $node_custom_config['host'] ?? '';
                    $servicename = $node_custom_config['servicename'] ?? '';
                    $path = $node_custom_config['header']['request']['path'][0] ?? $node_custom_config['path'] ?? '/';
                    $tls = in_array($security, ['tls', 'xtls']) ? '1' : '0';
                    $enable_vless = $node_custom_config['enable_vless'] ?? '0';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'vmess',
                        'address' => $server,
                        'port' => (int) $v2_port,
                        'uuid' => $user->uuid,
                        'alterid' => (int) $alter_id,
                        'security' => $security,
                        'flow' => $flow,
                        'encryption' => $encryption,
                        'network' => $network,
                        'header' => $header,
                        'header_type' => $header_type,
                        'host' => $host,
                        'path' => $path,
                        'servicename' => $servicename,
                        'tls' => (int) $tls,
                        'enable_vless' => (int) $enable_vless,
                        'remark' => $node_raw->info,
                    ];
                    break;
                case 14:
                    $trojan_port = $node_custom_config['trojan_port'] ?? ($node_custom_config['offset_port_user']
                        ?? ($node_custom_config['offset_port_node'] ?? 443));
                    $host = $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? '0';
                    $security = $node_custom_config['security']
                        ?? array_key_exists('enable_xtls', $node_custom_config)
                        && $node_custom_config['enable_xtls'] === '1' ? 'xtls' : 'tls';
                    $mux = $node_custom_config['mux'] ?? '';
                    $transport = $node_custom_config['transport']
                        ?? array_key_exists('grpc', $node_custom_config)
                        && $node_custom_config['grpc'] === '1' ? 'grpc' : 'tcp';

                    $transport_plugin = $node_custom_config['transport_plugin'] ?? '';
                    $transport_method = $node_custom_config['transport_method'] ?? '';
                    $servicename = $node_custom_config['servicename'] ?? '';
                    $path = $node_custom_config['path'] ?? '';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'trojan',
                        'address' => $server,
                        'host' => $host,
                        'port' => (int) $trojan_port,
                        'uuid' => $user->uuid,
                        'security' => $security,
                        'mux' => $mux,
                        'transport' => $transport,
                        'transport_plugin' => $transport_plugin,
                        'transport_method' => $transport_method,
                        'allow_insecure' => (int) $allow_insecure,
                        'servicename' => $servicename,
                        'path' => $path,
                        'remark' => $node_raw->info,
                    ];
                    break;
                default:
                    $node = [];
                    break;
            }

            if ($node === []) {
                continue;
            }

            $nodes[] = $node;
        }

        return [
            'version' => 2,
            'sub_name' => $_ENV['appName'],
            'user_email' => $user->email,
            'user_name' => $user->user_name,
            'user_class' => $user->class,
            'user_class_expire_date' => $user->class_expire,
            'user_total_traffic' => $user->transfer_enable,
            'user_used_traffic' => $user->u + $user->d,
            'nodes' => $nodes,
        ];
    }

    public static function getClash($user): string
    {
        $nodes = [];
        $clash_config = $_ENV['Clash_Config'];
        $clash_group_indexes = $_ENV['Clash_Group_Indexes'];
        $clash_group_config = $_ENV['Clash_Group_Config'];

        //篩選出用戶能連接的節點
        $nodes_raw = Node::where('type', 1)
            ->where('node_class', '<=', $user->class)
            ->whereIn('node_group', [0, $user->node_group])
            ->where(static function ($query): void {
                $query->where('node_bandwidth_limit', '=', 0)->orWhereRaw('node_bandwidth < node_bandwidth_limit');
            })
            ->get();

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);
            //檢查是否配置“前端/订阅中下发的服务器地址”
            if (! array_key_exists('server_user', $node_custom_config)) {
                $server = $node_raw->server;
            } else {
                $server = $node_custom_config['server_user'];
            }

            switch ((int) $node_raw->sort) {
                case 0:
                    $plugin = $node_custom_config['plugin'] ?? '';
                    $plugin_option = $node_custom_config['plugin_option'] ?? null;
                    // Clash 特定配置
                    $udp = $node_custom_config['udp'] ?? true;
                    // Clash.Meta
                    $client_fingerprint = $node_custom_config['client_fingerprint'] ?? '';

                    $node = [
                        'name' => $node_raw->name,
                        'type' => 'ss',
                        'server' => $server,
                        'port' => (int) $user->port,
                        'password' => $user->passwd,
                        'cipher' => $user->method,
                        'udp' => $udp,
                        'client-fingerprint' => $client_fingerprint,
                        'plugin' => $plugin,
                        'plugin-opts' => $plugin_option,
                    ];

                    break;
                case 11:
                    $v2_port = $node_custom_config['v2_port'] ?? ($node_custom_config['offset_port_user']
                        ?? ($node_custom_config['offset_port_node'] ?? 443));
                    $alter_id = $node_custom_config['alter_id'] ?? '0';
                    $security = $node_custom_config['security'] ?? 'none';
                    $encryption = $node_custom_config['encryption'] ?? 'auto';
                    $network = $node_custom_config['header']['type'] ?? $node_custom_config['network'] ?? '';
                    $host = $node_custom_config['header']['request']['headers']['Host'][0] ?? $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? false;
                    $tls = in_array($security, ['tls', 'xtls']);
                    // Clash 特定配置
                    $udp = $node_custom_config['udp'] ?? true;
                    $ws_opts = $node_custom_config['ws-opts'] ?? $node_custom_config['ws_opts'] ?? null;
                    $h2_opts = $node_custom_config['h2-opts'] ?? $node_custom_config['h2_opts'] ?? null;
                    $http_opts = $node_custom_config['http-opts'] ?? $node_custom_config['http_opts'] ?? null;
                    $grpc_opts = $node_custom_config['grpc-opts'] ?? $node_custom_config['grpc_opts'] ?? null;
                    // Clash.Meta
                    $type = ($node_custom_config['enable_vless'] ?? '0') === '1' ? 'vless' : 'vmess';
                    $client_fingerprint = $node_custom_config['client_fingerprint'] ?? '';
                    $fingerprint = $node_custom_config['fingerprint'] ?? '';
                    $flow = $node_custom_config['flow'] ?? '';
                    $reality_opts = $node_custom_config['reality-opts'] ?? $node_custom_config['reality_opts'] ?? null;

                    $node = [
                        'name' => $node_raw->name,
                        'type' => $type,
                        'server' => $server,
                        'port' => (int) $v2_port,
                        'uuid' => $user->uuid,
                        'alterId' => (int) $alter_id,
                        'cipher' => $encryption,
                        'udp' => $udp,
                        'tls' => $tls,
                        'client-fingerprint' => $client_fingerprint,
                        'fingerprint' => $fingerprint,
                        'flow' => $flow,
                        'skip-cert-verify' => $allow_insecure,
                        'servername' => $host,
                        'network' => $network,
                        'ws-opts' => $ws_opts,
                        'h2-opts' => $h2_opts,
                        'http-opts' => $http_opts,
                        'grpc-opts' => $grpc_opts,
                        'reality-opts' => $reality_opts,
                    ];

                    break;
                case 14:
                    $trojan_port = $node_custom_config['trojan_port'] ?? ($node_custom_config['offset_port_user']
                        ?? ($node_custom_config['offset_port_node'] ?? 443));
                    $network = $node_custom_config['network']
                        ?? (array_key_exists('grpc', $node_custom_config)
                            && $node_custom_config['grpc'] === '1' ? 'grpc' : 'tcp');
                    $host = $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? false;
                    // Clash 特定配置
                    $udp = $node_custom_config['udp'] ?? true;
                    $ws_opts = $node_custom_config['ws-opts'] ?? $node_custom_config['ws_opts'] ?? null;
                    $grpc_opts = $node_custom_config['grpc-opts'] ?? $node_custom_config['grpc_opts'] ?? null;
                    // Clash.Meta
                    $client_fingerprint = $node_custom_config['client_fingerprint'] ?? '';
                    $fingerprint = $node_custom_config['fingerprint'] ?? '';
                    $flow = $node_custom_config['flow'] ?? '';
                    $flow_show = $node_custom_config['flow_show'] ?? false;

                    $node = [
                        'name' => $node_raw->name,
                        'type' => 'trojan',
                        'server' => $server,
                        'sni' => $host,
                        'port' => (int) $trojan_port,
                        'password' => $user->uuid,
                        'network' => $network,
                        'udp' => $udp,
                        'client-fingerprint' => $client_fingerprint,
                        'fingerprint' => $fingerprint,
                        'flow' => $flow,
                        'flow-show' => $flow_show,
                        'skip-cert-verify' => $allow_insecure,
                        'ws-opts' => $ws_opts,
                        'grpc-opts' => $grpc_opts,
                    ];

                    break;
                default:
                    $node = [];
                    break;
            }

            if ($node === []) {
                continue;
            }

            $nodes[] = $node;

            foreach ($clash_group_indexes as $index) {
                $clash_group_config['proxy-groups'][$index]['proxies'][] = $node_raw->name;
            }
        }

        $clash_nodes = [
            'proxies' => $nodes,
        ];

        return Yaml::dump(
            array_merge($clash_config, $clash_nodes, $clash_group_config),
            4,
            1,
            Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE
        );
    }

    // SIP008 SS 订阅
    public static function getSIP008($user): array
    {
        $nodes = [];
        //判断是否开启SS订阅
        if (! Setting::obtain('enable_ss_sub')) {
            return $nodes;
        }
        //篩選出用戶能連接的節點
        $nodes_raw = Node::where('type', 1)
            ->where('node_class', '<=', $user->class)
            ->whereIn('node_group', [0, $user->node_group])
            ->where(static function ($query): void {
                $query->where('node_bandwidth_limit', '=', 0)->orWhereRaw('node_bandwidth < node_bandwidth_limit');
            })
            ->get();

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);
            //檢查是否配置“前端/订阅中下发的服务器地址”
            if (! array_key_exists('server_user', $node_custom_config)) {
                $server = $node_raw->server;
            } else {
                $server = $node_custom_config['server_user'];
            }

            switch ((int) $node_raw->sort) {
                case 0:
                    $plugin = $node_custom_config['plugin'] ?? '';
                    $plugin_option = $node_custom_config['plugin_option'] ?? '';
                    $node = [
                        'id' => $node_raw->id,
                        'remarks' => $node_raw->name,
                        'server' => $server,
                        'server_port' => (int) $user->port,
                        'password' => $user->passwd,
                        'method' => $user->method,
                        'plugin' => $plugin,
                        'plugin_opts' => $plugin_option,
                    ];
                    break;
                default:
                    $node = [];
                    break;
            }

            if ($node === []) {
                continue;
            }

            $nodes[] = $node;
        }

        return [
            'version' => 1,
            'servers' => $nodes,
            'bytes_used' => $user->u + $user->d,
            'bytes_remaining' => $user->transfer_enable - $user->u - $user->d,
        ];
    }

    public static function getUniversalSub($user): string
    {
        $userid = $user->id;
        $token = Link::where('userid', $userid)->first();

        if ($token === null) {
            $token = new Link();
            $token->userid = $userid;
            $token->token = Tools::genSubToken();
            $token->save();
        }

        return $_ENV['subUrl'] . '/sub/' . $token->token;
    }
}
