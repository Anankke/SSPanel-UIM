<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Link;
use App\Models\Node;
use App\Models\UserSubscribeLog;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;

/**
 *  SubController
 */
final class SubController extends BaseController
{
    public static function getContent($request, $response, $args): ResponseInterface
    {
        if (! $_ENV['Subscribe']) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $token = $args['token'];
        $subtype = $args['subtype'];

        $sub_token = Link::where('token', $token)->first();
        if ($sub_token === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $user = $sub_token->getUser();
        if ($user === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $subtype_list = ['json', 'clash'];
        if (! \in_array($subtype, $subtype_list)) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $nodes = [];
        //篩選出用戶能連接的節點，感謝 @AVX512
        $nodes_raw = Node::where('type', 1)
            ->where('node_class', '<=', $user->class)
            ->whereIn('node_group', [0, $user->group])
            ->where(static function ($query): void {
                $query->where('node_bandwidth_limit', '=', 0)->orWhereRaw('node_bandwidth < node_bandwidth_limit');
            })
            ->get();

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = \json_decode($node_raw->custom_config, true);
            //檢查是否配置“前端/订阅中下发的服务器地址”
            if (! array_key_exists('server_user', $node_custom_config)) {
                $server = $node_raw->server;
            } else {
                $server = $node_custom_config['server_user'];
            }
            switch ($node_raw->sort) {
                case '0':
                    $plugin = $node_custom_config['plugin'] ?? '';
                    $plugin_option = $node_custom_config['plugin_option'] ?? '';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'ss',
                        'address' => $server,
                        'port' => $user->port,
                        'password' => $user->passwd,
                        'encryption' => $user->method,
                        'plugin' => $plugin,
                        'plugin_option' => $plugin_option,
                        'remark' => $node_raw->info,
                    ];
                    break;
                case '11':
                    $v2_port = $node_custom_config['v2_port'] ?? ($node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443));
                    //默認值有問題的請懂 V2 怎麽用的人來改一改。
                    $alter_id = $node_custom_config['alter_id'] ?? '0';
                    $security = $node_custom_config['security'] ?? 'none';
                    $flow = $node_custom_config['flow'] ?? '';
                    $encryption = $node_custom_config['encryption'] ?? '';
                    $network = $node_custom_config['network'] ?? '';
                    $header = $node_custom_config['header'] ?? ['type' => 'none'];
                    $header_type = $header['type'] ?? '';
                    $host = $node_custom_config['host'] ?? '';
                    $servicename = $node_custom_config['servicename'] ?? '';
                    $path = $node_custom_config['path'] ?? '/';
                    $tls = \in_array($security, ['tls', 'xtls']) ? '1' : '0';
                    $enable_vless = $node_custom_config['enable_vless'] ?? '0';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'vmess',
                        'address' => $server,
                        'port' => $v2_port,
                        'uuid' => $user->uuid,
                        'alterid' => $alter_id,
                        'security' => $security,
                        'flow' => $flow,
                        'encryption' => $encryption,
                        'network' => $network,
                        'header' => $header,
                        'header_type' => $header_type,
                        'host' => $host,
                        'path' => $path,
                        'servicename' => $servicename,
                        'tls' => $tls,
                        'enable_vless' => $enable_vless,
                        'remark' => $node_raw->info,
                    ];
                    break;
                case '14':
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
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'trojan',
                        'address' => $server,
                        'host' => $host,
                        'port' => $trojan_port,
                        'uuid' => $user->uuid,
                        'security' => $security,
                        'mux' => $mux,
                        'transport' => $transport,
                        'transport_plugin' => $transport_plugin,
                        'transport_method' => $transport_method,
                        'allow_insecure' => $allow_insecure,
                        'servicename' => $servicename,
                        'path' => $path,
                        'remark' => $node_raw->info,
                    ];
                    break;
            }
            if ($node === null) {
                continue;
            }
            $nodes[] = $node;
        }

        $sub_info = [
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

        if ($_ENV['subscribeLog'] === true) {
            UserSubscribeLog::addSubscribeLog($user, $subtype, $request->getHeaderLine('User-Agent'));
        }
        //Etag相關，從 WebAPI 那邊抄的
        $header_etag = $request->getHeaderLine('If-None-Match');
        $etag = Tools::etag($sub_info);
        if ($header_etag === $etag) {
            return $response->withStatus(304);
        }

        return $response->withHeader('ETAG', $etag)->withHeader('WebAPI-ETAG', $etag)->withJson([
            $sub_info,
        ]);
    }

    public static function getUniversalSub($user)
    {
        $userid = $user->id;
        $token = Link::where('userid', $userid)->first();
        if ($token === null) {
            $token = new Link();
            $token = $userid;
            $token->token = Tools::genSubToken();
            $token->save();
        }
        return $_ENV['baseUrl'] . '/sub/' . $token->token;
    }
}
