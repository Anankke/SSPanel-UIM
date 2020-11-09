<?php

namespace App\Services;

use App\Models\GConfig;

class Config
{
    // TODO: remove
    public static function get($key)
    {
        return $_ENV[$key];
    }

    public static function getconfig($key)
    {
        $value = GConfig::where('key', '=', $key)->first();
        if ($value === null) {
            $value = DefaultConfig::firstOrCreate($key);
        }
        return $value->getValue();
    }

    public static function getPublicConfig()
    {
        return [
            'appName'                 => $_ENV['appName'],
            'version'                 => VERSION,
            'baseUrl'                 => $_ENV['baseUrl'],
            'min_port'                => $_ENV['min_port'],
            'max_port'                => $_ENV['max_port'],
            'checkinMin'              => $_ENV['checkinMin'],
            'checkinMax'              => $_ENV['checkinMax'],
            'invite_price'            => $_ENV['invite_price'],
            'invite_get_money'        => (int) self::getconfig('Register.string.defaultInvite_get_money'),
            'code_payback'            => $_ENV['code_payback'],
            'invite_gift'             => $_ENV['invite_gift'],
            'port_price'              => $_ENV['port_price'],
            'port_price_specify'      => $_ENV['port_price_specify'],
            'jump_delay'              => $_ENV['jump_delay'],
            'enable_analytics_code'   => $_ENV['enable_analytics_code'],
            'sspanelAnalysis'         => $_ENV['sspanelAnalysis'],
            'enable_donate'           => $_ENV['enable_donate'],
            'enable_telegram'         => $_ENV['enable_telegram'],
            'payment_system'          => $_ENV['payment_system'],
            'live_chat'               => $_ENV['live_chat'],
            'crisp_id'                => $_ENV['crisp_id'],
            'mylivechat_id'           => $_ENV['mylivechat_id'],
            'tawk_id'                 => $_ENV['tawk_id'],
            'enable_ticket'           => $_ENV['enable_ticket'],
            'enable_admin_contact'    => $_ENV['enable_admin_contact'],
            'admin_contact1'          => $_ENV['admin_contact1'],
            'admin_contact2'          => $_ENV['admin_contact2'],
            'admin_contact3'          => $_ENV['admin_contact3'],
            'register_mode'           => self::getconfig('Register.string.Mode'),
            'enable_flag'             => $_ENV['enable_flag'],
            'enable_kill'             => $_ENV['enable_kill'],
            'enable_change_email'     => $_ENV['enable_change_email'],
            'custom_invite_price'     => $_ENV['custom_invite_price'],
            'captcha_provider'        => $_ENV['captcha_provider'],
            'enable_email_verify'     => self::getconfig('Register.bool.Enable_email_verify'),

            'telegram_bot'            => $_ENV['telegram_bot'],

            'subscribe_client'        => $_ENV['subscribe_client'],
            'subscribe_client_url'    => $_ENV['subscribe_client_url'],

            'subscribeLog'            => $_ENV['subscribeLog'],
            'subscribeLog_show'       => $_ENV['subscribeLog_show'],
            'subscribeLog_keep_days'  => $_ENV['subscribeLog_keep_days'],

            'enable_auto_detect_ban'  => $_ENV['enable_auto_detect_ban'],
            'auto_detect_ban_type'    => $_ENV['auto_detect_ban_type'],
            'auto_detect_ban_number'  => $_ENV['auto_detect_ban_number'],
            'auto_detect_ban_time'    => $_ENV['auto_detect_ban_time'],
            'auto_detect_ban'         => $_ENV['auto_detect_ban'],

            'use_new_telegram_bot'    => $_ENV['use_new_telegram_bot'],

            'use_this_doc'            => $_ENV['use_this_doc'],
            'documents_name'          => $_ENV['documents_name'],
            'remote_documents'        => $_ENV['remote_documents'],
            'documents_source'        => $_ENV['documents_source'],

            'userCenterClient'        => $_ENV['userCenterClient'],

            'old_index_DESC'          => $_ENV['old_index_DESC'],

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

    public static function getRadiusDbConfig()
    {
        return [
            'driver'    => $_ENV['db_driver'],
            'host'      => $_ENV['radius_db_host'],
            'database'  => $_ENV['radius_db_database'],
            'username'  => $_ENV['radius_db_user'],
            'password'  => $_ENV['radius_db_password'],
            'charset'   => $_ENV['db_charset'],
            'collation' => $_ENV['db_collation']
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
            case 'relay_able_protocol':
                $list = array(
                    'auth_aes128_md5',
                    'auth_aes128_sha1',
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
