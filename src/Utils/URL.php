<?php

namespace App\Utils;

use App\Models\{
    User,
    Node,
    Relay
};
use App\Services\Config;
use App\Controllers\{
    LinkController,
    ConfController
};

class URL
{
    /*
    * 1 SSR can
    * 2 SS can
    * 3 Both can
    */
    public static function CanMethodConnect($method)
    {
        $ss_aead_method_list = Config::getSupportParam('ss_aead_method');
        if (in_array($method, $ss_aead_method_list)) {
            return 2;
        }
        return 3;
    }

    /*
    * 1 SSR can
    * 2 SS can
    * 3 Both can
    */
    public static function CanProtocolConnect($protocol)
    {
        if ($protocol != 'origin') {
            if (strpos($protocol, '_compatible') === false) {
                return 1;
            }

            return 3;
        }
        return 3;
    }

    /*
    * 1 SSR can
    * 2 SS can
    * 3 Both can
    * 4 Both can, But ssr need set obfs to plain
    * 5 Both can, But ss need set obfs to plain
    */
    public static function CanObfsConnect($obfs)
    {
        if ($obfs != 'plain') {
            //SS obfs only
            $ss_obfs = Config::getSupportParam('ss_obfs');
            if (in_array($obfs, $ss_obfs)) {
                if (strpos($obfs, '_compatible') === false) {
                    return 2;
                }

                return 4; //SSR need origin plain
            }

            if (strpos($obfs, '_compatible') === false) {
                return 1;
            }

            return 5; //SS need plain
        }

        return 3;
    }

    public static function parse_args($origin)
    {
        // parse xxx=xxx|xxx=xxx to array(xxx => xxx, xxx => xxx)
        $args_explode = explode('|', $origin);

        $return_array = [];
        foreach ($args_explode as $arg) {
            $split_point = strpos($arg, '=');

            $return_array[substr($arg, 0, $split_point)] = substr($arg, $split_point + 1);
        }

        return $return_array;
    }

    public static function SSCanConnect(User $user, $mu_port = 0)
    {
        if ($mu_port != 0) {
            $mu_user = User::where('port', '=', $mu_port)->where('is_multi_user', '<>', 0)->first();
            if ($mu_user == null) {
                return;
            }
            return self::SSCanConnect($mu_user);
        }
        return self::CanMethodConnect($user->method) >= 2 && self::CanProtocolConnect($user->protocol) >= 2 && self::CanObfsConnect($user->obfs) >= 2;
    }

    public static function SSRCanConnect(User $user, $mu_port = 0)
    {
        if ($mu_port != 0) {
            $mu_user = User::where('port', '=', $mu_port)->where('is_multi_user', '<>', 0)->first();
            if ($mu_user == null) {
                return;
            }
            return self::SSRCanConnect($mu_user);
        }
        return self::CanMethodConnect($user->method) != 2 && self::CanProtocolConnect($user->protocol) != 2 && self::CanObfsConnect($user->obfs) != 2;
    }

    public static function getSSConnectInfo(User $user)
    {
        $new_user = clone $user;
        if (self::CanObfsConnect($new_user->obfs) == 5) {
            $new_user->obfs = 'plain';
            $new_user->obfs_param = '';
        }
        if (self::CanProtocolConnect($new_user->protocol) == 3) {
            $new_user->protocol = 'origin';
            $new_user->protocol_param = '';
        }
        $new_user->obfs = str_replace('_compatible', '', $new_user->obfs);
        $new_user->protocol = str_replace('_compatible', '', $new_user->protocol);
        return $new_user;
    }

    public static function getSSRConnectInfo(User $user)
    {
        $new_user = clone $user;
        if (self::CanObfsConnect($new_user->obfs) == 4) {
            $new_user->obfs = 'plain';
            $new_user->obfs_param = '';
        }
        $new_user->obfs = str_replace('_compatible', '', $new_user->obfs);
        $new_user->protocol = str_replace('_compatible', '', $new_user->protocol);
        return $new_user;
    }

    /**
     * 获取全部节点对象
     *
     * @param User  $user
     * @param mixed $sort  数值或数组
     * @param array $rules 节点筛选规则
     */
    public static function getNodes(User $user, $sort, array $rules = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Node::query();
        if (is_array($sort)) {
            $query->whereIn('sort', $sort);
        } else {
            $query->where('sort', $sort);
        }
        if (!$user->is_admin) {
            $group = ($user->node_group != 0 ? [0, $user->node_group] : [0]);
            $query->whereIn('node_group', $group)
                ->where('node_class', '<=', $user->class);
        }
        // 等级筛选
        if (isset($rules['content']['class']) && count($rules['content']['class']) > 0) {
            $query->whereIn('node_class', $rules['content']['class']);
        }
        if (isset($rules['content']['noclass']) && count($rules['content']['noclass']) > 0) {
            $query->whereNotIn('node_class', $rules['content']['noclass']);
        }
        // 等级筛选 end
        $nodes = $query->where('type', '1')
            ->orderBy('name')->get();

        return $nodes;
    }

    /**
     * 获取全部节点
     *
     * ```
     * $Rule = [
     *      'type'    => 'all | ss | ssr | vmess | trojan',
     *      'emoji'   => false,
     *      'is_mu'   => 1,
     *      'content' => [
     *          'noclass' => [0, 1, 2],
     *          'class'   => [0, 1, 2],
     *          'regex'   => '.*香港.*HKBN.*',
     *      ]
     * ]
     * ```
     *
     * @param User  $user 用户
     * @param array $Rule 节点筛选规则
     */
    public static function getNew_AllItems(User $user, array $Rule): array
    {
        $is_ss = [0];
        $is_mu = (isset($Rule['is_mu']) ? $Rule['is_mu'] : (int) $_ENV['mergeSub']);
        $emoji = (isset($Rule['emoji']) ? $Rule['emoji'] : false);

        switch ($Rule['type']) {
            case 'ss':
                $sort = [0, 10, 13];
                $is_ss = [1];
                break;
            case 'ssr':
                $sort = [0, 10];
                break;
            case 'vmess':
                $sort = [11, 12];
                break;
            case 'trojan':
                $sort = [14];
                break;
            default:
                $Rule['type'] = 'all';
                $sort = [0, 10, 11, 12, 13, 14];
                $is_ss = [0, 1];
                break;
        }

        // 获取节点
        $nodes = self::getNodes($user, $sort, $Rule);

        // 单端口 sort = 9
        $mu_nodes = [];
        if ($is_mu != 0 && in_array($Rule['type'], ['all', 'ss', 'ssr'])) {
            $mu_node_query = Node::query();
            $mu_node_query->where('sort', 9)->where('type', '1');
            if ($is_mu != 1) {
                $mu_node_query->where('server', $is_mu);
            }
            if (!$user->is_admin) {
                $group = ($user->node_group != 0 ? [0, $user->node_group] : [0]);
                $mu_node_query->where('node_class', '<=', $user->class)
                    ->whereIn('node_group', $group);
            }
            $mu_nodes = $mu_node_query->get();
        }

        // 获取适用于用户的中转规则
        $relay_rules = $user->getRelays();

        $return_array = [];
        foreach ($nodes as $node) {
            if (isset($Rule['content']['regex']) && $Rule['content']['regex'] != '') {
                // 节点名称筛选
                if (
                    ConfController::getMatchProxy(
                        [
                            'remark' => $node->name
                        ],
                        [
                            'content' => [
                                'regex' => $Rule['content']['regex']
                            ]
                        ]
                    ) === null
                ) {
                    continue;
                }
            }
            // 筛选 End

            // 其他类型单端口节点
            if (in_array($node->sort, [11, 12, 13, 14])) {
                $node_class = [
                    11 => 'getV2RayItem',           // V2Ray
                    12 => 'getV2RayItem',           // V2Ray
                    13 => 'getV2RayPluginItem',     // Rico SS (V2RayPlugin && obfs)
                    14 => 'getTrojanItem',          // Trojan
                ];
                $class = $node_class[$node->sort];
                $item = $node->$class($user, 0, 0, 0, $emoji);
                if ($item != null) {
                    $return_array[] = $item;
                }
                continue;
            }
            // 其他类型单端口节点 End

            // SS 节点
            if (in_array($node->sort, [0, 10])) {
                // 节点非只启用单端口 && 只获取普通端口
                if ($node->mu_only != 1 && ($is_mu == 0 || ($is_mu != 0 && $_ENV['mergeSub'] === true))) {
                    foreach ($is_ss as $ss) {
                        if ($node->sort == 10) {
                            // SS 中转
                            $relay_rule_id = 0;
                            $relay_rule = Tools::pick_out_relay_rule($node->id, $user->port, $relay_rules);
                            if ($relay_rule != null && $relay_rule->dist_node() != null) {
                                $relay_rule_id = $relay_rule->id;
                            }
                            $item = $node->getItem($user, 0, $relay_rule_id, $ss, $emoji);
                        } else {
                            // SS 非中转
                            $item = $node->getItem($user, 0, 0, $ss, $emoji);
                        }
                        if ($item != null) {
                            $return_array[] = $item;
                        }
                    }
                }
                // 获取 SS 普通端口 End

                // 非只启用普通端口 && 获取单端口
                if ($node->mu_only != -1 && $is_mu != 0) {
                    foreach ($is_ss as $ss) {
                        foreach ($mu_nodes as $mu_node) {
                            if ($node->sort == 10) {
                                // SS 中转
                                $relay_rule_id = 0;
                                $relay_rule = Tools::pick_out_relay_rule($node->id, $mu_node->server, $relay_rules);
                                if ($relay_rule != null && $relay_rule->dist_node() != null) {
                                    $relay_rule_id = $relay_rule->id;
                                }
                                $item = $node->getItem($user, $mu_node->server, $relay_rule_id, $ss, $emoji);
                            } else {
                                // SS 非中转
                                $item = $node->getItem($user, $mu_node->server, 0, $ss, $emoji);
                            }
                            if ($item != null) {
                                $return_array[] = $item;
                            }
                        }
                    }
                }
                // 获取 SS 单端口 End
            }
            // SS 节点 End
        }

        return $return_array;
    }

    /**
     * 获取全部节点 Url
     *
     * ```
     *  $Rule = [
     *      'type'    => 'ss | ssr | vmess',
     *      'emoji'   => false,
     *      'is_mu'   => 1,
     *      'content' => [
     *          'noclass' => [0, 1, 2],
     *          'class'   => [0, 1, 2],
     *          'regex'   => '.*香港.*HKBN.*',
     *      ]
     *  ]
     * ```
     *
     * @param User  $user 用户
     * @param array $Rule 节点筛选规则
     */
    public static function get_NewAllUrl(User $user, array $Rule): string
    {
        $return_url = '';
        if (strtotime($user->expire_in) < time()) {
            return $return_url;
        }
        $items = URL::getNew_AllItems($user, $Rule);
        foreach ($items as $item) {
            if ($item['type'] == 'vmess') {
                $out = LinkController::getListItem($item, 'v2rayn');
            } else {
                $out = LinkController::getListItem($item, $Rule['type']);
            }
            if ($out !== null) {
                $return_url .= $out . PHP_EOL;
            }
        }
        return $return_url;
    }

    public static function getItemUrl($item, $is_ss)
    {
        return AppURI::getItemUrl($item, $is_ss);
    }

    /**
     * 获取 SS && SSR 全部节点
     *
     * @param User $user 用户
     * @param bool $emoji
     *
     * @return array
     */
    public static function getAllSSItems(User $user, $emoji = false)
    {
        return self::getNodes($user, [0, 10]);
    }

    /**
     * 获取 V2RayPlugin 全部节点
     *
     * @param User $user 用户
     * @param bool $emoji
     *
     * @return array
     */
    public static function getAllV2RayPluginItems(User $user, $emoji = false)
    {
        $return_array = array();
        $nodes = self::getNodes($user, 13);
        foreach ($nodes as $node) {
            $item = $node->getV2RayPluginItem($user, 0, 0, 0, $emoji);
            if ($item != null) {
                $return_array[] = $item;
            }
        }

        return $return_array;
    }

    /**
     * 获取 V2Ray 节点
     *
     * @param User $user
     * @param Node $node
     * @param bool $arrout
     * @param bool $emoji
     *
     * @return array|string
     */
    public static function getV2Url($user, $node, $arrout = false, $emoji = false)
    {
        $item = Tools::v2Array($node->server);
        $item['v'] = '2';
        $item['type'] = 'vmess';
        $item['ps'] = ($emoji ? Tools::addEmoji($node->name) : $node->name);
        $item['remark'] = $item['ps'];
        $item['id'] = $user->getUuid();
        $item['class'] = $node->node_class;
        if (!$arrout) {
            return 'vmess://' . base64_encode(
                json_encode($item, 320)
            );
        }
        return $item;
    }

    /**
     * 获取全部 V2Ray 节点
     *
     * @param User $user
     * @param bool $arrout
     * @param bool $emoji
     */
    public static function getAllVMessUrl(User $user, $arrout = false, $emoji = false)
    {
        $nodes = self::getNodes($user, [11, 12]);
        # 增加中转配置，后台目前配置user=0的话是自由门直接中转
        $tmp_nodes = array();
        foreach ($nodes as $node) {
            $tmp_nodes[] = $node;
            if ($node->sort == 12) {
                $relay_rule = Relay::where('source_node_id', $node->id)->where(
                    static function ($query) {
                        $query->Where('user_id', '=', 0);
                    }
                )->orderBy('priority', 'DESC')->orderBy('id')->first();
                if ($relay_rule != null) {
                    //是中转起源节点
                    $tmp_node = $relay_rule->dist_node();
                    $server = explode(';', $tmp_node->server);
                    $source_server = Tools::v2Array($node->server);
                    if (count($server) < 6) {
                        $tmp_node->server .= str_repeat(';', 6 - count($server));
                    }
                    $tmp_node->server .= 'relayserver=' . $source_server['add'] . '|' . 'outside_port=' . $source_server['port'];
                    $tmp_node->name = $node->name . '=>' . $tmp_node->name;
                    $tmp_nodes[] = $tmp_node;
                }
            }
        }
        $nodes = $tmp_nodes;
        if (!$arrout) {
            $result = '';
            foreach ($nodes as $node) {
                $result .= (self::getV2Url($user, $node, $arrout, $emoji) . PHP_EOL);
            }
        } else {
            $result = [];
            foreach ($nodes as $node) {
                $result[] = self::getV2Url($user, $node, $arrout, $emoji);
            }
        }
        return $result;
    }

    /**
     * 获取 Trojan 全部节点
     *
     * @param User $user 用户
     * @param bool $emoji
     */
    public static function getAllTrojan($user, $emoji = false): array
    {
        $return_array = array();
        $nodes = self::getNodes($user, 14);
        foreach ($nodes as $node) {
            $item = $node->getTrojanItem($user, 0, 0, 0, $emoji);
            if ($item != null) {
                $return_array[] = $item;
            }
        }

        return $return_array;
    }

    public static function getJsonObfs(array $item): string
    {
        $ss_obfs_list = Config::getSupportParam('ss_obfs');
        $plugin = '';
        if (in_array($item['obfs'], $ss_obfs_list)) {
            if (strpos($item['obfs'], 'http') !== false) {
                $plugin .= 'obfs-local --obfs http';
            } else {
                $plugin .= 'obfs-local --obfs tls';
            }
            if ($item['obfs_param'] != '') {
                $plugin .= '--obfs-host ' . $item['obfs_param'];
            }
        }
        return $plugin;
    }

    public static function getSurgeObfs(array $item): string
    {
        $ss_obfs_list = Config::getSupportParam('ss_obfs');
        $plugin = '';
        if (in_array($item['obfs'], $ss_obfs_list)) {
            if (strpos($item['obfs'], 'http') !== false) {
                $plugin .= ', obfs=http';
            } else {
                $plugin .= ', obfs=tls';
            }
            if ($item['obfs_param'] != '') {
                $plugin .= ', obfs-host=' . $item['obfs_param'];
            } else {
                $plugin .= ', obfs-host=wns.windows.com';
            }
        }
        return $plugin;
    }

    public static function cloneUser(User $user): User
    {
        return clone $user;
    }
}
