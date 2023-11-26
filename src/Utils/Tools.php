<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\Config;
use App\Models\Link;
use App\Models\User;
use App\Services\GeoIP2;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use function array_diff;
use function array_flip;
use function base64_encode;
use function bin2hex;
use function closedir;
use function date;
use function explode;
use function filter_var;
use function floor;
use function hash;
use function in_array;
use function is_numeric;
use function json_decode;
use function log;
use function mb_strcut;
use function opendir;
use function openssl_random_pseudo_bytes;
use function pow;
use function range;
use function readdir;
use function round;
use function shuffle;
use function strlen;
use function strpos;
use function substr;
use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_EMAIL;
use const FILTER_VALIDATE_INT;
use const FILTER_VALIDATE_IP;

final class Tools
{
    /**
     * 查询IP归属
     *
     * @param string $ip
     *
     * @return string
     *
     * @throws InvalidDatabaseException
     */
    public static function getIpLocation(string $ip): string
    {
        $err_msg = '';
        $city = null;
        $country = null;

        if ($_ENV['maxmind_license_key'] === '') {
            $err_msg = 'GeoIP2 服务未配置';
        } else {
            $geoip = new GeoIP2();

            try {
                $city = $geoip->getCity($ip);
            } catch (AddressNotFoundException $e) {
                $city = '未知城市';
            }

            try {
                $country = $geoip->getCountry($ip);
            } catch (AddressNotFoundException $e) {
                $country = '未知国家';
            }
        }

        if ($city !== null) {
            return $city . ', ' . $country;
        }

        if ($country !== null) {
            return $country;
        }

        return $err_msg;
    }

    /**
     * 根据流量值自动转换单位输出
     *
     * @param $size
     * @param int $precision
     *
     * @return string
     */
    public static function autoBytes($size, int $precision = 2): string
    {
        if ($size <= 0) {
            return '0B';
        }

        if ($size > 1208925819614629174706176) {
            return '∞';
        }

        $base = log((float) $size, 1024);
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        return round(pow(1024, $base - floor($base)), $precision) . $units[floor($base)];
    }

    /**
     * 根据含单位的流量值转换 B 输出
     *
     * @param $size
     *
     * @return int|null
     */
    public static function autoBytesR($size): ?int
    {
        if (is_numeric(substr($size, 0, -1))) {
            return (int) substr($size, 0, -1);
        }

        $suffix = substr($size, -2);
        $base = substr($size, 0, strlen($size) - 2);
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];

        if ($base > 999 && $suffix === 'EB') {
            return -1;
        }

        return (int) ($base * pow(1024, array_flip($units)[$suffix]));
    }

    /**
     * 根据速率值自动转换单位输出
     *
     * @param $size
     * @param int $precision
     *
     * @return string
     */
    public static function autoMbps($size, int $precision = 2): string
    {
        if ($size <= 0) {
            return '0Bps';
        }

        if ($size > 1000000000) {
            return '∞';
        }

        $base = log((float) $size, 1000);
        $units = ['Mbps', 'Gbps', 'Tbps'];

        return round(pow(1000, $base - floor($base)), $precision) . $units[floor($base)];
    }

    //虽然名字是toMB，但是实际上功能是from MB to B

    /**
     * @param $traffic
     *
     * @return int
     */
    public static function toMB($traffic): int
    {
        return (int) $traffic * 1048576;
    }

    //虽然名字是toGB，但是实际上功能是from GB to B

    /**
     * @param $traffic
     *
     * @return int
     */
    public static function toGB($traffic): int
    {
        return (int) $traffic * 1073741824;
    }

    /**
     * @param $traffic
     *
     * @return float
     */
    public static function flowToGB($traffic): float
    {
        return round($traffic / 1073741824, 2);
    }

    /**
     * @param $traffic
     *
     * @return float
     */
    public static function flowToMB($traffic): float
    {
        return round($traffic / 1048576, 2);
    }

    public static function genSubToken(): string
    {
        $token = self::genRandomChar($_ENV['sub_token_len']);
        $is_token_used = (new Link())->where('token', $token)->first();

        if ($is_token_used === null) {
            return $token;
        }

        return "couldn't alloc token";
    }

    public static function genRandomChar(int $length = 8): string
    {
        if ($length <= 2) {
            $length = 2;
        }

        return bin2hex(openssl_random_pseudo_bytes($length / 2));
    }

    public static function genSs2022UserPk($passwd, $len): string
    {
        $passwd_hash = hash('sha256', $passwd);

        $pk = match ($len) {
            16 => mb_strcut($passwd_hash, 0, 16),
            32 => mb_strcut($passwd_hash, 0, 32),
            default => $passwd_hash,
        };

        return base64_encode($pk);
    }

    public static function toDateTime(int $time): string
    {
        return date('Y-m-d H:i:s', $time);
    }

    public static function getSsPort(): int
    {
        if (Config::obtain('min_port') > 65535
            || Config::obtain('min_port') <= 0
            || Config::obtain('max_port') > 65535
            || Config::obtain('max_port') <= 0
        ) {
            return 0;
        }

        $det = (new User())->pluck('port')->toArray();
        $port = array_diff(range(Config::obtain('min_port'), Config::obtain('max_port')), $det);
        shuffle($port);

        return $port[0];
    }

    /**
     * @param $dir
     *
     * @return array
     */
    public static function getDir($dir): array
    {
        $dirArray = [];
        $handle = opendir($dir);

        if ($handle !== false) {
            $i = 0;

            while (($file = readdir($handle)) !== false) {
                if ($file !== '.' && $file !== '..' && ! strpos($file, '.')) {
                    $dirArray[$i] = $file;
                    $i++;
                }
            }

            closedir($handle);
        }

        return $dirArray;
    }

    /**
     * @param $type
     * @param $str
     *
     * @return bool
     */
    public static function isParamValidate($type, $str): bool
    {
        $list = self::getSsMethod($type);

        if (in_array($str, $list)) {
            return true;
        }

        return false;
    }

    public static function getSsMethod($type): array
    {
        return match ($type) {
            'ss_obfs' => [
                'simple_obfs_http',
                'simple_obfs_http_compatible',
                'simple_obfs_tls',
                'simple_obfs_tls_compatible',
            ],
            default => [
                'aes-128-gcm',
                'aes-192-gcm',
                'aes-256-gcm',
                'chacha20-ietf-poly1305',
                'xchacha20-ietf-poly1305',
            ],
        };
    }

    /**
     * @param $email
     *
     * @return array
     */
    public static function isEmailLegal($email): array
    {
        $res = [];
        $res['ret'] = 0;

        if (! self::isEmail($email)) {
            $res['msg'] = '邮箱不规范';
            return $res;
        }

        $mail_suffix = explode('@', $email)[1];
        $mail_filter_list = $_ENV['mail_filter_list'];

        switch ($_ENV['mail_filter']) {
            case 1:
                // 白名单
                if (in_array($mail_suffix, $mail_filter_list)) {
                    $res['ret'] = 1;
                } else {
                    $res['msg'] = '邮箱域名 ' . $mail_suffix . ' 无效，请更换邮件地址';
                }

                return $res;
            case 2:
                // 黑名单
                if (! in_array($mail_suffix, $mail_filter_list)) {
                    $res['ret'] = 1;
                } else {
                    $res['msg'] = '邮箱域名 ' . $mail_suffix . ' 无效，请更换邮件地址';
                }

                return $res;
            default:
                $res['ret'] = 1;
                return $res;
        }
    }

    /**
     * @param $input
     *
     * @return bool
     */
    public static function isEmail($input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    /**
     * @param $input
     *
     * @return bool
     */
    public static function isIPv4($input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        return true;
    }

    /**
     * @param $input
     *
     * @return bool
     */
    public static function isIPv6($input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return false;
        }

        return true;
    }

    /**
     * @param $input
     *
     * @return bool
     */
    public static function isInt($input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_INT)) {
            return false;
        }

        return true;
    }

    /**
     * 判断是否 JSON
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isJson(string $string): bool
    {
        if (! json_decode($string)) {
            return false;
        }

        return true;
    }
}
