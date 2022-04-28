<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitDatabase extends AbstractMigration
{
    public function up(): void
    {
        $this->table('user', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true, 'signed' => false])
            ->addColumn('user_name', 'string', [ 'comment' => '用户名' ])
            ->addColumn('email', 'string', [ 'comment' => 'E-Mail' ])
            ->addIndex([ 'email' ], [ 'unique' => true ])
            ->addColumn('pass', 'string', [ 'comment' => '登录密码' ])
            ->addColumn('passwd', 'string', [ 'comment' => '节点密码' ])
            ->addColumn('uuid', 'string', [ 'comment' => 'UUID' ])
            ->addIndex([ 'uuid' ], [ 'unique' => true ])
            ->addColumn('t', 'biginteger', [ 'default' => 0, 'signed' => false])
            ->addColumn('u', 'biginteger', [ 'default' => 0, 'signed' => false])
            ->addColumn('d', 'biginteger', [ 'default' => 0, 'signed' => false])
            ->addColumn('transfer_enable', 'biginteger', [ 'comment' => '总流量', 'default' => 0, 'signed' => false ])
            ->addColumn('port', 'integer', [ 'comment' => '用户端口' ])
            ->addColumn('enable', 'boolean', [ 'comment' => '是否启用', 'default' => true ])
            ->addColumn('last_detect_ban_time', 'datetime', [ 'comment' => '最后一次被封禁的时间', 'default' => '1989-06-04 00:05:00' ])
            ->addColumn('all_detect_number', 'integer', [ 'comment' => '累计违规次数', 'default' => 0 ])
            ->addColumn('last_check_in_time', 'biginteger', [ 'comment' => '最后签到时间', 'default' => 0, 'signed' => false ])
            ->addColumn('reg_date', 'datetime', [ 'comment' => '注册时间' ])
            ->addColumn('invite_num', 'integer', [ 'comment' => '可用邀请次数', 'default' => 0 ])
            ->addColumn('money', 'decimal', [ 'comment' => '钱包余额', 'default' => 0 ])
            ->addColumn('ref_by', 'biginteger', [ 'comment' => '邀请人ID', 'default' => '0', 'signed' => false ])
            ->addColumn('method', 'string', [ 'comment' => 'SS/SSR加密方式', 'default' => 'rc4-md5' ])
            ->addColumn('reg_ip', 'string', [ 'comment' => '注册IP', 'default' => '127.0.0.1' ])
            ->addColumn('node_speedlimit', 'decimal', [ 'default' => 0 ])
            ->addColumn('node_connector', 'integer', [ 'default' => 0 ])
            ->addColumn('is_admin', 'boolean', [ 'comment' => '是否管理员', 'default' => false ])
            ->addColumn('im_type', 'integer', [ 'comment' => '联系方式类型', 'default' => 1 ])
            ->addColumn('im_value', 'string', [ 'comment' => '联系方式', 'default' => '' ])
            ->addColumn('last_day_t', 'biginteger', [ 'comment' => '今天之前已使用的流量', 'default' => 0 ])
            ->addColumn('sendDailyMail', 'boolean', [ 'comment' => '每日报告开关', 'default' => 0 ])
            ->addColumn('class', 'integer', [ 'comment' => '用户等级', 'default' => 0 ])
            ->addColumn('class_expire', 'datetime', [ 'comment' => '等级过期时间', 'default' => '1989-06-04 00:05:00' ])
            ->addColumn('expire_in', 'datetime', [ 'default' => '2099-06-04 00:05:00' ])
            ->addColumn('theme', 'string', [ 'comment' => '网站主题' ])
            ->addColumn('ga_token', 'string', [ ])
            ->addIndex([ 'ga_token' ], [ 'unique' => true ])
            ->addColumn('ga_enable', 'integer', [ 'default' => '0' ])
            ->addColumn('remark', 'string', [ 'comment' => '备注', 'default' => '' ])
            ->addColumn('node_group', 'integer', [ 'comment' => '节点分组', 'default' => 0 ])
            ->addColumn('protocol', 'string', [ 'comment' => 'SS/SSR协议方式', 'default' => 'origin' ])
            ->addColumn('protocol_param', 'string', [ 'default' => '' ])
            ->addColumn('obfs', 'string', [ 'comment' => 'SS/SSR混淆方式', 'default' => 'plain' ])
            ->addColumn('obfs_param', 'string', [ 'default' => '' ])
            ->addColumn('is_hide', 'integer', [ 'default' => 0 ])
            ->addColumn('is_multi_user', 'integer', [ 'default' => 0 ])
            ->addColumn('telegram_id', 'biginteger', [ 'default' => 0 ])
            ->addColumn('expire_notified', 'boolean', [ 'default' => false])
            ->addColumn('traffic_notified', 'boolean', [ 'default' => false])
            ->addColumn('forbidden_ip', 'string', [ 'default' => '' ])
            ->addColumn('forbidden_port', 'string', [ 'default' => '' ])
            ->addColumn('auto_reset_day', 'integer', [ 'default' => 0])
            ->addColumn('auto_reset_bandwidth', 'decimal', [ 'default' => '0.00', 'precision' => 12, 'scale' => 2 ])
            ->addIndex([ 'user_name' ])
            ->create();

        $this->table('node', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'identity' => true ])
            ->addColumn('name', 'string', [])
            ->addColumn('type', 'integer', [])
            ->addColumn('server', 'string', [])
            ->addColumn('custom_config', 'string', [])
            ->addColumn('info', 'string', [ 'default' => '' ])
            ->addColumn('status', 'string', [ 'default' => '' ])
            ->addColumn('sort', 'integer', [])
            ->addColumn('traffic_rate', 'float', [ 'default' => 1 ])
            ->addColumn('node_class', 'integer', [ 'default' => 0 ])
            ->addColumn('node_speedlimit', 'decimal', [ 'default' => 0.00,'precision' => 12, 'scale' => 2 ])
            ->addColumn('node_connector', 'integer', [ 'default' => 0 ])
            ->addColumn('node_bandwidth', 'biginteger', [ 'default' => 0 ])
            ->addColumn('node_bandwidth_limit', 'biginteger', [ 'default' => 0 ])
            ->addColumn('bandwidthlimit_resetday', 'integer', [ 'default' => 0 ])
            ->addColumn('node_heartbeat', 'biginteger', [ 'default' => 0 ])
            ->addColumn('node_ip', 'string', [ 'default' => null ])
            ->addColumn('node_group', 'integer', [ 'default' => 0 ])
            ->addColumn('mu_only', 'boolean', [ 'default' => false ])
            ->addColumn('online', 'boolean', [ 'default' => true ])
            ->addColumn('gfw_block', 'boolean', [ 'default' => false ])
            ->create();

        $this->table('alive_ip', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('nodeid', 'integer', [])
            ->addColumn('userid', 'integer', [])
            ->addColumn('ip', 'string', [])
            ->addColumn('datetime', 'biginteger', [])
            ->create();

        $this->table('announcement', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'identity' => true ])
            ->addColumn('date', 'datetime', [])
            ->addColumn('content', 'string', [])
            ->addColumn('markdown', 'string', [])
            ->create();

        $this->table('blockip', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('nodeid', 'integer', [])
            ->addColumn('ip', 'string', [])
            ->addColumn('datetime', 'biginteger', [])
            ->create();

        $this->table('bought', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('userid', 'biginteger', [])
            ->addColumn('shopid', 'biginteger', [])
            ->addColumn('datetime', 'biginteger', [])
            ->addColumn('renew', 'biginteger', [])
            ->addColumn('coupon', 'string', [])
            ->addColumn('price', 'decimal', [ 'precision' => 12, 'scale' => 2 ])
            ->addColumn('is_notified', 'boolean', [ 'default' => false ])
            ->create();

        $this->table('code', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('code', 'string', [])
            ->addColumn('type', 'integer', [])
            ->addColumn('number', 'decimal', [ 'precision' => 12, 'scale' => 2 ])
            ->addColumn('isused', 'integer', [ 'default' => 0 ])
            ->addColumn('userid', 'biginteger', [])
            ->addColumn('usedatetime', 'datetime', [])
            ->create();

        $this->table('config', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'comment' => '主键','identity' => true ])
            ->addColumn('item', 'string', [ 'comment' => '项' ])
            ->addColumn('value', 'string', [ 'comment' => '值' ])
            ->addColumn('class', 'string', [ 'comment' => '配置分类','default' => 'default' ])
            ->addColumn('is_public', 'integer', [ 'comment' => '是否为公共参数','default' => 0 ])
            ->addColumn('type', 'string', [ 'comment' => '值类型' ])
            ->addColumn('default', 'string', [ 'comment' => '默认值' ])
            ->addColumn('mark', 'string', [ 'comment' => '备注' ])
            ->create();

        $this->table('coupon', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('code', 'string', [])
            ->addColumn('onetime', 'integer', [])
            ->addColumn('expire', 'biginteger', [])
            ->addColumn('shop', 'string', [])
            ->addColumn('credit', 'integer', [])
            ->create();

        $this->table('detect_ban_log', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'identity' => true,'signed' => false ])
            ->addColumn('user_name', 'string', [ 'comment' => '用户名' ])
            ->addColumn('user_id', 'biginteger', [ 'comment' => '用户 ID','signed' => false ])
            ->addColumn('email', 'string', [ 'comment' => '用户邮箱' ])
            ->addColumn('detect_number', 'integer', [ 'comment' => '本次违规次数' ])
            ->addColumn('ban_time', 'integer', [ 'comment' => '本次封禁时长' ])
            ->addColumn('start_time', 'biginteger', [ 'comment' => '统计开始时间' ])
            ->addColumn('end_time', 'biginteger', [ 'comment' => '统计结束时间' ])
            ->addColumn('all_detect_number', 'integer', [ 'comment' => '累计违规次数' ])
            ->addIndex([ 'user_id' ])
            ->addForeignKey('user_id', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('detect_list', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
            ->addColumn('name', 'string', [])
            ->addColumn('text', 'string', [])
            ->addColumn('regex', 'string', [])
            ->addColumn('type', 'integer', [])
            ->create();

        $this->table('detect_log', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
            ->addColumn('user_id', 'biginteger', [ 'signed' => false ])
            ->addColumn('list_id', 'biginteger', [ 'signed' => false ])
            ->addColumn('datetime', 'biginteger', [ 'signed' => false ])
            ->addColumn('node_id', 'integer', [])
            ->addColumn('status', 'integer', [ 'default' => 0 ])
            ->addIndex([ 'user_id' ])
            ->addIndex([ 'node_id' ])
            ->addIndex([ 'list_id' ])
            ->addForeignKey('user_id', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->addForeignKey('node_id', 'node', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->addForeignKey('list_id', 'detect_list', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('email_queue', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('to_email', 'string', [])
            ->addColumn('subject', 'string', [])
            ->addColumn('template', 'string', [])
            ->addColumn('array', 'string', [])
            ->addColumn('time', 'integer', [])
            ->create();

        $this->table('email_verify', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('email', 'string', [])
            ->addColumn('ip', 'string', [])
            ->addColumn('code', 'string', [])
            ->addColumn('expire_in', 'biginteger', [])
            ->create();

        $this->table('gconfig', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'identity' => true,'signed' => false ])
            ->addColumn('key', 'string', [ 'comment' => '配置键名' ])
            ->addColumn('type', 'string', [ 'comment' => '值类型' ])
            ->addColumn('value', 'string', [ 'comment' => '配置值' ])
            ->addColumn('oldvalue', 'string', [ 'comment' => '之前的配置值' ])
            ->addColumn('name', 'string', [ 'comment' => '配置名称' ])
            ->addColumn('comment', 'string', [ 'comment' => '配置描述' ])
            ->addColumn('operator_id', 'integer', [ 'comment' => '操作员 ID' ])
            ->addColumn('operator_name', 'string', [ 'comment' => '操作员名称' ])
            ->addColumn('operator_email', 'string', [ 'comment' => '操作员邮箱' ])
            ->addColumn('last_update', 'biginteger', [ 'comment' => '修改时间' ])
            ->create();

        $this->table('link', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('token', 'string', [ ])
            ->addIndex([ 'token' ], [ 'unique' => true ])
            ->addColumn('userid', 'biginteger', [ 'signed' => false ])
            ->addIndex([ 'userid' ], [ 'unique' => true ])
            ->addForeignKey('userid', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('login_ip', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('userid', 'biginteger', [ 'signed' => false ])
            ->addColumn('ip', 'string', [])
            ->addColumn('datetime', 'biginteger', [])
            ->addColumn('type', 'integer', [])
            ->addIndex([ 'userid' ])
            ->addForeignKey('userid', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('payback', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('total', 'decimal', [ 'precision' => 12, 'scale' => 2 ])
            ->addColumn('userid', 'biginteger', [])
            ->addColumn('ref_by', 'biginteger', [])
            ->addColumn('ref_get', 'decimal', [ 'precision' => 12, 'scale' => 2 ])
            ->addColumn('datetime', 'biginteger', [])
            ->create();

        $this->table('paylist', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('userid', 'biginteger', [ 'signed' => false ])
            ->addColumn('total', 'decimal', [ 'precision' => 12, 'scale' => 2 ])
            ->addColumn('status', 'integer', [ 'default' => 0 ])
            ->addColumn('tradeno', 'string', [ 'default' => null ])
            ->addColumn('datetime', 'biginteger', [ 'default' => 0 ])
            ->addIndex([ 'userid' ])
            ->addForeignKey('userid', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('shop', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('name', 'string', [])
            ->addColumn('price', 'decimal', [ 'precision' => 12, 'scale' => 2 ])
            ->addColumn('content', 'string', [])
            ->addColumn('auto_renew', 'integer', [])
            ->addColumn('auto_reset_bandwidth', 'integer', [ 'default' => 0 ])
            ->addColumn('status', 'integer', [ 'default' => 1 ])
            ->create();

        $this->table('user_invite_code', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
            ->addColumn('code', 'string', [  ])
            ->addIndex([ 'code' ], [ 'unique' => true ])
            ->addColumn('user_id', 'biginteger', [ 'signed' => false ])
            ->addIndex([ 'user_id' ], [ 'unique' => true ])
            ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP' ])
            ->addColumn('updated_at', 'timestamp', [ 'default' => '2016-06-01 00:00:00' ])
            ->addForeignKey('user_id', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('node_info', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
            ->addColumn('node_id', 'integer', [])
            ->addColumn('uptime', 'float', [])
            ->addColumn('load', 'string', [])
            ->addColumn('log_time', 'integer', [])
            ->addIndex([ 'node_id' ])
            ->addForeignKey('node_id', 'node', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('node_online_log', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'identity' => true ])
            ->addColumn('node_id', 'integer', [])
            ->addColumn('online_user', 'integer', [])
            ->addColumn('log_time', 'integer', [])
            ->addIndex([ 'node_id' ])
            ->addForeignKey('node_id', 'node', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('user_password_reset', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'identity' => true ])
            ->addColumn('email', 'string', [])
            ->addColumn('token', 'string', [])
            ->addColumn('init_time', 'integer', [])
            ->addColumn('expire_time', 'integer', [])
            ->create();

        $this->table('telegram_session', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('user_id', 'biginteger', [])
            ->addColumn('type', 'integer', [])
            ->addColumn('session_content', 'string', [])
            ->addColumn('datetime', 'biginteger', [])
            ->create();

        $this->table('ticket', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('title', 'string', [])
            ->addColumn('content', 'string', [])
            ->addColumn('rootid', 'biginteger', [])
            ->addColumn('userid', 'biginteger', [])
            ->addColumn('datetime', 'biginteger', [])
            ->addColumn('status', 'integer', [ 'default' => 1 ])
            ->create();

        $this->table('unblockip', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true ])
            ->addColumn('ip', 'string', [])
            ->addColumn('datetime', 'biginteger', [])
            ->addColumn('userid', 'biginteger', [])
            ->create();

        $this->table('user_hourly_usage', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
            ->addColumn('user_id', 'biginteger', [ 'signed' => false ])
            ->addColumn('traffic', 'biginteger', [])
            ->addColumn('hourly_usage', 'biginteger', [])
            ->addColumn('datetime', 'integer', [])
            ->addIndex([ 'user_id' ])
            ->addForeignKey('user_id', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('user_subscribe_log', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
            ->addColumn('user_name', 'string', [ 'comment' => '用户名' ])
            ->addColumn('user_id', 'biginteger', [ 'comment' => '用户 ID','signed' => false ])
            ->addColumn('email', 'string', [ 'comment' => '用户邮箱' ])
            ->addColumn('subscribe_type', 'string', [ 'comment' => '获取的订阅类型' ])
            ->addColumn('request_ip', 'string', [ 'comment' => '请求 IP' ])
            ->addColumn('request_time', 'datetime', [ 'comment' => '请求时间' ])
            ->addColumn('request_user_agent', 'string', [ 'comment' => '请求 UA 信息','default' => null ])
            ->addIndex([ 'user_id' ])
            ->addForeignKey('user_id', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();

        $this->table('user_token', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
            ->addColumn('token', 'string', [])
            ->addColumn('user_id', 'biginteger', [ 'signed' => false ])
            ->addColumn('create_time', 'biginteger', [ 'signed' => false ])
            ->addColumn('expire_time', 'biginteger', [])
            ->addIndex([ 'user_id' ])
            ->addForeignKey('user_id', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();
    }

    public function down(): void
    {
        $this->table('user')->drop()->update();
        $this->table('node')->drop()->update();
        $this->table('alive_ip')->drop()->update();
        $this->table('announcement')->drop()->update();
        $this->table('blockip')->drop()->update();
        $this->table('bought')->drop()->update();
        $this->table('code')->drop()->update();
        $this->table('config')->drop()->update();
        $this->table('coupon')->drop()->update();
        $this->table('detect_ban_log')->drop()->update();
        $this->table('detect_list')->drop()->update();
        $this->table('detect_log')->drop()->update();
        $this->table('email_queue')->drop()->update();
        $this->table('email_verify')->drop()->update();
        $this->table('gconfig')->drop()->update();
        $this->table('link')->drop()->update();
        $this->table('login_ip')->drop()->update();
        $this->table('payback')->drop()->update();
        $this->table('paylist')->drop()->update();
        $this->table('shop')->drop()->update();
        $this->table('user_invite_code')->drop()->update();
        $this->table('node_info')->drop()->update();
        $this->table('node_online_log')->drop()->update();
        $this->table('user_password_reset')->drop()->update();
        $this->table('telegram_session')->drop()->update();
        $this->table('ticket')->drop()->update();
        $this->table('unblockip')->drop()->update();
        $this->table('user_hourly_usage')->drop()->update();
        $this->table('user_subscribe_log')->drop()->update();
        $this->table('user_token')->drop()->update();
    }
}
