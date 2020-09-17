<?php

/**
 * default 为默认配置，你可以添加其他配置，但必须保证默认配置存在
 *
 * Checks 填写为没有直接在规则文件中使用的并且使用了筛选规则且组内或可能无节点的策略组名
 *  - 例如使用 regex 分类国家分组，未匹配时组内无节点，此类需要填入 Checks 中以保证配置文件无误
 *
 * Surge 以及 Surfboard 的 General 中，布尔值请填写为字符串
 *
 * Surge 以及 Surfboard 的 Proxy 中，请填写为该应用的格式
 * Clash 的 Proxy 中，请填写为数组
 */

/**
 * Surge 配置文件定义
 */
$_ENV['Surge_Profiles'] = [
    'default' => [
        'Checks' => [],
        'General' => [
            'loglevel'                    => 'notify',
            'dns-server'                  => 'system, 117.50.10.10, 119.29.29.29, 223.6.6.6',
            'skip-proxy'                  => '127.0.0.1, 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12, 100.64.0.0/10, 17.0.0.0/8, localhost, *.local, *.crashlytics.com',
            'external-controller-access'  => 'China@0.0.0.0:8233',
            'allow-wifi-access'           => 'true',
            'enhanced-mode-by-rule'       => 'false',
            'exclude-simple-hostnames'    => 'true',
            'show-error-page-for-reject'  => 'true',
            'ipv6'                        => 'true',
            'replica'                     => 'false',
            'http-listen'                 => '0.0.0.0:8234',
            'socks5-listen'               => '0.0.0.0:8235',
            'wifi-access-http-port'       => 6152,
            'wifi-access-socks5-port'     => 6153,
            'internet-test-url'           => 'http://baidu.com',
            'proxy-test-url'              => 'http://www.qualcomm.cn/generate_204',
            'test-timeout'                => 3
        ],
        'Proxy' => [
            '🚀直接连接 = direct'
        ],
        'ProxyGroup' => [
            [
                'name' => '🔰国外流量',
                'type' => 'select',
                'content' => [
                    'regex' => '(.*)',
                    'right-proxies' => [
                        '🚀直接连接'
                    ],
                ]
            ],
            [
                'name' => '⚓️其他流量',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量',
                        '🚀直接连接'
                    ]
                ]
            ],
            [
                'name' => '✈️Telegram',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬Youtube',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬Netflix',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬哔哩哔哩',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🚀直接连接'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬国外媒体',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🍎苹果服务',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🚀直接连接',
                        '🔰国外流量'
                    ]
                ]
            ]
        ],
        'Rule' => [
            'source' => 'surge/default.tpl'
        ]
    ]
];

/**
 * Surge 2.x 版本的配置文件定义
 */
$_ENV['Surge2_Profiles'] = [
    'default' => [
        'Checks' => [],
        'General' => [
            'loglevel'                   => 'notify',
            'ipv6'                       => 'true',
            'replica'                    => 'false',
            'dns-server'                 => 'system, 119.29.29.29, 223.5.5.5',
            'skip-proxy'                 => '127.0.0.1, 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12, 100.64.0.0/10, 17.0.0.0/8, localhost, *.local, *.crashlytics.com',
            'bypass-system'              => 'true',
            'allow-wifi-access'          => 'true',
            'external-controller-access' => 'ChinaX@0.0.0.0:8233'
        ],
        'Proxy' => [
            '🚀直接连接 = direct'
        ],
        'ProxyGroup' => [
            [
                'name' => '🔰国外流量',
                'type' => 'select',
                'content' => [
                    'regex' => '(.*)',
                    'right-proxies' => [
                        '🚀直接连接'
                    ],
                ]
            ],
            [
                'name' => '⚓️其他流量',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量',
                        '🚀直接连接'
                    ]
                ]
            ],
            [
                'name' => '✈️Telegram',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬Youtube',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬Netflix',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬哔哩哔哩',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🚀直接连接'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬国外媒体',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🍎苹果服务',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🚀直接连接',
                        '🔰国外流量'
                    ]
                ]
            ]
        ],
        'Rule' => [
            'source' => 'surge2/default.tpl'
        ]
    ]
];

/**
 * Clash 配置文件定义
 */
$_ENV['Clash_Profiles'] = [
    'default' => [
        'Checks' => [],
        'General' => [
            'port'                => 7890,
            'socks-port'          => 7891,
            'redir-port'          => 7892,
            'allow-lan'           => false,
            'mode'                => 'rule',
            'log-level'           => 'silent',
            'external-controller' => '0.0.0.0:9090',
            'secret'              => ''
        ],
        'DNS' => [
            'enable'              => true,
            'ipv6'                => false,
            'listen'              => '0.0.0.0:53',
            'enhanced-mode'       => 'fake-ip',
            'fake-ip-range'       => '198.18.0.1/16',
            'nameserver'=>[
                '114.114.114.114',
                'tcp://223.5.5.5'
            ],
            'fallback'=>[
                'tls://223.5.5.5:853',
                'https://223.5.5.5/dns-query'
            ],
            'fallback-filter'=>[
                'geoip'=> true,
                'ipcidr'=>[
                    '240.0.0.0/4'
                ]
            ]
        ],
        'Proxy' => [],
        'ProxyGroup' => [
            [
                'name' => '🔰国外流量',
                'type' => 'select',
                'content' => [
                    'regex' => '(.*)',
                    'right-proxies' => [
                        '🚀直接连接'
                    ],
                ]
            ],
            [
                'name' => '⚓️其他流量',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量',
                        '🚀直接连接'
                    ]
                ]
            ],
            [
                'name' => '✈️Telegram',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬Youtube',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬Netflix',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬哔哩哔哩',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🚀直接连接'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬国外媒体',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🍎苹果服务',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🚀直接连接',
                        '🔰国外流量'
                    ]
                ]
            ],
            [
                'name' => '🚀直接连接',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        'DIRECT'
                    ]
                ]
            ]
        ],
        'Rule' => [
            'source' => 'clash/default.tpl'
        ]
    ]
];

/**
 * Surfboard 配置文件定义
 */
$_ENV['Surfboard_Profiles'] = [
    'default' => [
        'Checks' => [],
        'General' => [
            'loglevel'   => 'notify',
            'dns-server' => 'system, 119.29.29.29, 223.5.5.5',
            'skip-proxy' => '127.0.0.1, 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12, 100.64.0.0/10, 17.0.0.0/8, localhost, *.local, *.crashlytics.com',
        ],
        'Proxy' => [
            '🚀直接连接 = direct'
        ],
        'ProxyGroup' => [
            [
                'name' => '🔰国外流量',
                'type' => 'select',
                'content' => [
                    'regex' => '(.*)',
                    'right-proxies' => [
                        '🚀直接连接'
                    ],
                ]
            ],
            [
                'name' => '⚓️其他流量',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量',
                        '🚀直接连接'
                    ]
                ]
            ],
            [
                'name' => '✈️Telegram',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬Youtube',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬Netflix',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬哔哩哔哩',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🚀直接连接'
                    ],
                    'regex' => '(.*)',
                ]
            ],
            [
                'name' => '🎬国外媒体',
                'type' => 'select',
                'content' => [
                    'left-proxies' => [
                        '🔰国外流量'
                    ],
                    'regex' => '(.*)',
                ]
            ]
        ],
        'Rule' => [
            'source' => 'surfboard/default.tpl'
        ]
    ]
];
