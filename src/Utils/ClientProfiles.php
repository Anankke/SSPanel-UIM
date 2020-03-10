<?php

namespace App\Utils;

use App\Controllers\LinkController;
use App\Services\Config;
use Ramsey\Uuid\Uuid;

class ClientProfiles
{
    public static function getSSPcConf($user)
    {
        $proxy = [];
        $items = URL::getNew_AllItems(
            $user,
            [
                'type' => 'ss'
            ]
        );
        foreach ($items as $item) {
            $proxy_plugin = '';
            $proxy_plugin_opts = '';
            if ($item['obfs'] == 'v2ray' || in_array($item['obfs'], Config::getSupportParam('ss_obfs'))) {
                if ($item['obfs'] == 'v2ray') {
                    $proxy_plugin .= 'v2ray';
                } else {
                    $proxy_plugin .= 'obfs-local';
                }
                if (strpos($item['obfs'], 'http') !== false) {
                    $proxy_plugin_opts .= 'obfs=http';
                } elseif (strpos($item['obfs'], 'tls') !== false) {
                    $proxy_plugin_opts .= 'obfs=tls';
                } else {
                    $proxy_plugin_opts .= 'v2ray;' . $item['obfs_param'];
                }
                if ($item['obfs_param'] != '' && $item['obfs'] != 'v2ray') {
                    $proxy_plugin_opts .= ';obfs-host=' . $item['obfs_param'];
                }
            }
            $proxy[] = [
                'remarks'     => $item['remark'],
                'server'      => $item['address'],
                'server_port' => $item['port'],
                'method'      => $item['method'],
                'password'    => $item['passwd'],
                'timeout'     => 5,
                'plugin'      => $proxy_plugin,
                'plugin_opts' => $proxy_plugin_opts
            ];
        }
        $config = [
            'configs'                => $proxy,
            'strategy'               => null,
            'index'                  => 0,
            'global'                 => false,
            'enabled'                => true,
            'shareOverLan'           => false,
            'isDefault'              => false,
            'localPort'              => 1080,
            'portableMode'           => true,
            'pacUrl'                 => null,
            'useOnlinePac'           => false,
            'secureLocalPac'         => true,
            'availabilityStatistics' => false,
            'autoCheckUpdate'        => true,
            'checkPreRelease'        => false,
            'isVerboseLogging'       => false,
            'logViewer'              => [
                'topMost'         => false,
                'wrapText'        => false,
                'toolbarShown'    => false,
                'Font'            => 'Consolas, 8pt',
                'BackgroundColor' => 'Black',
                'TextColor'       => 'White'
            ],
            'proxy' => [
                'useProxy'     => false,
                'proxyType'    => 0,
                'proxyServer'  => '',
                'proxyPort'    => 0,
                'proxyTimeout' => 3
            ],
            'hotkey' => [
                'SwitchSystemProxy'     => '',
                'SwitchSystemProxyMode' => '',
                'SwitchAllowLan'        => '',
                'ShowLogs'              => '',
                'ServerMoveUp'          => '',
                'ServerMoveDown'        => '',
                'RegHotkeysAtStartup'   => false
            ]
        ];

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    public static function getSSRPcConf($user)
    {
        $proxy = [];
        $items = URL::getNew_AllItems(
            $user,
            [
                'type' => 'ssr'
            ]
        );
        foreach ($items as $item) {
            $proxy[] = [
                'remarks'        => $item['remark'],
                'server'         => $item['address'],
                'server_port'    => $item['port'],
                'method'         => $item['method'],
                'obfs'           => $item['obfs'],
                'obfsparam'      => $item['obfs_param'],
                'remarks_base64' => base64_encode($item['remark']),
                'password'       => $item['passwd'],
                'tcp_over_udp'   => false,
                'udp_over_tcp'   => false,
                'group'          => $_ENV['appName'],
                'protocol'       => $item['protocol'],
                'protocolparam'  => $item['protocol_param'],
                'obfs_udp'       => false,
                'enable'         => true
            ];
        }
        $config = [
            'configs'               => $proxy,
            'index'                 => 0,
            'random'                => true,
            'sysProxyMode'          => 1,
            'shareOverLan'          => false,
            'localPort'             => 1080,
            'localAuthPassword'     => Tools::genRandomChar(26),
            'dnsServer'             => '',
            'reconnectTimes'        => 2,
            'balanceAlgorithm'      => 'LowException',
            'randomInGroup'         => false,
            'TTL'                   => 0,
            'connectTimeout'        => 5,
            'proxyRuleMode'         => 2,
            'proxyEnable'           => false,
            'pacDirectGoProxy'      => false,
            'proxyType'             => 0,
            'proxyHost'             => '',
            'proxyPort'             => 0,
            'proxyAuthUser'         => '',
            'proxyAuthPass'         => '',
            'proxyUserAgent'        => '',
            'authUser'              => '',
            'authPass'              => '',
            'autoBan'               => false,
            'sameHostForSameTarget' => false,
            'keepVisitTime'         => 180,
            'isHideTips'            => false,
            'nodeFeedAutoUpdate'    => true,
            'serverSubscribes'      => [
                [
                    'URL'            => LinkController::getSubinfo($user, 0)['ssr'],
                    'Group'          => $_ENV['appName'],
                    'LastUpdateTime' => 0
                ]
            ],
            'token'   => [],
            'portMap' => []
        ];

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    public static function getSSDPcConf($user)
    {
        $id = 1;
        $proxy = [];
        $items = URL::getNew_AllItems(
            $user,
            [
                'type' => 'ss'
            ]
        );
        foreach ($items as $item) {
            $proxy_plugin = '';
            $proxy_plugin_opts = '';
            if ($item['obfs'] == 'v2ray' || in_array($item['obfs'], Config::getSupportParam('ss_obfs'))) {
                if ($item['obfs'] == 'v2ray') {
                    $proxy_plugin .= 'v2ray';
                } else {
                    $proxy_plugin .= 'simple-obfs';
                }
                if (strpos($item['obfs'], 'http') !== false) {
                    $proxy_plugin_opts .= 'obfs=http';
                } elseif (strpos($item['obfs'], 'tls') !== false) {
                    $proxy_plugin_opts .= 'obfs=tls';
                } else {
                    $proxy_plugin_opts .= 'v2ray;' . $item['obfs_param'];
                }
                if ($item['obfs_param'] != '' && $item['obfs'] != 'v2ray') {
                    $proxy_plugin_opts .= ';obfs-host=' . $item['obfs_param'];
                }
            }
            $proxy[] = [
                'remarks'          => $item['remark'],
                'server'           => $item['address'],
                'server_port'      => $item['port'],
                'password'         => $item['passwd'],
                'method'           => $item['method'],
                'plugin'           => $proxy_plugin,
                'plugin_opts'      => $proxy_plugin_opts,
                'plugin_args'      => '',
                'timeout'          => 5,
                'id'               => $id,
                'ratio'            => $item['ratio'],
                'subscription_url' => LinkController::getSubinfo($user, 0)['ssd']
            ];
            $id++;
        }
        $plugin = '';
        $plugin_opts = '';
        if ($user->obfs == 'v2ray' || in_array($user->obfs, Config::getSupportParam('ss_obfs'))) {
            if ($user->obfs == 'v2ray') {
                $plugin .= 'v2ray';
            } else {
                $plugin .= 'simple-obfs';
            }
            if (strpos($user->obfs, 'http') !== false) {
                $plugin_opts .= 'obfs=http';
            } elseif (strpos($user->obfs, 'tls') !== false) {
                $plugin_opts .= 'obfs=tls';
            } else {
                $plugin_opts .= 'v2ray;' . $user->obfs_param;
            }
            if ($user->obfs_param != '' && $user->obfs != 'v2ray') {
                $plugin_opts .= ';obfs-host=' . $user->obfs_param;
            }
        }
        $config = [
            'configs'                => $proxy,
            'strategy'               => null,
            'index'                  => 0,
            'global'                 => false,
            'enabled'                => true,
            'shareOverLan'           => false,
            'isDefault'              => false,
            'localPort'              => 1080,
            'portableMode'           => true,
            'pacUrl'                 => null,
            'useOnlinePac'           => false,
            'secureLocalPac'         => true,
            'availabilityStatistics' => false,
            'autoCheckUpdate'        => true,
            'checkPreRelease'        => false,
            'isVerboseLogging'       => false,
            'logViewer'              => [
                'topMost'         => false,
                'wrapText'        => false,
                'toolbarShown'    => false,
                'Font'            => 'Consolas, 8pt',
                'BackgroundColor' => 'Black',
                'TextColor'       => 'White'
            ],
            'proxy' => [
                'useProxy'     => false,
                'proxyType'    => 0,
                'proxyServer'  => '',
                'proxyPort'    => 0,
                'proxyTimeout' => 3
            ],
            'hotkey' => [
                'SwitchSystemProxy'     => '',
                'SwitchSystemProxyMode' => '',
                'SwitchAllowLan'        => '',
                'ShowLogs'              => '',
                'ServerMoveUp'          => '',
                'ServerMoveDown'        => '',
                'RegHotkeysAtStartup'   => false
            ],
            'subscriptions' => [
                [
                    'airport'          => $_ENV['appName'],
                    'encryption'       => $user->method,
                    'password'         => $user->passwd,
                    'port'             => $user->port,
                    'expiry'           => $user->class_expire,
                    'traffic_used'     => Tools::flowToGB($user->u + $user->d),
                    'traffic_total'    => Tools::flowToGB($user->transfer_enable),
                    'url'              => LinkController::getSubinfo($user, 0)['ssd'],
                    'plugin'           => $plugin,
                    'plugin_options'   => $plugin_opts,
                    'plugin_arguments' => '',
                    'use_proxy'        => false
                ]
            ]
        ];

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    public static function getV2RayNPcConf($user)
    {
        $subUrl = LinkController::getSubinfo($user, 0)['v2ray'];
        $subId = Uuid::uuid3(Uuid::NAMESPACE_DNS, $subUrl)->toString();
        $config = [
            'inbound' => [
                [
                    'localPort'         => 10808,
                    'protocol'          => 'socks',
                    'udpEnabled'        => true,
                    'sniffingEnabled'   => true,
                ],
            ],
            'logEnabled'        => false,
            'loglevel'          => 'warning',
            'index'             => 0,
            'vmess'             => [],
            'muxEnabled'        => false,
            'domainStrategy'    => 'IPIfNonMatch',
            'routingMode'       => '3',
            'useragent'         => [],
            'userdirect'        => [],
            'userblock'         => [],
            'kcpItem'           => [
                'mtu'               => 1350,
                'tti'               => 50,
                'uplinkCapacity'    => 12,
                'downlinkCapacity'  => 100,
                'congestion'        => false,
                'readBufferSize'    => 2,
                'writeBufferSize'   => 2,
            ],
            'listenerType'          => 0,
            'urlGFWList'            => 'https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt',
            'allowLANConn'          => false,
            'enableStatistics'      => true,
            'statisticsFreshRate'   => 2000,
            'remoteDNS'             => '114.114.114.114,1.2.4.8,223.5.5.5,8,8,8,8',
            'subItem'               => [
                [
                    'id'        => $subId,
                    'remarks'   => $_ENV['appName'],
                    'url'       => $subUrl,
                    'enabled'   => true,
                ],
            ],
            'uiItem' => [
                'mainQRCodeWidth' => 600,
            ],
            'userPacRule' => []
        ];
        $Rule = [
            'type'   => 'vmess'
        ];
        $proxys = [];
        $items = URL::getNew_AllItems($user, $Rule);
        foreach ($items as $item) {
            if (!in_array($item['net'], ['tcp', 'ws', 'kcp', 'h2'])) {
                continue;
            }
            $proxy = [
                'configVersion'     => 2,
                'address'           => $item['add'],
                'port'              => $item['port'],
                'id'                => $item['id'],
                'alterId'           => $item['aid'],
                'security'          => 'auto',
                'network'           => $item['net'],
                'remarks'           => $item['remark'],
                'headerType'        => 'none',
                'requestHost'       => '',
                'path'              => '',
                'streamSecurity'    => '',
                'allowInsecure'     => '',
                'configType'        => 1,
                'testResult'        => '',
                'subid'             => $subId,
            ];
            switch ($item['net']) {
                case 'h2':
                    $proxy['requestHost'] = ($item['host'] != '' ? $item['host'] : $item['add']);
                    $proxy['path']        = $item['path'];
                    break;
                case 'ws':
                    $proxy['requestHost'] = ($item['host'] != '' ? $item['host'] : $item['add']);
                    $proxy['path']        = $item['path'];
                    break;
                case 'kcp':
                    $proxy['headerType']  = $item['type'];
                    break;
            }
            if ($item['tls'] == 'tls') {
                $proxy['streamSecurity'] = $item['tls'];
                if ($item['verify_cert'] == false) {
                    $proxy['allowInsecure'] = 'true';
                }
            }
            $proxys[] = $proxy;
        }
        $config['vmess'] = $proxys;
        return json_encode($config, JSON_PRETTY_PRINT);
    }
}
