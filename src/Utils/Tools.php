<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\Config;
use App\Models\User;
use App\Services\GeoIP2;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Random\RandomException;
use function array_diff;
use function array_flip;
use function base64_encode;
use function bin2hex;
use function ceil;
use function closedir;
use function count;
use function date;
use function filter_var;
use function floor;
use function hash;
use function in_array;
use function is_numeric;
use function json_decode;
use function log;
use function max;
use function mb_strcut;
use function opendir;
use function pow;
use function random_bytes;
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
use const PHP_INT_MAX;

final class Tools
{
    /**
     * 查询IP归属
     */
    public static function getIpLocation(string $ip): string
    {
        $data = 'GeoIP2 服务未配置';
        $city = null;
        $country = null;

        if ($_ENV['maxmind_license_key'] !== '') {
            try {
                $geoip = new GeoIP2();
            } catch (InvalidDatabaseException) {
                return $data;
            }

            try {
                $city = $geoip->getCity($ip);
            } catch (AddressNotFoundException|InvalidDatabaseException) {
                $city = '未知城市';
            }

            try {
                $country = $geoip->getCountry($ip);
            } catch (AddressNotFoundException|InvalidDatabaseException) {
                $country = '未知国家';
            }
        }

        if ($country !== null) {
            $data = $country;
        }

        if ($city !== null) {
            $data = $city . ', ' . $country;
        }

        return $data;
    }

    /**
     * 根据流量值自动转换单位输出
     */
    public static function autoBytes($size, int $precision = 2): string
    {
        if ($size <= 0) {
            return '0B';
        }

        if ($size > PHP_INT_MAX) {
            return '∞';
        }

        $base = log((float) $size, 1024);
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        return round(pow(1024, $base - floor($base)), $precision) . $units[floor($base)];
    }

    /**
     * 根据含单位的流量值转换 B 输出
     */
    public static function autoBytesR(string $size): int
    {
        $suffix_single = substr($size, -1);

        if (is_numeric(substr($size, 0, -1)) && $suffix_single === 'B') {
            return (int) substr($size, 0, -1);
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
        $suffix_double = substr($size, -2);

        if (! in_array($suffix_double, $units)) {
            return -1;
        }

        $base = substr($size, 0, strlen($size) - 2);

        if (! is_numeric($base) || ($base > 999 && $suffix_double === 'EB')) {
            return -1;
        }

        return (int) ($base * pow(1024, array_flip($units)[$suffix_double]));
    }

    /**
     * 根据速率值自动转换单位输出
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

    public static function mbToB($traffic): int
    {
        if ($traffic <= 0 || $traffic > PHP_INT_MAX) {
            return 0;
        }

        return (int) $traffic * 1048576;
    }

    public static function gbToB($traffic): int
    {
        if ($traffic <= 0 || $traffic > PHP_INT_MAX) {
            return 0;
        }

        return (int) $traffic * 1073741824;
    }

    public static function bToMB($traffic): float
    {
        if ($traffic <= 0 || $traffic > PHP_INT_MAX) {
            return 0;
        }

        return round($traffic / 1048576, 2);
    }

    public static function bToGB($traffic): float
    {
        if ($traffic <= 0 || $traffic > PHP_INT_MAX) {
            return 0;
        }

        return round($traffic / 1073741824, 2);
    }

    public static function genSubToken(): string
    {
        return self::genRandomChar(max($_ENV['sub_token_len'], 8));
    }

    public static function genRandomChar(int $length = 8): string|false
    {
        if ($length <= 2) {
            $length = 2;
        }

        try {
            $randomString = bin2hex(random_bytes((int) ceil($length / 2)));
        } catch (RandomException) {
            return false;
        }

        return substr($randomString, 0, $length);
    }

    public static function genSs2022UserPk(string $passwd, string $method): string|false
    {
        $ss2022_methods = [
            '2022-blake3-aes-128-gcm',
            '2022-blake3-aes-256-gcm',
            '2022-blake3-chacha8-poly1305',
            '2022-blake3-chacha12-poly1305',
            '2022-blake3-chacha20-poly1305',
        ];

        if (! in_array($method, $ss2022_methods)) {
            return false;
        }

        $passwd_hash = hash('sha3-256', $passwd);

        $pk = match ($method) {
            '2022-blake3-aes-128-gcm' => mb_strcut($passwd_hash, 0, 16),
            default => mb_strcut($passwd_hash, 0, 32),
        };

        return base64_encode($pk);
    }

    public static function toDateTime(int $time): string
    {
        return date('Y-m-d H:i:s', $time);
    }

    public static function getSsPort(): int
    {
        $max_port = Config::obtain('max_port');
        $min_port = Config::obtain('min_port');

        if ($min_port >= 65535
            || $min_port <= 0
            || $max_port > 65535
            || $max_port <= 0
            || $min_port > $max_port
            || count(User::all()) >= $max_port - $min_port + 1
        ) {
            return 0;
        }

        $det = (new User())->pluck('port')->toArray();
        $port = array_diff(range($min_port, $max_port), $det);
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

    public static function isParamValidate($type, $str): bool
    {
        $list = self::getSsMethod($type);

        if (in_array($str, $list)) {
            return true;
        }

        return false;
    }

    public static function getSsMethod(string $type = ''): array
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

    public static function isEmail($input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    public static function isIPv4($input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        return true;
    }

    public static function isIPv6($input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return false;
        }

        return true;
    }

    public static function isInt($input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_INT)) {
            return false;
        }

        return true;
    }

    /**
     * 判断是否 JSON
     * TODO: Remove this function when PHP 8.3 is minimum requirement and replace it with native function
     */
    public static function isJson(string $string): bool
    {
        if (! json_decode($string)) {
            return false;
        }

        return true;
    }
}
