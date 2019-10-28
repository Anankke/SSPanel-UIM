<?php

namespace App\Services;

class Config
{
    // TODO: remove
    public static function get($key)
    {
        return $_ENV[$key];
    }

    public static function getPublicConfig()
    {
        return [
            'appName' => self::get('appName'),
            'version' => VERSION,
            'baseUrl' => self::get('baseUrl'),
            'min_port' => self::get('min_port'),
            'max_port' => self::get('max_port'),
            'checkinMin' => self::get('checkinMin'),
            'checkinMax' => self::get('checkinMax'),
            'invite_price' => self::get('invite_price'),
            'invite_get_money' => self::get('invite_get_money'),
            'code_payback' => self::get('code_payback'),
            'invite_gift' => self::get('invite_gift'),
            'port_price' => self::get('port_price'),
            'port_price_specify' => self::get('port_price_specify'),
            'jump_delay' => self::get('jump_delay'),
            'enable_analytics_code' => self::get('enable_analytics_code'),
            'sspanelAnalysis' => self::get('sspanelAnalysis'),
            'enable_donate' => self::get('enable_donate'),
            'enable_telegram' => self::get('enable_telegram'),
            'enable_discord' => self::get('enable_discord'),
            'payment_system' => self::get('payment_system'),
            'enable_mylivechat' => self::get('enable_mylivechat'),
            'mylivechat_id' => self::get('mylivechat_id'),
            'enable_ticket' => self::get('enable_ticket'),
            'enable_admin_contact' => self::get('enable_admin_contact'),
            'admin_contact1' => self::get('admin_contact1'),
            'admin_contact2' => self::get('admin_contact2'),
            'admin_contact3' => self::get('admin_contact3'),
            'register_mode' => self::get('register_mode'),
            'enable_flag' => self::get('enable_flag'),
            'enable_kill' => self::get('enable_kill'),
            'custom_invite_price' => self::get('custom_invite_price'),
            'captcha_provider' => self::get('captcha_provider'),
            'enable_email_verify' => self::get('enable_email_verify'),
            'subscribe_client' => self::get('subscribe_client')
        ];
    }

    public static function getDbConfig()
    {
        return [
            'driver' => self::get('db_driver'),
            'host' => self::get('db_host'),
            'database' => self::get('db_database'),
            'username' => self::get('db_username'),
            'password' => self::get('db_password'),
            'charset' => self::get('db_charset'),
            'collation' => self::get('db_collation'),
            'prefix' => self::get('db_prefix')
        ];
    }

    public static function getRadiusDbConfig()
    {
        return [
            'driver' => self::get('db_driver'),
            'host' => self::get('radius_db_host'),
            'database' => self::get('radius_db_database'),
            'username' => self::get('radius_db_user'),
            'password' => self::get('radius_db_password'),
            'charset' => self::get('db_charset'),
            'collation' => self::get('db_collation')
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
