<?php

namespace App\Utils;

use App\Services\Config;

class AppURI
{
    public static function getItemUrl(array $item, int $is_ss)
    {
        $ss_obfs_list = Config::getSupportParam('ss_obfs');
        if (!$is_ss) {
            $ssurl = $item['address'] . ':' . $item['port'] . ':' . $item['protocol'] . ':' . $item['method'] . ':' . $item['obfs'] . ':' . Tools::base64_url_encode($item['passwd'])
                . '/?obfsparam=' . Tools::base64_url_encode($item['obfs_param'])
                . '&protoparam=' . Tools::base64_url_encode($item['protocol_param'])
                . '&remarks=' . Tools::base64_url_encode($item['remark'])
                . '&group=' . Tools::base64_url_encode($item['group']);

            return 'ssr://' . Tools::base64_url_encode($ssurl);
        }

        if ($is_ss == 2) {
            $personal_info = $item['method'] . ':' . $item['passwd'] . '@' . $item['address'] . ':' . $item['port'];
            $ssurl = 'ss://' . Tools::base64_url_encode($personal_info);
            $ssurl .= ($_ENV['add_appName_to_ss_uri'] === true
                ? '#' . rawurlencode($_ENV['appName'] . ' - ' . $item['remark'])
                : '#' . rawurlencode($item['remark']));
        } else {
            $personal_info = $item['method'] . ':' . $item['passwd'];
            $ssurl = 'ss://' . Tools::base64_url_encode($personal_info) . '@' . $item['address'] . ':' . $item['port'];
            $plugin = '';
            if ($item['obfs'] == 'v2ray' || in_array($item['obfs'], $ss_obfs_list)) {
                if (strpos($item['obfs'], 'http') !== false) {
                    $plugin .= 'obfs-local;obfs=http';
                } elseif (strpos($item['obfs'], 'tls') !== false) {
                    $plugin .= 'obfs-local;obfs=tls';
                } else {
                    $plugin .= 'v2ray;' . $item['obfs_param'];
                }
                if ($item['obfs_param'] != '' && $item['obfs'] != 'v2ray') {
                    $plugin .= ';obfs-host=' . $item['obfs_param'];
                }
                $ssurl .= '/?plugin=' . rawurlencode($plugin) . '&group=' . Tools::base64_url_encode($_ENV['appName']);
            } else {
                $ssurl .= '/?group=' . Tools::base64_url_encode($_ENV['appName']);
            }
            $ssurl .= ($_ENV['add_appName_to_ss_uri'] === true
                ? '#' . rawurlencode($_ENV['appName'] . ' - ' . $item['remark'])
                : '#' . rawurlencode($item['remark']));
        }
        return $ssurl;
    }

    public static function getV2RayNURI(array $item)
    {
        $return = null;
        switch ($item['type']) {
            case 'vmess':
                $node = [
                    'v'     => 2,
                    'ps'    => $item['remark'],
                    'add'   => $item['add'],
                    'port'  => $item['port'],
                    'id'    => $item['id'],
                    'aid'   => $item['aid'],
                    'net'   => $item['net'],
                    'type'  => $item['headerType'],
                    'host'  => $item['host'],
                    'path'  => $item['path'],
                    'tls'   => $item['tls']
                ];
                $return = ('vmess://' . base64_encode(
                    json_encode($node, 320)
                ));
                break;
        }
        return $return;
    }

    public static function getSurgeURI(array $item, int $version)
    {
        $return = null;
        switch ($version) {
            case 2:
                if ($item['obfs'] == 'v2ray') {
                    break;
                }
                if ($item['type'] == 'ss') {
                    $return = ($item['remark'] . ' = custom, ' . $item['address'] . ', ' . $item['port'] . ', ' . $item['method'] . ', ' . $item['passwd'] . ', https://raw.githubusercontent.com/lhie1/Rules/master/SSEncrypt.module' . URL::getSurgeObfs($item));
                }
                break;
            default:
                switch ($item['type']) {
                    case 'ss':
                        if ($item['obfs'] == 'v2ray') {
                            break;
                        }
                        $return = ($item['remark'] . ' = ss, ' . $item['address'] . ', ' . $item['port'] . ', encrypt-method=' . $item['method'] . ', password=' . $item['passwd'] . URL::getSurgeObfs($item) . ', udp-relay=true');
                        break;
                    case 'vmess':
                        if (!in_array($item['net'], ['ws', 'tcp'])) {
                            break;
                        }
                        $tls = ($item['tls'] == 'tls'
                            ? ', tls=true'
                            : '');
                        $ws = ($item['net'] == 'ws'
                            ? ', ws=true, ws-path=' . $item['path'] . ', ws-headers=host:' . $item['host']
                            : '');
                        $return = $item['remark'] . ' = vmess, ' . $item['add'] . ', ' . $item['port'] . ', username = ' . $item['id'] . $ws . $tls;
                        break;
                    case 'trojan':
                        $return = ($item['remark'] . ' = trojan, ' . $item['address'] . ', ' . $item['port'] . ', password=' . $item['passwd']) . ", sni=" . $item['host'];
                        break;
                }
                break;
        }
        return $return;
    }

    public static function getQuantumultURI(array $item, bool $base64_encode = false)
    {
        $return = null;
        switch ($item['type']) {
            case 'ss':
                if ($item['obfs'] == 'v2ray') {
                    break;
                }
                $return = ($item['remark'] . ' = shadowsocks, ' . $item['address'] . ', ' . $item['port'] . ', ' . $item['method'] . ', "' . $item['passwd'] . '", upstream-proxy=false, upstream-proxy-auth=false' . URL::getSurgeObfs($item) . ', group=' . $_ENV['appName'] . '_ss');
                break;
            case 'ssr':
                $return = ($item['remark'] . ' = shadowsocksr, ' . $item['address'] . ', ' . $item['port'] . ', ' . $item['method'] . ', "' . $item['passwd'] . '", protocol=' . $item['protocol'] . ', protocol_param=' . $item['protocol_param'] . ', obfs=' . $item['obfs'] . ', obfs_param="' . $item['obfs_param'] . '", group=' . $_ENV['appName']);
                break;
            case 'vmess':
                if (!in_array($item['net'], ['ws', 'tcp', 'http'])) {
                    break;
                }
                $tls = ', over-tls=false, certificate=1';
                if ($item['tls'] == 'tls') {
                    $tls = ', over-tls=true, tls-host=' . $item['host'];
                    if ($item['verify_cert']) {
                        $tls .= ', certificate=1';
                    } else {
                        $tls .= ', certificate=0';
                    }
                }
                $obfs = '';
                if (in_array($item['net'], ['ws', 'http'])) {
                    $obfs = ', obfs=' . $item['net'] . ', obfs-path="' . $item['path'] . '", obfs-header="Host: ' . $item['host'] . '[Rr][Nn]User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 18_0_0 like Mac OS X) AppleWebKit/888.8.88 (KHTML, like Gecko) Mobile/6666666"';
                }
                $return = ($item['remark'] . ' = vmess, ' . $item['add'] . ', ' . $item['port'] . ', chacha20-ietf-poly1305, "' . $item['id'] . '", group=' . $_ENV['appName'] . '_VMess' . $tls . $obfs);
                if ($base64_encode === true) {
                    $return = 'vmess://' . base64_encode($return);
                }
                break;
        }
        return $return;
    }

    public static function getQuantumultXURI(array $item)
    {
        $return = null;
        switch ($item['type']) {
            case 'ss':
                // ;shadowsocks=example.com:80, method=chacha20, password=pwd, obfs=http, obfs-host=bing.com, obfs-uri=/resource/file, fast-open=false, udp-relay=false, server_check_url=http://www.apple.com/generate_204, tag=ss-01
                // ;shadowsocks=example.com:80, method=chacha20, password=pwd, obfs=http, obfs-host=bing.com, obfs-uri=/resource/file, fast-open=false, udp-relay=false, tag=ss-02
                // ;shadowsocks=example.com:443, method=chacha20, password=pwd, obfs=tls, obfs-host=bing.com, fast-open=false, udp-relay=false, tag=ss-03
                // ;shadowsocks=example.com:80, method=aes-128-gcm, password=pwd, obfs=ws, fast-open=false, udp-relay=false, tag=ss-ws-01
                // ;shadowsocks=example.com:80, method=aes-128-gcm, password=pwd, obfs=ws, obfs-uri=/ws, fast-open=false, udp-relay=false, tag=ss-ws-02
                // ;shadowsocks=example.com:443, method=aes-128-gcm, password=pwd, obfs=wss, obfs-uri=/ws, fast-open=false, udp-relay=false, tag=ss-ws-tls
                $return = ('shadowsocks=' . $item['address'] . ':' . $item['port'] . ', method=' . $item['method'] . ', password=' . $item['passwd']);
                switch ($item['obfs']) {
                    case 'simple_obfs_http':
                        $return .= ', obfs=http';
                        $return .= ($item['obfs_param'] != '' ? ', obfs-host=' . $item['obfs_param'] : ', obfs-host=wns.windows.com');
                        $return .= ', obfs-uri=/';
                        break;
                    case 'simple_obfs_tls':
                        $return .= ', obfs=tls';
                        $return .= ($item['obfs_param'] != '' ? ', obfs-host=' . $item['obfs_param'] : ', obfs-host=wns.windows.com');
                        $return .= ', obfs-uri=/';
                        break;
                    case 'v2ray';
                        $return .= ($item['tls'] == 'tls' ? ', obfs=wss' : ', obfs=ws');
                        $return .= ', obfs-uri=' . $item['path'] . ', obfs-host=' . $item['host'];
                        break;
                }
                $return .= (', tag=' . $item['remark']);
                break;
            case 'ssr':
                // ;shadowsocks=example.com:443, method=chacha20, password=pwd, ssr-protocol=auth_chain_b, ssr-protocol-param=def, obfs=tls1.2_ticket_fastauth, obfs-host=bing.com, tag=ssr
                $return = ('shadowsocks=' . $item['address'] . ':' . $item['port'] . ', method=' . $item['method'] . ', password=' . $item['passwd']);
                $return .= (', ssr-protocol=' . $item['protocol']);
                $return .= (', ssr-protocol-param=' . $item['protocol_param']);
                $return .= (', obfs=' . $item['obfs']);
                $return .= (', obfs-host="' . $item['obfs_param']);
                $return .= (', tag=' . $item['remark']);
                break;
            case 'vmess':
                // ;vmess=example.com:80, method=none, password=23ad6b10-8d1a-40f7-8ad0-e3e35cd32291, fast-open=false, udp-relay=false, tag=vmess-01
                // ;vmess=example.com:80, method=aes-128-gcm, password=23ad6b10-8d1a-40f7-8ad0-e3e35cd32291, fast-open=false, udp-relay=false, tag=vmess-02
                // ;vmess=example.com:443, method=none, password=23ad6b10-8d1a-40f7-8ad0-e3e35cd32291, obfs=over-tls, fast-open=false, udp-relay=false, tag=vmess-tls
                // ;vmess=example.com:80, method=chacha20-poly1305, password=23ad6b10-8d1a-40f7-8ad0-e3e35cd32291, obfs=ws, obfs-uri=/ws, fast-open=false, udp-relay=false, tag=vmess-ws
                // ;vmess=example.com:443, method=chacha20-poly1305, password=23ad6b10-8d1a-40f7-8ad0-e3e35cd32291, obfs=wss, obfs-uri=/ws, fast-open=false, udp-relay=false, tag=vmess-ws-tls
                if (!in_array($item['net'], ['ws', 'tcp'])) {
                    break;
                }
                $return = ('vmess=' . $item['add'] . ':' . $item['port'] . ', method=chacha20-poly1305' . ', password=' . $item['id']);
                switch ($item['net']) {
                    case 'ws':
                        $return .= ($item['tls'] == 'tls' ? ', obfs=wss' : ', obfs=ws');
                        $return .= ', obfs-uri=' . $item['path'] . ', obfs-host=' . $item['host'];
                        break;
                    case 'tcp':
                        $return .= ($item['tls'] == 'tls' ? ', obfs=over-tls' : '');
                        break;
                }
                $return .= (', tag=' . $item['remark']);
                break;
            case 'trojan':
                // ;trojan=example.com:443, password=pwd, over-tls=true, tls-verification=true, fast-open=false, udp-relay=false, tag=trojan-tls-01
                $return  = ('trojan=' . $item['address'] . ':' . $item['port'] . ', password=' . $item['passwd'] . ', tls-host=' . $item['host']);
                $return .= ', over-tls=true, tls-verification=true';
                $return .= (', tag=' . $item['remark']);
                break;
        }
        return $return;
    }

    public static function getSurfboardURI(array $item)
    {
        $return = null;
        switch ($item['type']) {
            case 'ss':
                if ($item['obfs'] == 'v2ray') {
                    break;
                }
                $return = ($item['remark'] . ' = custom, ' . $item['address'] . ', ' . $item['port'] . ', ' . $item['method'] . ', ' . $item['passwd'] . ', https://raw.githubusercontent.com/lhie1/Rules/master/SSEncrypt.module' . URL::getSurgeObfs($item));
                break;
            case 'vmess':
                if (!in_array($item['net'], ['ws', 'tcp'])) {
                    break;
                }
                $tls = ($item['tls'] == 'tls'
                    ? ', tls=true'
                    : '');
                $ws = ($item['net'] == 'ws'
                    ? ', ws=true, ws-path=' . $item['path'] . ', ws-headers=host:' . $item['host']
                    : '');
                $return = $item['remark'] . ' = vmess, ' . $item['add'] . ', ' . $item['port'] . ', username = ' . $item['id'] . $ws . $tls;
                break;
        }
        return $return;
    }

    public static function getClashURI(array $item, bool $ssr_support = false)
    {
        $return = null;
        if ($item['type'] == 'ssr' && $ssr_support === false) {
            return $return;
        }
        switch ($item['type']) {
            case 'ss':
                $method = ['rc4-md5-6', 'camellia-128-cfb', 'camellia-192-cfb', 'camellia-256-cfb', 'bf-cfb', 'cast5-cfb', 'des-cfb', 'des-ede3-cfb', 'idea-cfb', 'rc2-cfb', 'seed-cfb', 'salsa20', 'chacha20', 'xsalsa20', 'none'];
                if (in_array($item['method'], $method)) {
                    // 不支持的
                    break;
                }
                $return = [
                    'name'        => $item['remark'],
                    'type'        => 'ss',
                    'server'      => $item['address'],
                    'port'        => $item['port'],
                    'cipher'      => $item['method'],
                    'password'    => $item['passwd'],
                    'udp'         => true
                ];
                if ($item['obfs'] != 'plain') {
                    switch ($item['obfs']) {
                        case 'simple_obfs_http':
                            $return['plugin'] = 'obfs';
                            $return['plugin-opts']['mode'] = 'http';
                            break;
                        case 'simple_obfs_tls':
                            $return['plugin'] = 'obfs';
                            $return['plugin-opts']['mode'] = 'tls';
                            break;
                        case 'v2ray':
                            $return['plugin'] = 'v2ray-plugin';
                            $return['plugin-opts']['mode'] = 'websocket';
                            if ($item['tls'] == 'tls') {
                                $return['plugin-opts']['tls'] = true;
                                if ($item['verify_cert'] == false) {
                                    $return['plugin-opts']['skip-cert-verify'] = true;
                                }
                            }
                            $return['plugin-opts']['host'] = $item['host'];
                            $return['plugin-opts']['path'] = $item['path'];
                            break;
                    }
                    if ($item['obfs'] != 'v2ray') {
                        if ($item['obfs_param'] != '') {
                            $return['plugin-opts']['host'] = $item['obfs_param'];
                        } else {
                            $return['plugin-opts']['host'] = 'windowsupdate.windows.com';
                        }
                    }
                }
                break;
            case 'ssr':
                // if (
                //     in_array($item['method'], ['rc4-md5-6', 'des-ede3-cfb', 'xsalsa20', 'none'])
                //     ||
                //     in_array($item['protocol'], array_merge(Config::getSupportParam('allow_none_protocol'), ['verify_deflate']))
                //     ||
                //     in_array($item['obfs'], ['tls1.2_ticket_fastauth'])
                // ) {
                //     // 不支持的
                //     break;
                // }
                $return = [
                    'name'            => $item['remark'],
                    'type'            => 'ssr',
                    'server'          => $item['address'],
                    'port'            => $item['port'],
                    'cipher'          => $item['method'],
                    'password'        => $item['passwd'],
                    'protocol'        => $item['protocol'],
                    'protocolparam'   => $item['protocol_param'],
                    'obfs'            => $item['obfs'],
                    'obfsparam'       => $item['obfs_param']
                ];
                break;
            case 'vmess':
                if (!in_array($item['net'], array('ws', 'tcp'))) {
                    break;
                }
                $return = [
                    'name'    => $item['remark'],
                    'type'    => 'vmess',
                    'server'  => $item['add'],
                    'port'    => $item['port'],
                    'uuid'    => $item['id'],
                    'alterId' => $item['aid'],
                    'cipher'  => 'auto',
                    'udp'     => true
                ];
                if ($item['net'] == 'ws') {
                    $return['network'] = 'ws';
                    $return['ws-path'] = $item['path'];
                    $return['ws-headers']['Host'] = ($item['host'] != '' ? $item['host'] : $item['add']);
                }
                if ($item['tls'] == 'tls') {
                    $return['tls'] = true;
                    if ($item['verify_cert'] == false) {
                        $return['skip-cert-verify'] = true;
                    }
                }
                break;
            case 'trojan':
                $return = [
                    'name'        => $item['remark'],
                    'type'        => 'trojan',
                    'server'      => $item['address'],
                    'port'        => $item['port'],
                    'password'    => $item['passwd'],
                    'sni'         => $item['host']
                ];
                break;
        }
        return $return;
    }

    public static function getShadowrocketURI(array $item)
    {
        $return = null;
        switch ($item['type']) {
            case 'ss':
                if (in_array($item['obfs'], Config::getSupportParam('ss_obfs'))) {
                    $return = (self::getItemUrl($item, 1));
                } else {
                    if ($item['obfs'] == 'v2ray') {
                        $v2rayplugin = [
                            'address'   => $item['address'],
                            'port'      => (string) $item['port'],
                            'path'      => $item['path'],
                            'host'      => $item['host'],
                            'mode'      => 'websocket',
                        ];
                        $v2rayplugin['tls'] = $item['tls'] == 'tls' ? true : false;
                        $return = ('ss://' . Tools::base64_url_encode($item['method'] . ':' . $item['passwd'] . '@' . $item['address'] . ':' . $item['port']) . '?v2ray-plugin=' . base64_encode(json_encode($v2rayplugin)) . '#' . rawurlencode($item['remark']));
                    }
                    if ($item['obfs'] == 'plain') {
                        $return = (self::getItemUrl($item, 2));
                    }
                }
                break;
            case 'ssr':
                $return = (self::getItemUrl($item, 0));
                break;
            case 'vmess':
                if (!in_array($item['net'], ['tcp', 'ws', 'http', 'h2'])) {
                    break;
                }
                $obfs = '';
                switch ($item['net']) {
                    case 'ws':
                        $obfs .= ($item['host'] != ''
                            ? ('&obfsParam=' . $item['host'] . '&path=' . $item['path'] . '&obfs=websocket')
                            : ('&obfsParam=' . $item['add'] . '&path=' . $item['path'] . '&obfs=websocket'));
                        break;
                    case 'kcp':
                        $obfs .= 'obfsParam={"header":' . '"' . ($item['headerType'] == '' || $item['headerType'] == 'noop' ? 'none' : $item['headerType']) . '"' . '}&obfs=mkcp';
                        break;
                    case 'mkcp':
                        $obfs .= 'obfsParam={"header":' . '"' . ($item['headerType'] == '' || $item['headerType'] == 'noop' ? 'none' : $item['headerType']) . '"' . '}&obfs=mkcp';
                        break;
                    case 'h2':
                        $obfs .= ($item['host'] != ''
                            ? ('&obfsParam=' . $item['host'] . '&path=' . $item['path'] . '&obfs=h2')
                            : ('&obfsParam=' . $item['add'] . '&path=' . $item['path'] . '&obfs=h2'));
                        break;
                    default:
                        $obfs .= '&obfs=none';
                        break;
                }
                $tls = '';
                if ($item['tls'] == 'tls') {
                    $tls = '&tls=1';
                    if ($item['verify_cert'] == false) {
                        $tls .= '&allowInsecure=1';
                    }
                    if (isset($item['localserver'])) {
                        $tls .= '&peer=' . $item['localserver'];
                    }
                }
                $return = ('vmess://' . Tools::base64_url_encode('chacha20-poly1305:' . $item['id'] . '@' . $item['add'] . ':' . $item['port']) . '?remarks=' . rawurlencode($item['remark']) . $obfs . $tls);
                break;
            case 'trojan':
                $return  = ('trojan://' . $item['passwd'] . '@' . $item['address'] . ':' . $item['port']);
                $return .= ('?peer=' . $item['host'] . '#' . rawurlencode($item['remark']));
                break;
        }
        return $return;
    }

    public static function getKitsunebiURI(array $item)
    {
        $return = null;
        switch ($item['type']) {
            case 'ss':
                if (in_array($item['obfs'], ['v2ray', 'simple_obfs_http', 'simple_obfs_tls'])) {
                    break;
                }
                $return = (self::getItemUrl($item, 2));
                break;
            case 'vmess':
                $network = ($item['net'] == 'tls'
                    ? '&network=tcp'
                    : ('&network=' . $item['net']));
                $protocol = '';
                switch ($item['net']) {
                    case 'kcp':
                        $protocol .= ('&kcpheader=' . $item['headerType']);
                        break;
                    case 'ws':
                        $protocol .= ('&wspath=' . $item['path'] . '&wsHost=' . $item['host']);
                        break;
                    case 'h2':
                        $protocol .= ('&h2Path=' . $item['path'] . '&h2Host=' . $item['host']);
                        break;
                }
                $tls = '';
                if ($item['tls'] == 'tls') {
                    $tls = '&tls=1';
                    if ($item['verify_cert'] == false) {
                        $tls .= '&allowInsecure=1';
                    }
                }
                $return .= ('vmess://' . base64_encode('auto:' . $item['id'] . '@' . $item['add'] . ':' . $item['port']) . '?remark=' . rawurlencode($item['remark']) . $network . $protocol . '&aid=' . $item['aid'] . $tls);
                break;
        }
        return $return;
    }

    public static function getSSDURI(array $item)
    {
        $return = null;
        switch ($item['type']) {
            case 'ss':
                # 666
                $return['remarks']      = $item['remark'];
                $return['server']       = $item['address'];
                $return['port']         = $item['port'];
                $return['encryption']   = $item['method'];
                $return['password']     = $item['passwd'];
                $plugin_options         = '';
                if ($item['obfs'] != 'plain') {
                    switch ($item['obfs']) {
                        case 'simple_obfs_http':
                            $return['plugin'] = 'simple-obfs';
                            $plugin_options .= 'obfs=http;obfs-host=' . $item['obfs_param'];
                            break;
                        case 'simple_obfs_tls':
                            $return['plugin'] = 'simple-obfs';
                            $plugin_options .= 'obfs=tls;obfs-host=' . $item['obfs_param'];
                            break;
                        case 'v2ray':
                            $return['plugin'] = 'v2ray';
                            if ($item['net'] == 'ws') {
                                $plugin_options .= 'mode=ws';
                            }
                            if ($item['tls'] == 'tls') {
                                $plugin_options .= ';security=tls';
                            } else {
                                $plugin_options .= ';security=none';
                            }
                            $plugin_options .= ';path=' . $item['path'];
                            if ($item['host'] != '') {
                                $plugin_options .= ';host=' . $item['host'];
                            } else {
                                $plugin_options .= ';host=' . $item['address'];
                            }
                            break;
                    }
                }
                $return['plugin_options'] = $plugin_options;
                $return['ratio']          = $item['ratio'];
                break;
        }
        return $return;
    }

    public static function getSSJSON(array $item)
    {
        $return = null;
        switch ($item['type']) {
            case 'ss':
                # 666
                $return['remarks']      = $item['remark'];
                $return['server']       = $item['address'];
                $return['server_port']  = $item['port'];
                $return['method']       = $item['method'];
                $return['password']     = $item['passwd'];
                if ($item['obfs'] != 'plain') {
                    $plugin_options         = '';
                    switch ($item['obfs']) {
                        case 'simple_obfs_http':
                            $return['plugin'] = 'simple-obfs';
                            $plugin_options .= 'obfs=http;obfs-host=' . $item['obfs_param'];
                            break;
                        case 'simple_obfs_tls':
                            $return['plugin'] = 'simple-obfs';
                            $plugin_options .= 'obfs=tls;obfs-host=' . $item['obfs_param'];
                            break;
                        case 'v2ray':
                            $return['plugin'] = 'v2ray';
                            if ($item['net'] == 'ws') {
                                $plugin_options .= 'mode=ws';
                            }
                            if ($item['tls'] == 'tls') {
                                $plugin_options .= ';security=tls';
                            } else {
                                $plugin_options .= ';security=none';
                            }
                            $plugin_options .= ';path=' . $item['path'];
                            if ($item['host'] != '') {
                                $plugin_options .= ';host=' . $item['host'];
                            } else {
                                $plugin_options .= ';host=' . $item['address'];
                            }
                            break;
                    }
                    $return['plugin_opts'] = $plugin_options;
                }
                break;
        }
        return $return;
    }

    public static function getTrojanURI(array $item)
    {
        $return = null;
        switch ($item['type']) {
            case 'trojan':
                $return  = ('trojan://' . $item['passwd'] . '@' . $item['address'] . ':' . $item['port']);
                $return .= ('?peer=' . $item['host'] . '#' .  rawurlencode($item['remark']));
                break;
        }
        return $return;
    }
}
