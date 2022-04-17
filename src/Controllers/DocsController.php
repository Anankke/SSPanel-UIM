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
                    'switch' => true,
                ],
                [
                    'name' => 'V2rayN',
                    'url' => '/user/docs/v2rayn',
                    'switch' => true,
                ],
                [
                    'name' => 'Clash',
                    'url' => '/user/docs/clash-for-windows',
                    'switch' => true,
                ],
                [
                    'name' => 'Shadowsocks',
                    'url' => '/user/docs/shadowsocks-windows',
                    'switch' => true,
                ],
                [
                    'name' => 'ShadowsocksR',
                    'url' => '/user/docs/shadowsocksr-windows',
                    'switch' => true,
                ],
            ],
            'Android' => [
                [
                    'name' => 'V2rayNG',
                    'url' => '/user/docs/v2rayng',
                    'switch' => true,
                ],
                [
                    'name' => 'Clash',
                    'url' => '/user/docs/clash-for-android',
                    'switch' => true,
                ],
                [
                    'name' => 'SagerNet',
                    'url' => '/user/docs/sagernet',
                    'switch' => true,
                ],
                [
                    'name' => 'Surfboard',
                    'url' => '/user/docs/surfboard',
                    'switch' => true,
                ],
                [
                    'name' => 'Kitsunebi',
                    'url' => '/user/docs/kitsunebi-android',
                    'switch' => true,
                ],
                [
                    'name' => 'Shadowsocks',
                    'url' => '/user/docs/shadowsocks',
                    'switch' => true,
                ],
                [
                    'name' => 'ShadowsocksR',
                    'url' => '/user/docs/shadowsocksr',
                    'switch' => true,
                ],
            ],
            'IOS' => [
                [
                    'name' => 'Shadowrocket',
                    'url' => '/user/docs/shadowrocket',
                    'switch' => true,
                ],
                [
                    'name' => 'Quantumult',
                    'url' => '/user/docs/quantumult',
                    'switch' => true,
                ],
                [
                    'name' => 'QuantumultX',
                    'url' => '/user/docs/quantumultx',
                    'switch' => true,
                ],
                [
                    'name' => 'Surge',
                    'url' => '/user/docs/surge',
                    'switch' => true,
                ],
                [
                    'name' => 'Kitsunebi',
                    'url' => '/user/docs/kitsunebi-ios',
                    'switch' => true,
                ],
                [
                    'name' => 'Stash',
                    'url' => '/user/docs/stash',
                    'switch' => true,
                ],
                [
                    'name' => 'Loon',
                    'url' => '/user/docs/loon',
                    'switch' => true,
                ],
            ],
            'MacOS' => [
                [
                    'name' => 'v2rayU',
                    'url' => '/user/docs/v2rayu',
                    'switch' => true,
                ],
                [
                    'name' => 'ClashX',
                    'url' => '/user/docs/clashx',
                    'switch' => true,
                ],
                [
                    'name' => 'Qv2ray',
                    'url' => '/user/docs/qv2ray-macos',
                    'switch' => true,
                ],
                [
                    'name' => 'V2rayX',
                    'url' => '/user/docs/v2rayx',
                    'switch' => true,
                ],
                [
                    'name' => 'Surge',
                    'url' => '/user/docs/surge-macos',
                    'switch' => true,
                ],
            ],
            'Route' => [
                [
                    'name' => 'Merlin',
                    'url' => '/user/docs/merlin',
                    'switch' => true,
                ],
            ],
            'Linux-cli' => [
                [
                    'name' => 'V2ray',
                    'url' => '/user/docs/in-development',
                    'switch' => true,
                ],
            ],
            'Linux-gui' => [
                [
                    'name' => 'Qv2ray',
                    'url' => '/user/docs/qv2ray',
                    'switch' => true,
                ],
                [
                    'name' => 'Electron',
                    'url' => '/user/docs/electron',
                    'switch' => true,
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
                ->assign('subInfo', LinkController::getSubinfo($this->user, 0))
                ->display('user/docs.tpl')
        );
    }
}
