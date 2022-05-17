<?php

declare(strict_types=1);

namespace App\Utils;

use App\Controllers\LinkController;
use App\Models\Node;
use App\Models\User;
use App\Services\Config;

final class URL
{
    /*
    * 1 SSR can
    * 2 SS can
    * 3 Both can
    */
    public static function canMethodConnect($method)
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
    public static function canProtocolConnect($protocol)
    {
        if ($protocol !== 'origin') {
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
    public static function canObfsConnect($obfs)
    {
        if ($obfs !== 'plain') {
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

    public static function SSCanConnect(User $user, $mu_port = 0): bool
    {
        if ($mu_port !== 0) {
            $mu_user = User::where('port', '=', $mu_port)
                ->where('is_multi_user', '<>', 0)->first();
            if ($mu_user === null) {
                return 0;
            }
            return self::SSCanConnect($mu_user);
        }
        return self::canMethodConnect($user->method) >= 2 &&
            self::canProtocolConnect($user->protocol) >= 2 &&
            self::canObfsConnect($user->obfs) >= 2;
    }

    public static function SSRCanConnect(User $user, $mu_port = 0): bool
    {
        if ($mu_port !== 0) {
            $mu_user = User::where('port', '=', $mu_port)
                ->where('is_multi_user', '<>', 0)->first();
            if ($mu_user === null) {
                return false;
            }
            return self::SSRCanConnect($mu_user);
        }
        return self::canMethodConnect($user->method) !== 2 &&
            self::canProtocolConnect($user->protocol) !== 2 &&
            self::canObfsConnect($user->obfs) !== 2;
    }

    public static function getSSConnectInfo(User $user)
    {
        $new_user = clone $user;
        if (self::canObfsConnect($new_user->obfs) === 5) {
            $new_user->obfs = 'plain';
            $new_user->obfs_param = '';
        }
        if (self::canProtocolConnect($new_user->protocol) === 3) {
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
        if (self::canObfsConnect($new_user->obfs) === 4) {
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
     * @param mixed $sort  数值或数组
     * @param array $rules 节点筛选规则
     */
    public static function getNodes(
        User $user,
        $sort,
        array $rules = []
    ): \Illuminate\Database\Eloquent\Collection {
        $query = Node::query();
        if (is_array($sort)) {
            $query->whereIn('sort', $sort);
        } else {
            $query->where('sort', $sort);
        }
        if (! $user->is_admin) {
            $group = ($user->node_group !== 0 ? [0, $user->node_group] : [0]);
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
        return $query->where('type', '1')
            ->orderBy('name')->get();
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
    public static function getNewAllItems(User $user, array $Rule): array
    {
        $is_ss = [0];
        $is_mu = ($Rule['is_mu'] ?? (int) $_ENV['mergeSub']);
        $emoji = ($Rule['emoji'] ?? false);

        switch ($Rule['type']) {
            case 'ss':
                $sort = [0, 13];
                $is_ss = [1];
                break;
            case 'ssr':
                $sort = [0];
                break;
            case 'vmess':
                $sort = [11];
                break;
            case 'trojan':
                $sort = [14];
                break;
            default:
                $Rule['type'] = 'all';
                $sort = [0, 11, 13, 14];
                $is_ss = [0];
                break;
        }

        // 获取节点
        $nodes = self::getNodes($user, $sort, $Rule);

        // 单端口 sort = 9
        $mu_nodes = [];
        if ($is_mu !== 0 && in_array($Rule['type'], ['all', 'ss', 'ssr'])) {
            $mu_node_query = Node::query();
            $mu_node_query->where('sort', 9)->where('type', '1');
            if ($is_mu !== 1) {
                $mu_node_query->where('server', $is_mu);
            }
            if (! $user->is_admin) {
                $group = ($user->node_group !== 0 ? [0, $user->node_group] : [0]);
                $mu_node_query->where('node_class', '<=', $user->class)
                    ->whereIn('node_group', $group);
            }
            $mu_nodes = $mu_node_query->get();
        }

        $return_array = [];
        foreach ($nodes as $node) {
            if (isset($Rule['content']['regex']) && $Rule['content']['regex'] !== '') {
                // 节点名称筛选
                if (
                    ConfGenerate::getMatchProxy(
                        [
                            'remark' => $node->name,
                        ],
                        [
                            'content' => [
                                'regex' => $Rule['content']['regex'],
                            ],
                        ]
                    ) === null
                ) {
                    continue;
                }
            }
            // 筛选 End

            // 其他类型单端口节点
            if (in_array($node->sort, [11, 13, 14])) {
                $node_class = [
                    11 => 'getV2RayItem',           // V2Ray
                    13 => 'getV2RayPluginItem',     // Rico SS (V2RayPlugin && obfs)
                    14 => 'getTrojanItem',          // Trojan
                ];
                $class = $node_class[$node->sort];
                $item = $node->$class($user, 0, 0, $emoji);
                if ($item !== null) {
                    $return_array[] = $item;
                }
                continue;
            }
            // 其他类型单端口节点 End

            // SS 节点
            if (in_array($node->sort, [0])) {
                // 节点非只启用单端口 && 只获取普通端口
                if ($node->mu_only !== 1 &&
                    ($is_mu === 0 || ($is_mu !== 0 && $_ENV['mergeSub'] === true))) {
                    foreach ($is_ss as $ss) {
                        $item = $node->getItem($user, 0, $ss, $emoji);
                        if ($item !== null) {
                            $return_array[] = $item;
                        }
                    }
                }
                // 获取 SS 普通端口 End

                // 非只启用普通端口 && 获取单端口
                if ($node->mu_only !== -1 && $is_mu !== 0) {
                    foreach ($is_ss as $ss) {
                        foreach ($mu_nodes as $mu_node) {
                            $item = $node->getItem($user, $mu_node->server, $ss, $emoji);
                            if ($item !== null) {
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
    public static function getNewAllUrl(User $user, array $Rule): string
    {
        $return_url = '';
        if (strtotime($user->expire_in) < time()) {
            return $return_url;
        }
        $items = URL::getNewAllItems($user, $Rule);
        foreach ($items as $item) {
            if ($item['type'] === 'vmess') {
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
     *
     * @return array
     */
    public static function getAllSSItems(User $user, bool $emoji = false): array
    {
        return self::getNodes($user, [0, 10]);
    }

    /**
     * 获取 V2RayPlugin 全部节点
     *
     * @param User $user 用户
     *
     * @return array
     */
    public static function getAllV2RayPluginItems(User $user, bool $emoji = false): array
    {
        $return_array = [];
        $nodes = self::getNodes($user, 13);
        foreach ($nodes as $node) {
            $item = $node->getV2RayPluginItem($user, 0, 0, $emoji);
            if ($item !== null) {
                $return_array[] = $item;
            }
        }

        return $return_array;
    }

    /**
     * 获取 V2Ray 节点
     *
     * @return array|string
     */
    public static function getV2Url(
        User $user,
        Node $node,
        bool $arrout = false,
        bool $emoji = false
    ) {
        $item = Tools::v2Array($node->server);
        $item['v'] = '2';
        $item['type'] = 'vmess';
        $item['ps'] = ($emoji ? Tools::addEmoji($node->name) : $node->name);
        $item['remark'] = $item['ps'];
        $item['id'] = $user->uuid;
        $item['class'] = $node->node_class;
        if (! $arrout) {
            return 'vmess://' . base64_encode(
                json_encode($item, 320)
            );
        }
        return $item;
    }

    /**
     * 获取全部 V2Ray 节点
     */
    public static function getAllVMessUrl(
        User $user,
        bool $arrout = false,
        bool $emoji = false
    ) {
        $nodes = self::getNodes($user, [11]);
        # 增加中转配置，后台目前配置user=0的话是自由门直接中转
        $tmp_nodes = [];
        foreach ($nodes as $node) {
            $tmp_nodes[] = $node;
        }
        $nodes = $tmp_nodes;
        if (! $arrout) {
            $result = '';
            foreach ($nodes as $node) {
                $result .= self::getV2Url($user, $node, $arrout, $emoji) . PHP_EOL;
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
     */
    public static function getAllTrojan(User $user, bool $emoji = false): array
    {
        $return_array = [];
        $nodes = self::getNodes($user, 14);
        foreach ($nodes as $node) {
            $item = $node->getTrojanItem($user, 0, 0, $emoji);
            if ($item !== null) {
                $return_array[] = $item;
            }
        }

        return $return_array;
    }

    /**
     * 获取 Trojan URL
     *
     * @param User $user 用户
     */
    public static function getTrojanUrl(User $user, Node $node): string
    {
        $server = $node->getTrojanItem($user);
        $return = 'trojan://' . $server['passwd']
            . '@' . $server['address'] . ':' . $server['port'];
        if ($server['host'] !== $server['address']) {
            $return .= '?peer=' . $server['host'] . '&sni=' . $server['host'];
        }
        return $return . '#' . rawurlencode($node->name);
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
            if ($item['obfs_param'] !== '') {
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
            if ($item['obfs_param'] !== '') {
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
