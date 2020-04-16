<?php

namespace App\Utils;

use App\Models\{
    User,
    Node,
    Relay
};
use App\Services\Config;
use DateTime;

class Tools
{
    /**
     * 根据流量值自动转换单位输出
     */
    public static function flowAutoShow($value = 0)
    {
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = $gb * 1024;
        $pb = $tb * 1024;
        if (abs($value) > $pb) {
            return round($value / $pb, 2) . 'PB';
        }

        if (abs($value) > $tb) {
            return round($value / $tb, 2) . 'TB';
        }

        if (abs($value) > $gb) {
            return round($value / $gb, 2) . 'GB';
        }

        if (abs($value) > $mb) {
            return round($value / $mb, 2) . 'MB';
        }

        if (abs($value) > $kb) {
            return round($value / $kb, 2) . 'KB';
        }

        return round($value, 2) . 'B';
    }

    /**
     * 根据含单位的流量值转换 B 输出
     */
    public static function flowAutoShowZ($Value)
    {
        $number = substr($Value, 0, strlen($Value) - 2);
        if (!is_numeric($number)) return null;
        $unit = strtoupper(substr($Value, -2));
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = $gb * 1024;
        $pb = $tb * 1024;
        switch ($unit) {
            case 'B':
                $number = round($number, 2);
                break;
            case 'KB':
                $number = round($number * $kb, 2);
                break;
            case 'MB':
                $number = round($number * $mb, 2);
                break;
            case 'GB':
                $number = round($number * $gb, 2);
                break;
            case 'TB':
                $number = round($number * $tb, 2);
                break;
            case 'PB':
                $number = round($number * $pb, 2);
                break;
            default:
                return null;
                break;
        }
        return $number;
    }

    //虽然名字是toMB，但是实际上功能是from MB to B
    public static function toMB($traffic)
    {
        $mb = 1048576;
        return $traffic * $mb;
    }

    //虽然名字是toGB，但是实际上功能是from GB to B
    public static function toGB($traffic)
    {
        $gb = 1048576 * 1024;
        return $traffic * $gb;
    }

    /**
     * @param $traffic
     * @return float
     */
    public static function flowToGB($traffic)
    {
        $gb = 1048576 * 1024;
        return $traffic / $gb;
    }

    /**
     * @param $traffic
     * @return float
     */
    public static function flowToMB($traffic)
    {
        $gb = 1048576;
        return $traffic / $gb;
    }

    //获取随机字符串

    public static function genRandomNum($length = 8)
    {
        // 来自Miku的 6位随机数 注册验证码 生成方案
        $chars = '0123456789';
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $char;
    }

    public static function genRandomChar($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $char;
    }

    public static function genToken()
    {
        return self::genRandomChar(64);
    }

    public static function is_ip($a)
    {
        return preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $a);
    }

    // Unix time to Date Time
    public static function toDateTime($time)
    {
        return date('Y-m-d H:i:s', $time);
    }

    public static function secondsToTime($seconds)
    {
        $dtF = new DateTime('@0');
        $dtT = new DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a 天, %h 小时, %i 分 + %s 秒');
    }

    public static function genSID()
    {
        $unid = uniqid($_ENV['key'], true);
        return Hash::sha256WithSalt($unid);
    }

    public static function genUUID()
    {
        // @TODO
        return self::genSID();
    }

    public static function getLastPort()
    {
        $user = User::orderBy('id', 'desc')->first();
        if ($user == null) {
            return 1024; // @todo
        }
        return $user->port;
    }

    public static function getAvPort()
    {
        //检索User数据表现有port
        $det = User::pluck('port')->toArray();
        $port = array_diff(range($_ENV['min_port'], $_ENV['max_port']), $det);
        shuffle($port);
        return $port[0];
    }

    public static function base64_url_encode($input)
    {
        return strtr(base64_encode($input), array('+' => '-', '/' => '_', '=' => ''));
    }

    public static function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function getDir($dir)
    {
        $dirArray[] = null;
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..' && !strpos($file, '.')) {
                    $dirArray[$i] = $file;
                    $i++;
                }
            }
            closedir($handle);
        }
        return $dirArray;
    }

    public static function is_validate($str)
    {
        $pattern = "/[^A-Za-z0-9\-_\.]/";
        return !preg_match($pattern, $str);
    }

    public static function is_relay_rule_avaliable($rule, $ruleset, $node_id)
    {
        $cur_id = $rule->id;

        foreach ($ruleset as $single_rule) {
            if (($rule->port == $single_rule->port || $single_rule->port == 0) && ($node_id == $single_rule->source_node_id || $single_rule->source_node_id == 0) && (($rule->id != $single_rule->id && $rule->priority < $single_rule->priority) || ($rule->id < $single_rule->id && $rule->priority == $single_rule->priority))) {
                $cur_id = $single_rule->id;
            }
        }

        return !($cur_id != $rule->id);
    }

    public static function pick_out_relay_rule($relay_node_id, $port, $ruleset)
    {

        /*
        for id in self.relay_rule_list:
            if ((self.relay_rule_list[id]['user_id'] == user_id or self.relay_rule_list[id]['user_id'] == 0) or row['is_multi_user'] != 0) and (self.relay_rule_list[id]['port'] == 0 or self.relay_rule_list[id]['port'] == port):
                has_higher_priority = False
                for priority_id in self.relay_rule_list:
                    if ((self.relay_rule_list[priority_id]['priority'] > self.relay_rule_list[id]['priority'] and self.relay_rule_list[id]['id'] != self.relay_rule_list[priority_id]['id']) or (self.relay_rule_list[priority_id]['priority'] == self.relay_rule_list[id]['priority'] and self.relay_rule_list[id]['id'] > self.relay_rule_list[priority_id]['id'])) and (self.relay_rule_list[id]['user_id'] == self.relay_rule_list[priority_id]['user_id'] or self.relay_rule_list[priority_id]['user_id'] == 0) and (self.relay_rule_list[id]['port'] == self.relay_rule_list[priority_id]['port'] or self.relay_rule_list[priority_id]['port'] == 0):
                        has_higher_priority = True
                        continue

                if has_higher_priority:
                    continue

            temp_relay_rules[id] = self.relay_rule_list[id]
        */

        $match_rule = null;

        foreach ($ruleset as $single_rule) {
            if (($single_rule->port == $port || $single_rule->port == 0) && ($single_rule->source_node_id == 0 || $single_rule->source_node_id == $relay_node_id)) {
                $has_higher_priority = false;
                foreach ($ruleset as $priority_rule) {
                    if (($priority_rule->port == $port || $priority_rule->port == 0) && ($priority_rule->source_node_id == 0 || $priority_rule->source_node_id == $relay_node_id)) {
                        if (($priority_rule->priority > $single_rule->priority && $priority_rule->id != $single_rule->id) || ($priority_rule->priority == $single_rule->priority && $priority_rule->id < $single_rule->id)) {
                            $has_higher_priority = true;
                            continue;
                        }
                    }
                }

                if ($has_higher_priority) {
                    continue;
                }

                $match_rule = $single_rule;
            }
        }

        if (($match_rule != null) && $match_rule->dist_node_id == -1) {
            return null;
        }

        return $match_rule;
    }

    public static function get_middle_text($origin_text, $begin_text, $end_text)
    {
        $begin_pos = strpos($origin_text, $begin_text);
        if ($begin_pos == false) {
            return null;
        }

        $end_pos = strpos($origin_text, $end_text, $begin_pos + strlen($begin_text));
        if ($end_pos == false) {
            return null;
        }

        return substr($origin_text, $begin_pos + strlen($begin_text), $end_pos - $begin_pos - strlen($begin_text));
    }

    public static function is_param_validate($type, $str)
    {
        $list = Config::getSupportParam($type);
        if (in_array($str, $list)) {
            return true;
        }
        return false;
    }

    public static function is_protocol_relay($user)
    {
        return true;

        $relay_able_list = Config::getSupportParam('relay_able_protocol');

        return in_array($user->protocol, $relay_able_list) || $_ENV['relay_insecure_mode'] == true;
    }

    public static function has_conflict_rule($input_rule, $ruleset, $edit_rule_id = 0, $origin_node_id = 0, $user_id = 0)
    {
        foreach ($ruleset as $rule) {
            if (($rule->source_node_id == $input_rule->dist_node_id) && (($rule->port == $input_rule->port || $input_rule->port == 0) || $rule->port == 0)) {
                if ($rule->dist_node_id == $origin_node_id && $rule->id != $edit_rule_id) {
                    return $rule->id;
                }

                //递归处理这个节点
                $maybe_rule_id = self::has_conflict_rule($rule, $ruleset, $edit_rule_id, $origin_node_id, $rule->user_id);
                if ($maybe_rule_id != 0) {
                    return $maybe_rule_id;
                }
            }
        }

        if (($input_rule->id == $edit_rule_id || $edit_rule_id == 0) && $input_rule->dist_node_id != -1) {
            $dist_node = Node::find($input_rule->dist_node_id);
            if ($input_rule->source_node_id == 0 && ($dist_node->sort == 10 || $dist_node->sort == 12)) {
                return -1;
            }

            if ($input_rule->dist_node_id == $input_rule->source_node_id) {
                return -1;
            }
        }

        return 0;
    }

    public static function insertPathRule($single_rule, $pathset, $port)
    {
        /* path
          path pathtext
          begin_node_id id
          end_node id
          port port
        */

        if ($single_rule->dist_node_id == -1) {
            return $pathset;
        }

        foreach ($pathset as &$path) {
            if ($path->port == $port) {
                if ($single_rule->dist_node_id == $path->begin_node->id) {
                    $path->begin_node = $single_rule->Source_Node();
                    if ($path->begin_node->isNodeAccessable() == false) {
                        $path->path = '<font color="#FF0000">' . $single_rule->Source_Node()->name . '</font>' . ' → ' . $path->path;
                        $path->status = '阻断';
                    } else {
                        $path->path = $single_rule->Source_Node()->name . ' → ' . $path->path;
                        $path->status = '通畅';
                    }
                    return $pathset;
                }

                if ($path->end_node->id == $single_rule->source_node_id) {
                    $path->end_node = $single_rule->Dist_Node();
                    if ($path->end_node->isNodeAccessable() == false) {
                        $path->path = $path->path . ' → ' . '<font color="#FF0000">' . $single_rule->Dist_Node()->name . '</font>';
                        $path->status = '阻断';
                    } else {
                        $path->path = $path->path . ' → ' . $single_rule->Dist_Node()->name;
                    }
                    return $pathset;
                }
            }
        }

        $new_path = new \stdClass();
        $new_path->begin_node = $single_rule->Source_Node();
        if ($new_path->begin_node->isNodeAccessable() == false) {
            $new_path->path = '<font color="#FF0000">' . $single_rule->Source_Node()->name . '</font>';
            $new_path->status = '阻断';
        } else {
            $new_path->path = $single_rule->Source_Node()->name;
            $new_path->status = '通畅';
        }

        $new_path->end_node = $single_rule->Dist_Node();
        if ($new_path->end_node->isNodeAccessable() == false) {
            $new_path->path .= ' -> ' . '<font color="#FF0000">' . $single_rule->Dist_Node()->name . '</font>';
            $new_path->status = '阻断';
        } else {
            $new_path->path .= ' -> ' . $single_rule->Dist_Node()->name;
        }

        $new_path->port = $port;
        $pathset->append($new_path);

        return $pathset;
    }

    /**
     * Filter key in `App\Models\Model` object
     *
     * @param \App\Models\Model $object
     * @param array             $filter_array
     *
     * @return \App\Models\Model
     */
    public static function keyFilter($object, $filter_array)
    {
        foreach ($object->toArray() as $key => $value) {
            if (!in_array($key, $filter_array)) {
                unset($object->$key);
            }
        }
        return $object;
    }

    public static function relayRulePortCheck($rules)
    {
        $res = [];
        foreach ($rules as $value) {
            $res[$value->port][] = $value->port;
        }
        return count($res) == count($rules);
    }

    public static function getRelayNodeIp($source_node, $dist_node)
    {
        $dist_ip_str = $dist_node->node_ip;
        $dist_ip_array = explode(',', $dist_ip_str);
        $return_ip = null;
        foreach ($dist_ip_array as $single_dist_ip_str) {
            $child1_array = explode('#', $single_dist_ip_str);
            if ($child1_array[0] == $single_dist_ip_str) {
                $return_ip = $child1_array[0];
            } elseif (isset($child1_array[1])) {
                $node_id_array = explode('|', $child1_array[1]);
                if (in_array($source_node->id, $node_id_array)) {
                    $return_ip = $child1_array[0];
                }
            }
        }

        return $return_ip;
    }

    public static function updateRelayRuleIp($dist_node)
    {
        $rules = Relay::where('dist_node_id', $dist_node->id)->get();

        foreach ($rules as $rule) {
            $source_node = Node::where('id', $rule->source_node_id)->first();

            $rule->dist_ip = self::getRelayNodeIp($source_node, $dist_node);
            $rule->save();
        }
    }

    public static function checkNoneProtocol($user)
    {
        return !($user->method == 'none' && !in_array($user->protocol, Config::getSupportParam('allow_none_protocol')));
    }

    public static function getRealIp($rawIp)
    {
        return str_replace('::ffff:', '', $rawIp);
    }

    public static function isInt($str)
    {
        if ($str[0] == '-') {
            $str = substr($str, 1);
        }

        return ctype_digit($str);
    }

    public static function v2Array($node)
    {
        $server = explode(';', $node);
        $item = [
            'host' => '',
            'path' => '',
            'tls' => '',
            'verify_cert' => true
        ];
        $item['add'] = $server[0];
        if ($server[1] == '0' || $server[1] == '') {
            $item['port'] = 443;
        } else {
            $item['port'] = (int) $server[1];
        }
        $item['aid'] = (int) $server[2];
        $item['net'] = 'tcp';
        $item['headerType'] = 'none';
        if (count($server) >= 4) {
            $item['net'] = $server[3];
            if ($item['net'] == 'ws') {
                $item['path'] = '/';
            } elseif ($item['net'] == 'tls') {
                $item['tls'] = 'tls';
            }
        }
        if (count($server) >= 5) {
            if (in_array($item['net'], array('kcp', 'http', 'mkcp'))) {
                $item['headerType'] = $server[4];
            } elseif ($server[4] == 'ws') {
                $item['net'] = 'ws';
            } elseif ($server[4] == 'tls') {
                $item['tls'] = 'tls';
            }
        }
        if (count($server) >= 6 && $server[5] != '') {
            $item = array_merge($item, URL::parse_args($server[5]));
            if (array_key_exists('server', $item)) {
                $item['add'] = $item['server'];
                unset($item['server']);
            }
            if (array_key_exists('relayserver', $item)) {
                $item['localserver'] = $item['add'];
                $item['add'] = $item['relayserver'];
                unset($item['relayserver']);
                if ($item['tls'] == 'tls') {
                    $item['verify_cert'] = false;
                }
            }
            if (array_key_exists('outside_port', $item)) {
                $item['port'] = (int) $item['outside_port'];
                unset($item['outside_port']);
            }
            if (isset($item['inside_port'])) {
                unset($item['inside_port']);
            }
        }
        return $item;
    }

    public static function checkTls($node)
    {
        $server = self::v2Array($node);
        return !($server['tls'] == 'tls' && self::is_ip($server['add']));
    }

    public static function ssv2Array($node)
    {
        $server = explode(';', $node);
        $item = [
            'host' => 'microsoft.com',
            'path' => '',
            'net' => 'ws',
            'tls' => ''
        ];
        $item['add'] = $server[0];
        if ($server[1] == '0' || $server[1] == '') {
            $item['port'] = 443;
        } else {
            $item['port'] = (int) $server[1];
        }
        if (count($server) >= 4) {
            $item['net'] = $server[3];
            if ($item['net'] == 'ws') {
                $item['path'] = '/';
            } elseif ($item['net'] == 'tls') {
                $item['tls'] = 'tls';
            }
        }
        if (count($server) >= 5 && $server[4] == 'ws') {
            $item['net'] = 'ws';
        } elseif (count($server) >= 5 && $server[4] == 'tls') {
            $item['tls'] = 'tls';
        }
        if (count($server) >= 6) {
            $item = array_merge($item, URL::parse_args($server[5]));
            if (array_key_exists('server', $item)) {
                $item['add'] = $item['server'];
                unset($item['server']);
            }
            if (array_key_exists('relayserver', $item)) {
                $item['add'] = $item['relayserver'];
                unset($item['relayserver']);
            }
            if (array_key_exists('outside_port', $item)) {
                $item['port'] = (int) $item['outside_port'];
                unset($item['outside_port']);
            }
        }
        if ($item['net'] == 'obfs') {
            if (stripos($server[4], 'http') !== false) {
                $item['obfs'] = 'simple_obfs_http';
            }
            if (stripos($server[4], 'tls') !== false) {
                $item['obfs'] = 'simple_obfs_tls';
            }
        }
        return $item;
    }

    public static function OutPort($server, $node_name, $mu_port)
    {
        $node_server = explode(';', $server);
        $node_port = $mu_port;

        if (isset($node_server[1])) {
            if (strpos($node_server[1], 'port') !== false) {
                $item = URL::parse_args($node_server[1]);
                if (strpos($item['port'], '#') !== false) { // 端口偏移，指定端口，格式：8.8.8.8;port=80#1080
                    if (strpos($item['port'], '+') !== false) { // 多个单端口节点，格式：8.8.8.8;port=80#1080+443#8443
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
                } else { // 端口偏移，偏移端口，格式：8.8.8.8;port=1000 or 8.8.8.8;port=-1000
                    $node_port = ($mu_port + (int) $item['port']);
                }
            }
        }

        return [
            'name' => ($_ENV['disable_sub_mu_port'] ? $node_name : $node_name . ' - ' . $node_port . ' 单端口'),
            'address' => $node_server[0],
            'port' => $node_port
        ];
    }

    public static function get_MuOutPortArray($server)
    {
        $type = 0; //偏移
        $port = []; //指定
        $node_server = explode(';', $server);
        if (isset($node_server[1])) {
            if (strpos($node_server[1], 'port') !== false) {
                $item = URL::parse_args($node_server[1]);
                if (strpos($item['port'], '#') !== false) {
                    if (strpos($item['port'], '+') !== false) {
                        $args_explode = explode('+', $item['port']);
                        foreach ($args_explode as $arg) {
                            $port[substr($arg, 0, strpos($arg, '#'))] = (int) substr($arg, strpos($arg, '#') + 1);
                        }
                    } else {
                        $port[substr($item['port'], 0, strpos($item['port'], '#'))] = (int) substr($item['port'], strpos($item['port'], '#') + 1);
                    }
                } else {
                    $type = (int) $item['port'];
                }
            }
        }

        return [
            'type' => $type,
            'port' => $port
        ];
    }

    // 请将冷门的国家或地区放置在上方，热门的中继起源放置在下方
    // 以便于兼容如：【上海 -> 美国】等节点名称
    private static $emoji = [
        "🇦🇷" => [
            "阿根廷"
        ],
        "🇦🇹" => [
            "奥地利",
            "维也纳"
        ],
        "🇦🇺" => [
            "澳大利亚",
            "悉尼"
        ],
        "🇧🇷" => [
            "巴西",
            "圣保罗"
        ],
        "🇨🇦" => [
            "加拿大",
            "蒙特利尔",
            "温哥华"
        ],
        "🇨🇭" => [
            "瑞士",
            "苏黎世"
        ],
        "🇩🇪" => [
            "德国",
            "法兰克福"
        ],
        "🇫🇮" => [
            "芬兰",
            "赫尔辛基"
        ],
        "🇫🇷" => [
            "法国",
            "巴黎"
        ],
        "🇬🇧" => [
            "英国",
            "伦敦"
        ],
        "🇮🇩" => [
            "印尼",
            "印度尼西亚",
            "雅加达"
        ],
        "🇮🇪" => [
            "爱尔兰",
            "都柏林"
        ],
        "🇮🇳" => [
            "印度",
            "孟买"
        ],
        "🇮🇹" => [
            "意大利",
            "米兰"
        ],
        "🇰🇵" => [
            "朝鲜"
        ],
        "🇲🇾" => [
            "马来西亚"
        ],
        "🇳🇱" => [
            "荷兰",
            "阿姆斯特丹"
        ],
        "🇵🇭" => [
            "菲律宾"
        ],
        "🇷🇴" => [
            "罗马尼亚"
        ],
        "🇷🇺" => [
            "俄罗斯",
            "伯力",
            "莫斯科",
            "圣彼得堡",
            "西伯利亚",
            "新西伯利亚"
        ],
        "🇸🇬" => [
            "新加坡"
        ],
        "🇹🇭" => [
            "泰国",
            "曼谷"
        ],
        "🇹🇷" => [
            "土耳其",
            "伊斯坦布尔"
        ],
        "🇺🇲" => [
            "美国",
            "波特兰",
            "俄勒冈",
            "凤凰城",
            "费利蒙",
            "硅谷",
            "拉斯维加斯",
            "洛杉矶",
            "圣克拉拉",
            "西雅图",
            "芝加哥",
            "沪美"
        ],
        "🇻🇳" => [
            "越南"
        ],
        "🇿🇦" => [
            "南非"
        ],
        "🇰🇷" => [
            "韩国",
            "首尔"
        ],
        "🇲🇴" => [
            "澳门"
        ],
        "🇯🇵" => [
            "日本",
            "东京",
            "大阪",
            "埼玉",
            "沪日"
        ],
        "🇹🇼" => [
            "台湾",
            "台北",
            "台中"
        ],
        "🇭🇰" => [
            "香港",
            "深港"
        ],
        "🇨🇳" => [
            "中国",
            "江苏",
            "北京",
            "上海",
            "深圳",
            "杭州",
            "徐州",
            "宁波",
            "镇江"
        ]
    ];

    public static function addEmoji($Name)
    {
        $done = [
            'index' => -1,
            'emoji' => ''
        ];
        foreach (self::$emoji as $key => $value) {
            foreach ($value as $item) {
                $index = strpos($Name, $item);
                if ($index !== false) {
                    $done['index'] = $index;
                    $done['emoji'] = $key;
                    continue 2;
                }
            }
        }
        return ($done['index'] == -1
            ? $Name
            : ($done['emoji'] . ' ' . $Name));
    }

    /**
     * Add files and sub-directories in a folder to zip file.
     *
     * @param string     $folder
     * @param ZipArchive $zipFile
     * @param int        $exclusiveLength Number of text to be exclusived from the file path.
     */
    public static function folderToZip($folder, &$zipFile, $exclusiveLength)
    {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    /**
     * 清空文件夹
     *
     * @param string $dirName
     */
    public static function delDirAndFile($dirPath)
    {
        if ($handle = opendir($dirPath)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dirPath . '/' . $item)) {
                        self::delDirAndFile($dirPath . '/' . $item);
                    } else {
                        unlink($dirPath . '/' . $item);
                    }
                }
            }
            closedir($handle);
        }
    }
}
