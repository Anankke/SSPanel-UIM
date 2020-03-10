<?php

namespace App\Services;

use App\Models\GConfig;

class DefaultConfig
{
    /**
     * 创建配置，成功返回 true
     *
     * @param string $key 键名
     *
     * @return bool
     */
    public static function create($key)
    {
        $value = self::default_value($key);
        if ($value != null) {
            $new                 = new GConfig();
            $new->key            = $value['key'];
            $new->type           = $value['type'];
            $new->value          = $value['value'];
            $new->name           = $value['name'];
            $new->comment        = $value['comment'];
            $new->operator_id    = $value['operator_id'];
            $new->operator_name  = $value['operator_name'];
            $new->oldvalue       = '';
            $new->operator_email = '';
            $new->last_update    = time();
            if ($new->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 创建并返回配置，如果该键名存在默认配置中
     *
     * @param string $key
     *
     * @return GConfig|null
     */
    public static function firstOrCreate($key)
    {
        return (self::create($key)
            ? GConfig::where('key', '=', $key)->first()
            : null
        );
    }

    /**
     * 检查新增的配置并创建
     *
     * @return string
     */
    public static function detectConfigs()
    {
        $return = '开始检查新增的配置项...' . PHP_EOL;
        $configs = self::configs();
        foreach ($configs as $key => $value) {
            if (GConfig::where('key', '=', $key)->first() == null) {
                if (self::create($key)) {
                    $return .= '新增的配置项：' . $key . '：' . $value['name'] . PHP_EOL;
                } else {
                    $return .= $key . '：配置项创建失败，请检查错误' . PHP_EOL;
                }
            }
        }
        $return .= '以上是新增的配置项...' . PHP_EOL;

        return $return;
    }

    /**
     * 默认配置，新增配置请添加到此处
     *
     * @param string $key 键名
     *
     * @return array
     */
    public static function configs($key = null)
    {
        $configs = [
            // Telegram 部分
            'Telegram.bool.show_group_link' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 0,
                'name'          => '在 Bot 菜单中显示加入用户群',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.group_link' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '',
                'name'          => '用户群的链接',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.bool.group_bound_user' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 0,
                'name'          => '是否仅允许已绑定 Telegram 账户的用户加入群组',
                'comment'       => '是否仅允许已绑定 Telegram 账户的用户加入 telegram_chatid 设定的群组',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.bool.unbind_kick_member' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 0,
                'name'          => '解绑 Telegram 账户后自动踢出群组',
                'comment'       => '用户解绑 Telegram 账户后自动踢出群组，不含管理员',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.Diary' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送系统今天的运行状况',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.Diary' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => ('各位老爷少奶奶，我来为大家报告一下系统今天的运行状况哈~' . PHP_EOL . '今日签到人数：%getTodayCheckinUser%' . PHP_EOL . '今日使用总流量：%lastday_total%' . PHP_EOL . '晚安~'),
                'name'          => '自定义向 Telegram 群组推送系统今天的运行状况的信息',
                'comment'       => '可用变量：' . PHP_EOL . '[今日签到人数] %getTodayCheckinUser%' . PHP_EOL . '[今日使用总流量] %lastday_total%',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.DailyJob' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送数据库清理的通知',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.DailyJob' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '姐姐姐姐，数据库被清理了，感觉身体被掏空了呢~',
                'name'          => '自定义向 Telegram 群组推送数据库清理通知的信息',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.NodeOffline' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送节点掉线的通知',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.NodeOffline' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '喵喵喵~ %node_name% 节点掉线了喵~',
                'name'          => '自定义向 Telegram 群组推送节点掉线通知的信息',
                'comment'       => '可用变量：' . PHP_EOL . '[节点名称] %node_name%',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.NodeOnline' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送节点上线的通知',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.NodeOnline' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '喵喵喵~ %node_name% 节点恢复了喵~',
                'name'          => '自定义向 Telegram 群组推送节点上线通知的信息',
                'comment'       => '可用变量：' . PHP_EOL . '[节点名称] %node_name%',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.NodeGFW' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送节点被墙的通知',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.NodeGFW' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '喵喵喵~ %node_name% 节点被墙了喵~',
                'name'          => '自定义向 Telegram 群组推送节点被墙通知的信息',
                'comment'       => '可用变量：' . PHP_EOL . '[节点名称] %node_name%',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.NodeGFW_recover' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送节点被墙恢复的通知',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.NodeGFW_recover' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '喵喵喵~ %node_name% 节点恢复了喵~',
                'name'          => '自定义向 Telegram 群组推送节点被墙恢复通知的信息',
                'comment'       => '可用变量：' . PHP_EOL . '[节点名称] %node_name%',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.AddNode' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送节点新增的通知',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.AddNode' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '新节点添加~ %node_name%',
                'name'          => '自定义向 Telegram 群组推送节点新增通知的信息',
                'comment'       => '可用变量：' . PHP_EOL . '[节点名称] %node_name%',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.UpdateNode' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送节点修改的通知',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.UpdateNode' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '节点信息被修改~ %node_name%',
                'name'          => '自定义向 Telegram 群组推送节点修改通知的信息',
                'comment'       => '可用变量：' . PHP_EOL . '[节点名称] %node_name%',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Telegram.bool.DeleteNode' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => 1,
                'name'          => '开启 Telegram 群组推送节点删除的通知',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Telegram.string.DeleteNode' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '节点被删除~ %node_name%',
                'name'          => '自定义向 Telegram 群组推送节点删除通知的信息',
                'comment'       => '可用变量：' . PHP_EOL . '[节点名称] %node_name%',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],


            // 注册设置
            'Register.string.Mode' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => 'open',
                'name'          => '注册模式',
                'comment'       => 'close：关闭' . PHP_EOL . 'open：开放' . PHP_EOL . 'invite：仅限邀请码',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.bool.Enable_email_verify' => [
                'key'           => $key,
                'type'          => 'bool',
                'value'         => '0',
                'name'          => '是否启用注册邮箱验证码',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.Email_verify_ttl' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '3600',
                'name'          => '邮箱验证码有效期',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.Email_verify_iplimit' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '10',
                'name'          => '验证码有效期内单IP可请求次数',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultTraffic' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '1',
                'name'          => '用户初始流量',
                'comment'       => '用户初始流量，单位：GB',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultClass' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '0',
                'name'          => '用户初始等级',
                'comment'       => '用户初始等级',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultConn' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '0',
                'name'          => '初始客户端数量限制',
                'comment'       => '0 为不限制',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultSpeedlimit' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '0',
                'name'          => '初始连接速率限制',
                'comment'       => '0 为不限制',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultExpire_in' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '3650',
                'name'          => '默认账户过期时间',
                'comment'       => '用户账户过期时间，在注册时设置（天）',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultClass_expire' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '24',
                'name'          => '等级过期时间',
                'comment'       => '用户等级过期时间，在注册时设置（小时）',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultMethod' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => 'chacha20-ietf',
                'name'          => '注册时默认加密方式',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultProtocol' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => 'auth_aes128_sha1',
                'name'          => '注册时默认协议',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultProtocol_param' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '',
                'name'          => '注册时默认协议参数',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultObfs' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => 'http_simple',
                'name'          => '注册时默认混淆方式',
                'comment'       => '',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultObfs_param' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '',
                'name'          => '注册时默认混淆参数',
                'comment'       => '注册时默认混淆参数 设置单端口后 这边必须配置！填写www.jd.hk就行',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],

            'Register.string.defaultInviteNum' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '10',
                'name'          => '邀请链接可用次数',
                'comment'       => '注册后的邀请链接可用次数',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
            'Register.string.defaultInvite_get_money' => [
                'key'           => $key,
                'type'          => 'string',
                'value'         => '1',
                'name'          => '通过邀请链接注册获得奖励',
                'comment'       => '新用户通过私人邀请链接注册时，获得奖励金额（作为初始资金）',
                'operator_id'   => 0,
                'operator_name' => '系统默认',
            ],
        ];
        return ($key === null
            ? $configs
            : $configs[$key]
        );
    }

    /**
     * 恢复配置为默认值
     *
     * @param string $key 键名
     *
     * @return void
     */
    public static function default_value($key)
    {
        return self::configs($key);
    }
}
