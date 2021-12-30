<?php

namespace App\Controllers;

use App\Utils\Tools;
use App\Models\{
    Link,
    Node,
    UserSubscribeLog
};
use Psr\Http\Message\ResponseInterface;
use Slim\Http\{
    Request,
    Response
};

/**
 *  SubController
 */
class SubController extends BaseController
{
    public static function getContent($request, $response, $args): ResponseInterface
    {
        if (!$_ENV['Subscribe']) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $token = $args['token'];
        $subtype = $args['subtype'];

        $sub_token = Link::where('token', $token)->first();
        if ($sub_token == null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $user = $sub_token->getUser();
        if ($user == null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $subtype_list = ['all', 'ss', 'ssr', 'v2ray', 'trojan'];
        if (!in_array($subtype, $subtype_list)) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $nodes = array();
        //篩選出用戶能連接的節點，感謝 @AVX512
        $nodes_raw = Node::where('type', 1)
            ->where('node_class', '<=', $user->class)
            ->whereIn('node_group', [0, $user->group])
            ->where(function ($query) {
                $query->where('node_bandwidth_limit', '=', 0)->orWhereRaw('node_bandwidth < node_bandwidth_limit');
            })
            ->get();

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);
            //檢查是否配置“前端/订阅中下发的服务器地址”
            if (!array_key_exists('server_user', $node_custom_config)) {
                $server = $node_raw->server;
            } else {
                $server = $node_custom_config['server_user'];
            }
            switch ($node_raw->sort) {
                case "0":
                    //只給下發正確類型的節點
                    if (!in_array($subtype, ['ss', 'all'])) {
                        $node = null;
                        break;
                    }
                    //處理一下 Undefined Index
                    $plugin = $node_custom_config['plugin'] ?? '';
                    $plugin_option = $node_custom_config['plugin_option'] ?? '';
                    $node = [
                        "name" => $node_raw->name,
                        "id" => $node_raw->id,
                        "type" => "ss",
                        "address" => $server,
                        "port" => $user->port,
                        "password" => $user->passwd,
                        "encryption" => $user->method,
                        "plugin" => $plugin,
                        "plugin_option" => $plugin_option,
                        "remark" => $node_raw->info
                    ];
                    break;
                //單獨加了一種SSR節點類型用來同時處理多端口和單端口SSR的訂閲下發
                case "1":
                    if (!in_array($subtype, ['ssr', 'all'])) {
                        $node = null;
                        break;
                    }
                    //判斷一下是普通SSR節點還是單端口SSR節點，混淆式单端就去掉了，配起来怪麻烦的
                    if ($node_raw->mu_only == -1) {
                        $node = [
                            "name" => $node_raw->name,
                            "id" => $node_raw->id,
                            "type" => "ssr",
                            "address" => $server,
                            "port" => $user->port,
                            "password" => $user->passwd,
                            "encryption" => $user->method,
                            "protocol" => $user->protocol,
                            "protocol_param" => $user->protocol_param,
                            "obfs" => $user->obfs,
                            "obfs_param" => $user->obfs_param,
                            "remark" => $node_raw->info
                        ];
                    } else {
                        //優先級是 mu_port > offset_port_user > offset_port_node ，v2 和 trojan 同理
                        if (!array_key_exists('mu_port', $node_custom_config)
                            && !array_key_exists('offset_port_user', $node_custom_config)) {
                            $mu_port = $node_custom_config['offset_port_node'];
                        } elseif (!array_key_exists('mu_port', $node_custom_config)) {
                            $mu_port = $node_custom_config['offset_port_user'];
                        } else {
                            $mu_port = $node_custom_config['mu_port'];
                        }
                        $mu_password = $node_custom_config['mu_password'] ?? '';
                        $mu_encryption = $node_custom_config['mu_encryption'] ?? '';
                        $mu_protocol = $node_custom_config['mu_protocol'] ?? '';
                        $mu_obfs = $node_custom_config['mu_obfs'] ?? '';
                        $mu_suffix = $node_custom_config['mu_suffix'] ?? '';
                        //現在就只能用協議式單端口。理論上應該加個協議式單端口和混淆式單端口的配置項，然後這裏寫個判斷切換的。先咕了，SSR不是重點。
                        $user_protocol_param = $user->id . ':' . $user->passwd;
                        $node = [
                            "name" => $node_raw->name,
                            "id" => $node_raw->id,
                            "type" => "ssr",
                            "address" => $server,
                            "port" => $mu_port,
                            "password" => $mu_password,
                            "encryption" => $mu_encryption,
                            "protocol" => $mu_protocol,
                            "protocol_param" => $user_protocol_param,
                            "obfs" => $mu_obfs,
                            "obfs_param" => $mu_suffix,
                            "remark" => $node_raw->info
                        ];
                    }
                    break;
                case "11":
                    if (!in_array($subtype, ['v2ray', 'all'])) {
                        $node = null;
                        break;
                    }
                    if (!array_key_exists('v2_port', $node_custom_config)
                        && !array_key_exists('offset_port_user', $node_custom_config)
                        && !array_key_exists('offset_port_node', $node_custom_config)) {
                        $v2_port = 443;
                    } elseif (!array_key_exists('v2_port', $node_custom_config)
                        && !array_key_exists('offset_port_user', $node_custom_config)) {
                        $v2_port = $node_custom_config['offset_port_node'];
                    } elseif (!array_key_exists('v2_port', $node_custom_config)) {
                        $v2_port = $node_custom_config['offset_port_user'];
                    } else {
                        $v2_port = $node_custom_config['v2_port'];
                    }
                    //V2Ray 真給我整不會了，有好好的 Trojan 不用用什麽 V2。默認值有問題的請懂 V2 怎麽用的人來改一改。
                    $alter_id = $node_custom_config['alter_id'] ?? '0';
                    $security = $node_custom_config['security'] ?? 'none';
                    $flow = $node_custom_config['flow'] ?? '';
                    $encryption = $node_custom_config['encryption'] ?? '';
                    $network = $node_custom_config['network'] ?? '';
                    $header = $node_custom_config['header'] ?? ["type" => "none"];
                    $header_type = $header['type'] ?? '';
                    $host = $node_custom_config['host'] ?? '';
                    $servicename = $node_custom_config['servicename'] ?? '';
                    $path = $node_custom_config['path'] ?? '/';
                    $tls = in_array($security, ['tls', 'xtls']) ? '1' : '0';
                    $enable_vless = $node_custom_config['enable_vless'] ?? '0';
                    $node = [
                        "name" => $node_raw->name,
                        "id" => $node_raw->id,
                        "type" => "v2ray",
                        "address" => $server,
                        "port" => $v2_port,
                        "uuid" => $user->uuid,
                        "alterid" => $alter_id,
                        "security" => $security,
                        "flow" => $flow,
                        "encryption" => $encryption,
                        "network" => $network,
                        "header" => $header,
                        "header_type" => $header_type,
                        "host" => $host,
                        "path" => $path,
                        "servicename" => $servicename,
                        "tls" => $tls,
                        "enable_vless" => $enable_vless,
                        "remark" => $node_raw->info
                    ];
                    break;
                case "14":
                    if (!in_array($subtype, ['trojan', 'all'])) {
                        $node = null;
                        break;
                    }
                    if (!array_key_exists('trojan_port', $node_custom_config)
                        && !array_key_exists('offset_port_user', $node_custom_config)
                        && !array_key_exists('offset_port_node', $node_custom_config)) {
                        $trojan_port = 443;
                    } elseif (!array_key_exists('trojan_port', $node_custom_config)
                        && !array_key_exists('offset_port_user', $node_custom_config)) {
                        $trojan_port = $node_custom_config['offset_port_node'];
                    } elseif (!array_key_exists('trojan_port', $node_custom_config)) {
                        $trojan_port = $node_custom_config['offset_port_user'];
                    } else {
                        $trojan_port = $node_custom_config['trojan_port'];
                    }
                    $host = $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? '0';
                    //Trojan-Go 啥都好，就是特性連個支持的付費後端都沒有
                    $security = $node_custom_config['security'] ?? $node_custom_config['enable_xtls'] == '1' ? 'xtls' : 'tls';
                    $mux = $node_custom_config['mux'] ?? '';
                    $transport = $node_custom_config['transport'] ?? $node_custom_config['grpc'] == '1' ? 'grpc' : 'tcp';;
                    $transport_plugin = $node_custom_config['transport_plugin'] ?? '';
                    $transport_method = $node_custom_config['transport_method'] ?? '';
                    $servicename = $node_custom_config['servicename'] ?? '';
                    $path = $node_custom_config['path'] ?? '';
                    $node = [
                        "name" => $node_raw->name,
                        "id" => $node_raw->id,
                        "type" => "trojan",
                        "address" => $server,
                        "host" => $host,
                        "port" => $trojan_port,
                        "uuid" => $user->uuid,
                        "security" => $security,
                        "mux" => $mux,
                        "transport" => $transport,
                        "transport_plugin" => $transport_plugin,
                        "transport_method" => $transport_method,
                        "allow_insecure" => $allow_insecure,
                        "servicename" => $servicename,
                        "path" => $path,
                        "remark" => $node_raw->info,
                    ];
                    break;
            }
            if ($node == null) {
                continue;
            } else {
                $nodes[] = $node;
            }
        }

        $sub_info = [
            "version" => 1,
            "sub_name" => $_ENV['appName'],
            "user_email" => $user->email,
            "user_name" => $user->user_name,
            "user_class" => $user->class,
            "user_class_expire_date" => $user->class_expire,
            "user_total_traffic" => $user->transfer_enable,
            "user_used_traffic" => $user->u + $user->d,
            "nodes" => $nodes
        ];

        if ($_ENV['subscribeLog'] === true) {
            UserSubscribeLog::addSubscribeLog($user, $subtype, $request->getHeaderLine('User-Agent'));
        }
        //Etag相關，從 WebAPI 那邊抄的
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($sub_info);
        if ($header_etag == $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson([
            $sub_info
        ]);
    }

    public static function getUniversalSub($user)
    {
        $userid = $user->id;
        $token = Link::where('userid', $userid)->first();
        if ($token == null) {
            $token = new Link();
            $token = $userid;
            $token->token = Tools::genSubToken();
            $token->save();
        }
        return $_ENV['baseUrl'] . '/sub/' . $token->token;
    }
}
