<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Link;
use App\Models\Node;
use App\Models\Setting;
use App\Models\UserSubscribeLog;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function array_key_exists;
use function base64_encode;
use function json_decode;
use function json_encode;

/**
 *  LinkController
 */
final class LinkController extends BaseController
{
    public static function getContent(ServerRequest $request, Response $response, array $args)
    {
        if (! $_ENV['Subscribe']) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }
        //判断是否开启传统订阅
        if (! Setting::obtain('enable_traditional_sub')) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $token = $args['token'];
        $params = $request->getQueryParams();

        $Elink = Link::where('token', $token)->first();
        if ($Elink === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $user = $Elink->getUser();
        if ($user === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        if ((int) $user->is_banned === 1) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $sub_type = '';
        $sub_info = [];

        if (isset($params['clash']) && $params['clash'] === '1') {
            $sub_type = 'clash';
            $sub_info = SubController::getClash($user);
        }

        if (isset($params['sip002']) && $params['sip002'] === '1') {
            $sub_type = 'sip002';
            $sub_info = self::getSIP002($user);
        }

        if (isset($params['ss']) && $params['ss'] === '1') {
            $sub_type = 'ss';
            $sub_info = self::getSS($user);
        }

        if (isset($params['v2ray']) && $params['v2ray'] === '1') {
            $sub_type = 'v2ray';
            $sub_info = self::getV2Ray($user);
        }

        if (isset($params['trojan']) && $params['trojan'] === '1') {
            $sub_type = 'trojan';
            $sub_info = self::getTrojan($user);
        }

        if (isset($params['sub'])) {
            switch ($params['sub']) {
                case '3':
                    $sub_type = 'v2ray';
                    $sub_info = self::getV2Ray($user);
                    break;
                case '4':
                    $sub_type = 'trojan';
                    $sub_info = self::getTrojan($user);
                    break;
                default:
                    $sub_type = 'ss';
                    $sub_info = self::getSS($user);
                    break;
            }
        }

        // 记录订阅日志
        if ($_ENV['subscribeLog'] === true) {
            UserSubscribeLog::addSubscribeLog($user, $sub_type, $request->getHeaderLine('User-Agent'));
        }

        $sub_details = ' upload=' . $user->u
            . '; download=' . $user->d
            . '; total=' . $user->transfer_enable
            . '; expire=' . strtotime($user->class_expire);

        return $response->withHeader('Subscription-Userinfo', $sub_details)->write(
            $sub_info
        );
    }

    // 传统 SS 订阅
    public static function getSS($user): string
    {
        $links = '';
        //判断是否开启SS订阅
        if (! Setting::obtain('enable_ss_sub')) {
            return $links;
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
            if ((int) $node_raw->sort === 0) {
                $links .= base64_encode($user->method . ':' . $user->passwd . '@' . $server . ':' . $user->port) . '#' .
                    $node_raw->name . PHP_EOL;
            }
        }

        return $links;
    }

    // SIP002 SS 订阅
    public static function getSIP002($user): string
    {
        $links = '';
        //判断是否开启SS订阅
        if (! Setting::obtain('enable_ss_sub')) {
            return $links;
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
            if ((int) $node_raw->sort === 0) {
                $plugin = $node_custom_config['plugin'] ?? '';
                $plugin_option = $node_custom_config['plugin_option'] ?? '';

                $links .= $user->method . ':' . $user->passwd . '@' . $server . ':' .
                    $user->port . '/?plugin=' . $plugin . '&' . $plugin_option . '#' .
                    $node_raw->name . PHP_EOL;
            }
        }

        return $links;
    }

    public static function getV2Ray($user): string
    {
        $links = '';
        //判断是否开启V2Ray订阅
        if (! Setting::obtain('enable_v2_sub')) {
            return $links;
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
            if ((int) $node_raw->sort === 11) {
                $v2_port = $node_custom_config['v2_port'] ?? ($node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443));
                //默認值有問題的請懂 V2 怎麽用的人來改一改。
                $alter_id = $node_custom_config['alter_id'] ?? '0';
                $security = $node_custom_config['security'] ?? 'none';
                $network = $node_custom_config['network'] ?? '';
                $header = $node_custom_config['header'] ?? ['type' => 'none'];
                $header_type = $header['type'] ?? '';
                $host = $node_custom_config['host'] ?? '';
                $path = $node_custom_config['path'] ?? '/';

                $v2rayn_array = [
                    'v' => '2',
                    'ps' => $node_raw->name,
                    'add' => $server,
                    'port' => $v2_port,
                    'id' => $user->uuid,
                    'aid' => $alter_id,
                    'net' => $network,
                    'type' => $header_type,
                    'host' => $host,
                    'path' => $path,
                    'tls' => $security,
                ];

                $links .= 'vmess://' . base64_encode(json_encode($v2rayn_array)) . PHP_EOL;
            }
        }

        return $links;
    }

    public static function getTrojan($user): string
    {
        $links = '';
        //判断是否开启Trojan订阅
        if (! Setting::obtain('enable_trojan_sub')) {
            return $links;
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
            if ((int) $node_raw->sort === 14) {
                $trojan_port = $node_custom_config['trojan_port'] ?? ($node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443));
                $host = $node_custom_config['host'] ?? '';
                $allow_insecure = $node_custom_config['allow_insecure'] ?? '0';
                $security = $node_custom_config['security'] ?? array_key_exists('enable_xtls', $node_custom_config) && $node_custom_config['enable_xtls'] === '1' ? 'xtls' : 'tls';
                $mux = $node_custom_config['mux'] ?? '';
                $transport = $node_custom_config['transport'] ?? array_key_exists('grpc', $node_custom_config) && $node_custom_config['grpc'] === '1' ? 'grpc' : 'tcp';

                $transport_plugin = $node_custom_config['transport_plugin'] ?? '';
                $transport_method = $node_custom_config['transport_method'] ?? '';
                $servicename = $node_custom_config['servicename'] ?? '';
                $path = $node_custom_config['path'] ?? '';

                $links .= 'trojan://' . $user->uuid . '@' . $server . ':' . $trojan_port . '?peer=' . $host . '&sni=' . $host .
                    '&obfs=' . $transport_plugin . '&path=' . $path . '&mux=' . $mux . '&allowInsecure=' . $allow_insecure .
                    '&obfsParam=' . $transport_method . '&type=' . $transport . '&security=' . $security . '&serviceName=' . $servicename . '#' .
                    $node_raw->name . PHP_EOL;
            }
        }

        return $links;
    }

    public static function getTraditionalSub($user): string
    {
        $userid = $user->id;
        $token = Link::where('userid', $userid)->first();
        return $_ENV['subUrl'] . '/link/' . $token->token;
    }
}
