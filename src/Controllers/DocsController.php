<?php
namespace App\Controllers;

class DocsController extends BaseController
{
    public static function groups()
    {
        $groups = [
            'Windows' => [
                [
                    'name' => 'Netch',
                    'url' => '/user/docs/netch',
                ],
                [
                    'name' => 'V2rayN',
                    'url' => '/user/docs/v2rayn',
                ],
                [
                    'name' => 'Clash',
                    'url' => '/user/docs/clash-for-windows',
                ],
                [
                    'name' => 'Shadowsocks',
                    'url' => '/user/docs/shadowsocks-windows',
                ],
                [
                    'name' => 'ShadowsocksR',
                    'url' => '/user/docs/shadowsocksr-windows',
                ],
            ],
            'Android' => [
                [
                    'name' => 'V2rayNG',
                    'url' => '/user/docs/v2rayng',
                ],
                [
                    'name' => 'Clash',
                    'url' => '/user/docs/clash-for-android',
                ],
                [
                    'name' => 'SagerNet',
                    'url' => '/user/docs/sagernet',
                ],
                [
                    'name' => 'Surfboard',
                    'url' => '/user/docs/surfboard',
                ],
                [
                    'name' => 'Kitsunebi',
                    'url' => '/user/docs/kitsunebi-android',
                ],
                [
                    'name' => 'Shadowsocks',
                    'url' => '/user/docs/shadowsocks',
                ],
                [
                    'name' => 'ShadowsocksR',
                    'url' => '/user/docs/shadowsocksr',
                ],
            ],
            'IOS' => [
                [
                    'name' => 'Shadowrocket',
                    'url' => '/user/docs/shadowrocket',
                ],
                [
                    'name' => 'Quantumult',
                    'url' => '/user/docs/quantumult',
                ],
                [
                    'name' => 'QuantumultX',
                    'url' => '/user/docs/quantumultx',
                ],
                [
                    'name' => 'Surge',
                    'url' => '/user/docs/surge',
                ],
                [
                    'name' => 'Kitsunebi',
                    'url' => '/user/docs/kitsunebi-ios',
                ],
                [
                    'name' => 'Stash',
                    'url' => '/user/docs/stash',
                ],
                [
                    'name' => 'Loon',
                    'url' => '/user/docs/loon',
                ],
            ],
            'MacOS' => [
                [
                    'name' => 'v2rayU',
                    'url' => '/user/docs/v2rayu',
                ],
                [
                    'name' => 'ClashX',
                    'url' => '/user/docs/clashx',
                ],
                [
                    'name' => 'Qv2ray',
                    'url' => '/user/docs/qv2ray-macos',
                ],
                [
                    'name' => 'V2rayX',
                    'url' => '/user/docs/v2rayx',
                ],
                [
                    'name' => 'Surge',
                    'url' => '/user/docs/surge-macos',
                ],
            ],
            'Route' => [
                [
                    'name' => 'Merlin',
                    'url' => '/user/docs/merlin',
                ],
            ],
            'Linux-cli' => [
                [
                    'name' => 'V2ray',
                    'url' => '/user/docs/in-development',
                ],
            ],
            'Linux-gui' => [
                [
                    'name' => 'Qv2ray',
                    'url' => '/user/docs/qv2ray',
                ],
                [
                    'name' => 'Electron',
                    'url' => '/user/docs/electron',
                ],
            ],
        ];

        return $groups;
    }
    
    public function index($request, $response, $args)
    {
        $client = $args['client'];

        return $response->write(
            $this->view()
                ->assign('client', $client)
                ->assign('groups', self::groups())
                ->display('user/docs.tpl')
        );
    }
}
