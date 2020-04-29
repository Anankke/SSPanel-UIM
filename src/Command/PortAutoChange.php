<?php

/**
 * [实验性] 检测到端口被墙则自动更换端口
 *
 * // 实验性，可能会有惊喜，请确保拥有承受能力
 * // 请通过 Crontab 启动，间隔建议 60 分钟以上
 */

namespace App\Command;

use App\Models\{
    User,
    Node
};
use App\Utils\URL;

class PortAutoChange extends Command
{
    public $description = '├─=: php xcat PortAutoChange - 端口被墙则自动更换端口' . PHP_EOL;

    /**
     *  配置
     */
    private $Config = [
        // 取端口范围最小值，新的端口将是之间的随机数
        'port_min' => 23333,

        // 取端口范围最大值，新的端口将是之间的随机数
        'port_max' => 65535,

        // 当某个使用的端口的被墙节点数量超过该端口使用总数量的百分比
        // 超过该百分比时，直接更换单端口多用户节点(sort=9)和承载用户的端口
        // 未超过时，对已被墙该端口的节点进行端口偏移
        'mu_node_port_change_percent' => 60,

        // 例外的节点，填写节点 ID，英文逗号分隔
        // 此处提供的节点将不会进行端口更换
        // 即使当原先的承载端口被更换时，也会将例外节点的端口偏移回去
        'exception_node_id' => array(),
    ];

    public function boot()
    {
        $gfw_port_nodes = [];
        $nodes = Node::where(
            static function ($query) {
                $query->where('sort', 0)
                    ->orwhere('sort', 10);
            }
        )
            ->where('mu_only', '>=', '0')
            ->where('type', '1')
            ->get();
        foreach ($nodes as $node) {
            $mu_nodes = Node::where('sort', 9)->where('type', '1')
                ->where(
                    static function ($query) use ($node) {
                        $query->Where('node_group', '=', $node->node_group)
                            ->orWhere('node_group', '=', 0);
                    }
                )
                ->where('node_class', '<=', $node->node_class)
                ->get();
            foreach ($mu_nodes as $mu_node) {
                $mu_user = User::where('enable', 1)->where('is_multi_user', '<>', 0)->where('port', '=', $mu_node->server)->first();
                if ($mu_user == null) continue;
                $port = $this->OutPort($node->server, $mu_node->server);
                $api_url = $_ENV['detect_gfw_url'];
                $api_url = str_replace(
                    array('{ip}', '{port}'),
                    array($node->node_ip, $port),
                    $api_url
                );
                $result_tcping = $this->DetectPort($api_url);
                if ($result_tcping) continue;
                $gfw_port_nodes[$mu_node->server][] = $node->id;
                echo ('#' . $node->id . ' --- ' . $node->name . ' --- ' . $port . ' 端口不通' . PHP_EOL);
            }
        }
        foreach ($gfw_port_nodes as $port => $array) {
            $mu_node = Node::where('sort', 9)->where('server', '=', $port)->where('type', '1')->first();
            $mu_user = User::where('enable', 1)->where('is_multi_user', '<>', 0)->where('port', '=', $port)->first();
            if ($mu_node == null || $mu_user == null) continue;
            $mu_port_nodes = Node::where(
                static function ($query) {
                    $query->where('sort', 0)
                        ->orwhere('sort', 10);
                }
            )
                ->where(
                    static function ($query) use ($mu_node) {
                        if ($mu_node->node_group == 0) {
                            $query->where('node_group', '>=', 0);
                        } else {
                            $query->where('node_group', '=', $mu_node->node_group);
                        }
                    }
                )
                ->where('node_class', '>=', $mu_node->node_class)
                ->where('mu_only', '>=', '0')
                ->where('type', '1')
                ->get();
            for ($i = 0; $i <= 10; $i++) {
                $new_port = rand((int) $this->Config['port_min'], (int) $this->Config['port_max']);
                if (Node::where('sort', 9)->where('server', '=', $new_port)->first() == null && User::where('port', '=', $new_port)->first() == null) {
                    break;
                }
            }
            $number = (count($array) / count($mu_port_nodes)) * 100;
            if ($number >= $this->Config['mu_node_port_change_percent']) {
                echo ('超过百分比：' . $number . '%' . PHP_EOL);
                echo ('#' . $mu_node->id . ' - 单端口承载节点 - ' . $mu_node->name . ' - 更换了新的端口 ' . $new_port . PHP_EOL);
                $mu_node->server = $new_port;
                $mu_node->save();
                echo ('#' . $mu_user->id . ' - 单端口承载用户 - ' . $mu_user->user_name . ' - 更换了新的端口 ' . $new_port . PHP_EOL);
                $mu_user->port = $new_port;
                $mu_user->save();
                foreach ($mu_port_nodes as $mu_port_node) {
                    $node_port = $this->OutPort($mu_port_node->server, $port);
                    if (in_array($mu_port_node->id, $array) && !in_array($mu_port_node->id, $this->Config['exception_node_id'])) {
                        if ($node_port != $port) {
                            if ($node_port == $new_port) {
                                if (strpos($mu_port_node->server, ($port . '#')) !== false) {
                                    for ($i = 0; $i <= 10; $i++) {
                                        $new_mu_node_port = rand((int) $this->Config['port_min'], (int) $this->Config['port_max']);
                                        if ($new_mu_node_port != $new_port && Node::where('port', '=', $new_mu_node_port)->first() == null && User::where('port', '=', $new_mu_node_port)->first() == null) {
                                            break;
                                        }
                                    }
                                    $mu_port_node->server = str_replace(($port . '#' . $node_port), ($new_port . '#' . $new_mu_node_port), $mu_port_node->server);
                                    echo ('#' . $mu_port_node->id . ' - 节点 - ' . $mu_port_node->name . ' - 端口从 ' . $node_port . ' 偏移到了新的端口 ' . $new_mu_node_port . PHP_EOL);
                                }
                            } else {
                                if (strpos($mu_port_node->server, ($port . '#')) !== false) {
                                    $mu_port_node->server = str_replace(('+' . $port . '#' . $node_port), '', $mu_port_node->server);
                                    $mu_port_node->server = str_replace(($port . '#' . $node_port . '+'), '', $mu_port_node->server);
                                    $mu_port_node->server = str_replace(($port . '#' . $node_port), '', $mu_port_node->server);
                                    echo ('#' . $mu_port_node->id . ' - 节点 - ' . $mu_port_node->name . ' - 端口从 ' . $node_port . ' 偏移到了新的端口 ' . $new_port . PHP_EOL);
                                }
                            }
                        }
                    } else {
                        if ($node_port == $port) {
                            if (strpos($mu_port_node->server, ';') !== false) {
                                if (strpos($mu_port_node->server, 'port=') !== false) {
                                    $mu_port_node->server = str_replace('port=', ('port=' . $new_port . '#' . $port . '+'), $mu_port_node->server);
                                } else {
                                    $mu_port_node->server = ($mu_port_node->server . ';port=' . $new_port . '#' . $port);
                                }
                            } else {
                                $mu_port_node->server = ($mu_port_node->server . ';port=' . $new_port . '#' . $port);
                            }
                        } else {
                            if (strpos($mu_port_node->server, ($port . '#')) !== false) {
                                $mu_port_node->server = str_replace(($port . '#'), ($new_port . '#'), $mu_port_node->server);
                            }
                        }
                        echo ('#' . $mu_port_node->id . ' - 节点 - ' . $mu_port_node->name . ' - 由于端口未被墙或例外设置，已将端口偏移回原端口 ' . $node_port . PHP_EOL);
                    }
                    $mu_port_node->save();
                }
            } else {
                foreach ($array as $node_id) {
                    if (in_array($node_id, $this->Config['exception_node_id'])) continue;
                    $node = Node::find($node_id);
                    $node_port = $this->OutPort($node->server, $port);
                    if ($node_port != $port) {
                        if (strpos($node->server, ('#' . $node_port)) !== false) {
                            echo ('#' . $node->id . ' - 节点 - ' . $node->name . ' - 端口从' . $node_port . '偏移到了新的端口 ' . $new_port . PHP_EOL);
                            $node->server = str_replace(('#' . $node_port), ('#' . $new_port), $node->server);
                        }
                    } else {
                        if (strpos($node->server, ';') !== false) {
                            if (strpos($node->server, 'port=') !== false) {
                                $node->server = str_replace('port=', ('port=' . $port . '#' . $new_port . '+'), $node->server);
                            } else {
                                $node->server = ($node->server . ';port=' . $port . '#' . $new_port);
                            }
                        } else {
                            $node->server = ($node->server . ';port=' . $port . '#' . $new_port);
                        }
                        echo ('#' . $node->id . ' - 节点 - ' . $node->name . ' - 端口从' . $node_port . '偏移到了新的端口 ' . $new_port . PHP_EOL);
                    }
                    $node->save();
                }
            }
        }
    }

    public function OutPort($server, $mu_port)
    {
        $node_port = $mu_port;
        if (strpos($server, ';') !== false) {
            $node_server = explode(';', $server);
            if (strpos($node_server[1], 'port') !== false) {
                $item = URL::parse_args($node_server[1]);
                if (strpos($item['port'], '#') !== false) {
                    if (strpos($item['port'], '+') !== false) {
                        $args_explode = explode('+', $item['port']);
                        foreach ($args_explode as $arg) {
                            if ((int) substr($arg, 0, strpos($arg, '#')) == $mu_port) {
                                $node_port = (int) substr($arg, strpos($arg, '#') + 1);
                            }
                        }
                    } else {
                        if ((int) substr($item['port'], 0, strpos($item['port'], '#')) == $mu_port) {
                            $node_port = (int) substr($item['port'], strpos($item['port'], '#') + 1);
                        }
                    }
                } else {
                    $node_port = ($mu_port + (int) $item['port']);
                }
            }
        }

        return $node_port;
    }

    public function DetectPort($api_url)
    {
        $result_tcping = false;
        $detect_time = $_ENV['detect_gfw_count'];
        for ($i = 1; $i <= $detect_time; $i++) {
            $json_tcping = json_decode(file_get_contents($api_url), true);
            if (eval('return ' . $_ENV['detect_gfw_judge'] . ';')) {
                $result_tcping = true;
                break;
            }
        }

        return $result_tcping;
    }
}
