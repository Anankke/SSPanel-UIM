<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\Link;
use App\Models\Paylist;
use App\Models\Setting;
use App\Models\User;
use App\Services\Config;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use function array_diff;
use function array_flip;
use function bin2hex;
use function closedir;
use function date;
use function explode;
use function filter_var;
use function floatval;
use function floor;
use function in_array;
use function is_null;
use function is_numeric;
use function log;
use function opendir;
use function openssl_random_pseudo_bytes;
use function pow;
use function range;
use function readdir;
use function round;
use function shuffle;
use function strlen;
use function strpos;
use function strtotime;
use function substr;
use function time;
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
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public static function getIpLocation($ip): string
    {
        $err_msg = '';
        $city = null;
        $country = null;

        if ($_ENV['maxmind_license_key'] === '') {
            $err_msg = 'GeoIP2 服务未配置';
        } else {
            $geoip = new GeoIP2();
            $city = $geoip->getCity($ip);
            $country = $geoip->getCountry($ip);
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
     */
    public static function autoBytes($size, $precision = 2): string
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

    //虽然名字是toMB，但是实际上功能是from MB to B
    public static function toMB($traffic): float|int
    {
        $mb = 1048576;
        return $traffic * $mb;
    }

    //虽然名字是toGB，但是实际上功能是from GB to B
    public static function toGB($traffic): float|int
    {
        $gb = 1048576 * 1024;
        return $traffic * $gb;
    }

    public static function flowToGB($traffic): float
    {
        $gb = 1048576 * 1024;
        return $traffic / $gb;
    }

    public static function flowToMB($traffic): float
    {
        $gb = 1048576;
        return $traffic / $gb;
    }

    public static function genRandomChar($length = 8): string
    {
        return bin2hex(openssl_random_pseudo_bytes($length / 2));
    }

    public static function toDateTime(int $time): string
    {
        return date('Y-m-d H:i:s', $time);
    }

    public static function getAvPort()
    {
        if (Setting::obtain('min_port') > 65535
            || Setting::obtain('min_port') <= 0
            || Setting::obtain('max_port') > 65535
            || Setting::obtain('max_port') <= 0
        ) {
            return 0;
        }

        $det = User::pluck('port')->toArray();
        $port = array_diff(range(Setting::obtain('min_port'), Setting::obtain('max_port')), $det);
        shuffle($port);

        return $port[0];
    }

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
        $list = Config::getSupportParam($type);

        if (in_array($str, $list)) {
            return true;
        }

        return false;
    }

    public static function isEmail($input): bool
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        return true;
    }

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
                    $res['msg'] = '我们无法将邮件投递至域 ' . $mail_suffix . ' ，请更换邮件地址';
                }

                return $res;
            case 2:
                // 黑名单
                if (! in_array($mail_suffix, $mail_filter_list)) {
                    $res['ret'] = 1;
                } else {
                    $res['msg'] = '我们无法将邮件投递至域 ' . $mail_suffix . ' ，请更换邮件地址';
                }

                return $res;
            default:
                $res['ret'] = 1;
                return $res;
        }
    }

    public static function isIPv4($input): bool
    {
        if (filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return false;
        }

        return true;
    }

    public static function isIPv6($input): bool
    {
        if (filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return false;
        }

        return true;
    }

    public static function isInt($input): bool
    {
        if (filter_var($input, FILTER_VALIDATE_INT) === false) {
            return false;
        }

        return true;
    }

    public static function genSubToken(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $token = self::genRandomChar($_ENV['sub_token_len']);
            $is_token_used = Link::where('token', $token)->first();

            if ($is_token_used === null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }

    /**
     * 获取累计收入
     */
    public static function getIncome(string $req): float
    {
        $today = strtotime('00:00:00');
        $number = match ($req) {
            'today' => Paylist::where('status', 1)
                ->whereBetween('datetime', [$today, time()])->sum('total'),
            'yesterday' => Paylist::where('status', 1)
                ->whereBetween('datetime', [strtotime('-1 day', $today), $today])->sum('total'),
            'this month' => Paylist::where('status', 1)
                ->whereBetween('datetime', [strtotime('first day of this month 00:00:00'), time()])->sum('total'),
            default => Paylist::where('status', 1)->sum('total'),
        };

        return is_null($number) ? 0.00 : round(floatval($number), 2);
    }
}
