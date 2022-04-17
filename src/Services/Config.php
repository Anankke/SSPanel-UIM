<?php
namespace App\Services;

use App\Models\Setting;

class Config
{
    // TODO: remove
    public static function get($key)
    {
        return $_ENV[$key];
    }

    public static function getPublicConfig()
    {
        $public_configs = Setting::getPublicConfig();

        // 鉴于还未完成配置的全面数据库化，先这么用着
        
        return [
            'version'                 => VERSION,
            'appName'                 => $_ENV['appName'],
            'baseUrl'                 => $_ENV['baseUrl'],
            // 充值
            'active_payments'         => $_ENV['active_payments'],
            'code_payback'            => $public_configs['rebate_ratio'],
            // 个性化
            'user_center_bg'          => $public_configs['user_center_bg'],
            'admin_center_bg'         => $public_configs['admin_center_bg'],
            'user_center_bg_addr'     => $public_configs['user_center_bg_addr'],
            'admin_center_bg_addr'    => $public_configs['admin_center_bg_addr'],
            // 客服系统
            'live_chat'               => $public_configs['live_chat'],
            'tawk_id'                 => $public_configs['tawk_id'],
            'crisp_id'                => $public_configs['crisp_id'],
            'livechat_id'             => $public_configs['livechat_id'],
            'mylivechat_id'           => $public_configs['mylivechat_id'],
            // 验证码
            'captcha_provider'        => $public_configs['captcha_provider'],
            'enable_reg_captcha'      => $public_configs['enable_reg_captcha'],
            'enable_login_captcha'    => $public_configs['enable_login_captcha'],
            'enable_checkin_captcha'  => $public_configs['enable_checkin_captcha'],
            // 注册与登录
            'register_mode'           => $public_configs['reg_mode'],
            'enable_email_verify'     => $public_configs['reg_email_verify'],
            'enable_reg_im'           => $_ENV['enable_reg_im'],
            // 订阅
            'subscribe_client'        => $_ENV['subscribe_client'],
            'subscribe_client_url'    => $_ENV['subscribe_client_url'],
            'subscribeLog'            => $_ENV['subscribeLog'],
            'subscribeLog_show'       => $_ENV['subscribeLog_show'],
            'subscribeLog_keep_days'  => $_ENV['subscribeLog_keep_days'],
            // telegram
            'enable_telegram'         => $_ENV['enable_telegram'],
            'telegram_bot'            => $_ENV['telegram_bot'],
            'use_new_telegram_bot'    => $_ENV['use_new_telegram_bot'],
            'enable_telegram_login'   => $_ENV['enable_telegram_login'],
            // 其他
            'enable_checkin'          => $_ENV['enable_checkin'],
            'checkinMin'              => $_ENV['checkinMin'],
            'checkinMax'              => $_ENV['checkinMax'],
            'jump_delay'              => $_ENV['jump_delay'],
            'enable_ticket'           => $_ENV['enable_ticket'],
            'enable_docs'             => $_ENV['enable_docs'],
            'enable_kill'             => $_ENV['enable_kill'],
            'enable_change_email'     => $_ENV['enable_change_email'],
            'pwdMethod'               => $_ENV['pwdMethod'],
            'sentry_dsn'              => !empty($_ENV['sentry_dsn']) ? $_ENV['sentry_dsn'] : null,
        ];
    }

    public static function getDbConfig()
    {
        return [
            'driver'        => $_ENV['db_driver'],
            'host'          => $_ENV['db_host'],
            'unix_socket'   => $_ENV['db_socket'],
            'database'      => $_ENV['db_database'],
            'username'      => $_ENV['db_username'],
            'password'      => $_ENV['db_password'],
            'charset'       => $_ENV['db_charset'],
            'collation'     => $_ENV['db_collation'],
            'prefix'        => $_ENV['db_prefix'],
        ];
    }

    public static function getMuKey()
    {
        $muKeyList = array_key_exists('muKeyList', $_ENV) ? $_ENV['muKeyList'] : ['　'];
        return array_merge(explode(',', $_ENV['muKey']), $muKeyList);
    }

    public static function getSupportParam($type)
    {
        switch ($type) {
            case 'obfs':
                $list = array(
                    'plain',
                    'http_simple',
                    'http_simple_compatible',
                    'http_post',
                    'http_post_compatible',
                    'tls1.2_ticket_auth',
                    'tls1.2_ticket_auth_compatible',
                    'tls1.2_ticket_fastauth',
                    'tls1.2_ticket_fastauth_compatible',
                    'simple_obfs_http',
                    'simple_obfs_http_compatible',
                    'simple_obfs_tls',
                    'simple_obfs_tls_compatible'
                );
                return $list;
            case 'protocol':
                $list = array(
                    'origin',
                    'verify_deflate',
                    'auth_sha1_v4',
                    'auth_sha1_v4_compatible',
                    'auth_aes128_sha1',
                    'auth_aes128_md5',
                    'auth_chain_a',
                    'auth_chain_b',
                    'auth_chain_c',
                    'auth_chain_d',
                    'auth_chain_e',
                    'auth_chain_f'
                );
                return $list;
            case 'allow_none_protocol':
                $list = array(
                    'auth_chain_a',
                    'auth_chain_b',
                    'auth_chain_c',
                    'auth_chain_d',
                    'auth_chain_e',
                    'auth_chain_f'
                );
                return $list;
            case 'ss_aead_method':
                $list = array(
                    'aes-128-gcm',
                    'aes-192-gcm',
                    'aes-256-gcm',
                    'chacha20-ietf-poly1305',
                    'xchacha20-ietf-poly1305'
                );
                return $list;
            case 'ss_obfs':
                $list = array(
                    'simple_obfs_http',
                    'simple_obfs_http_compatible',
                    'simple_obfs_tls',
                    'simple_obfs_tls_compatible'
                );
                return $list;
            default:
                $list = array(
                    'rc4-md5',
                    'rc4-md5-6',
                    'aes-128-cfb',
                    'aes-192-cfb',
                    'aes-256-cfb',
                    'aes-128-ctr',
                    'aes-192-ctr',
                    'aes-256-ctr',
                    'camellia-128-cfb',
                    'camellia-192-cfb',
                    'camellia-256-cfb',
                    'bf-cfb',
                    'cast5-cfb',
                    'des-cfb',
                    'des-ede3-cfb',
                    'idea-cfb',
                    'rc2-cfb',
                    'seed-cfb',
                    'salsa20',
                    'chacha20',
                    'xsalsa20',
                    'chacha20-ietf',
                    'aes-128-gcm',
                    'aes-192-gcm',
                    'none',
                    'aes-256-gcm',
                    'chacha20-ietf-poly1305',
                    'xchacha20-ietf-poly1305'
                );
                return $list;
        }
    }
}
