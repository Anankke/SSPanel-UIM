<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\Link;
use App\Models\Model;
use App\Models\Setting;
use App\Models\User;
use App\Services\Config;
use DateTime;
use ZipArchive;

final class Tools
{
    /**
     * 查询IP归属
     */
    public static function getIpInfo($ip)
    {
        $iplocation = new QQWry();
        $location = $iplocation->getlocation($ip);
        return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
    }

    /**
     * 根据流量值自动转换单位输出
     */
    public static function flowAutoShow($value = 0)
    {
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = $gb * 1024;
        $pb = $tb * 1024;
        if (abs((float) $value) > $pb) {
            return round((float) $value / $pb, 2) . 'PB';
        }

        if (abs((float) $value) > $tb) {
            return round((float) $value / $tb, 2) . 'TB';
        }

        if (abs((float) $value) > $gb) {
            return round((float) $value / $gb, 2) . 'GB';
        }

        if (abs((float) $value) > $mb) {
            return round((float) $value / $mb, 2) . 'MB';
        }

        if (abs((float) $value) > $kb) {
            return round((float) $value / $kb, 2) . 'KB';
        }

        return round((float) $value, 2) . 'B';
    }

    /**
     * 根据含单位的流量值转换 B 输出
     */
    public static function flowAutoShowZ($Value)
    {
        $number = substr($Value, 0, -2);
        if (! is_numeric($number)) {
            return null;
        }
        $number = intval($number);
        $unit = strtoupper(substr($Value, -2));
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = $gb * 1024;
        $pb = $tb * 1024;
        switch ($unit) {
            case 'B':
                $number = round($number, 2);
                break;
            case 'KB':
                $number = round($number * $kb, 2);
                break;
            case 'MB':
                $number = round($number * $mb, 2);
                break;
            case 'GB':
                $number = round($number * $gb, 2);
                break;
            case 'TB':
                $number = round($number * $tb, 2);
                break;
            case 'PB':
                $number = round($number * $pb, 2);
                break;
            default:
                return null;
                break;
        }
        return $number;
    }

    //虽然名字是toMB，但是实际上功能是from MB to B
    public static function toMB($traffic)
    {
        $mb = 1048576;
        return $traffic * $mb;
    }

    //虽然名字是toGB，但是实际上功能是from GB to B
    public static function toGB($traffic)
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

    public static function genRandomChar($length = 8)
    {
        return bin2hex(openssl_random_pseudo_bytes($length / 2));
    }

    public static function genToken()
    {
        return self::genRandomChar(64);
    }

    // Unix time to Date Time
    public static function toDateTime(int $time)
    {
        return date('Y-m-d H:i:s', $time);
    }

    public static function secondsToTime($seconds)
    {
        $dtF = new DateTime('@0');
        $dtT = new DateTime("@${seconds}");
        return $dtF->diff($dtT)->format('%a 天, %h 小时, %i 分 + %s 秒');
    }

    public static function genSID()
    {
        $unid = uniqid($_ENV['key'], true);
        return Hash::sha256WithSalt($unid);
    }

    public static function genUUID()
    {
        // @TODO
        return self::genSID();
    }

    public static function getLastPort()
    {
        $user = User::orderBy('id', 'desc')->first();
        if ($user === null) {
            return 1024; // @todo
        }
        return $user->port;
    }

    public static function getAvPort()
    {
        if (Setting::obtain('min_port') > 65535 || Setting::obtain('min_port') <= 0 || Setting::obtain('max_port') > 65535 || Setting::obtain('max_port') <= 0) {
            return 0;
        }
        $det = User::pluck('port')->toArray();
        $port = array_diff(range(Setting::obtain('min_port'), Setting::obtain('max_port')), $det);
        shuffle($port);
        return $port[0];
    }

    public static function base64UrlEncode($input)
    {
        return strtr(base64_encode($input), ['+' => '-', '/' => '_', '=' => '']);
    }

    public static function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function getDir($dir)
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

    public static function isSpecialChars($input)
    {
        return ! preg_match('/[^A-Za-z0-9\-_\.]/', $input);
    }

    public static function isParamValidate($type, $str)
    {
        $list = Config::getSupportParam($type);
        if (\in_array($str, $list)) {
            return true;
        }
        return false;
    }

    /**
     * Filter key in `App\Models\Model` object
     *
     * @param array $filter_array
     */
    public static function keyFilter(Model $object, array $filter_array): Model
    {
        foreach ($object->toArray() as $key => $value) {
            if (! \in_array($key, $filter_array)) {
                unset($object->$key);
            }
        }
        return $object;
    }

    public static function checkNoneProtocol($user)
    {
        return ! ($user->method === 'none' && ! \in_array($user->protocol, Config::getSupportParam('allow_none_protocol')));
    }

    public static function getRealIp($rawIp)
    {
        return str_replace('::ffff:', '', $rawIp);
    }

    public static function isEmail($input)
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        return true;
    }

    public static function isIPv4($input)
    {
        if (filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return false;
        }
        return true;
    }

    public static function isIPv6($input)
    {
        if (filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return false;
        }
        return true;
    }

    public static function isInt($input)
    {
        if (filter_var($input, FILTER_VALIDATE_INT) === false) {
            return false;
        }
        return true;
    }

    public static function v2Array($node)
    {
        $server = explode(';', $node->server);
        $item = [
            'host' => '',
            'path' => '',
            'tls' => '',
            'verify_cert' => true,
        ];
        $item['add'] = $server[0];
        if ($server[1] === '0' || $server[1] === '') {
            $item['port'] = 443;
        } else {
            $item['port'] = (int) $server[1];
        }
        $item['aid'] = (int) $server[2];
        $item['net'] = 'tcp';
        $item['headerType'] = 'none';
        if (count($server) >= 4) {
            $item['net'] = $server[3];
            if ($item['net'] === 'ws') {
                $item['path'] = '/';
            } elseif ($item['net'] === 'tls') {
                $item['tls'] = 'tls';
            }
            if ($server[4] === 'grpc') {
                $item['net'] = 'grpc';
            }
        }
        if (count($server) >= 5) {
            if (\in_array($item['net'], ['kcp', 'http', 'mkcp'])) {
                $item['headerType'] = $server[4];
            } else {
                switch ($server[4]) {
                    case 'ws':
                        $item['net'] = $server[4];
                        break;
                    case 'tls':
                    case 'xtls':
                        $item['tls'] = $server[4];
                        break;
                }
            }
            if (count($server) >= 6 && $server[5] !== '') {
                $item = array_merge($item, URL::parseArgs($server[5]));
            }
            $item = array_merge($item, $node->getArgs());
            if (array_key_exists('server', $item)) {
                $item['add'] = $item['server'];
                unset($item['server']);
            }
            if (array_key_exists('relayserver', $item)) {
                $item['localserver'] = $item['add'];
                $item['add'] = $item['relayserver'];
                unset($item['relayserver']);
                if ($item['tls'] === 'tls') {
                    $item['verify_cert'] = false;
                }
            }
            if (array_key_exists('outside_port', $item)) {
                $item['port'] = (int) $item['outside_port'];
                unset($item['outside_port']);
            }
            if (isset($item['inside_port'])) {
                unset($item['inside_port']);
            }

            if (array_key_exists('servicename', $item)) {
                $item['servicename'] = $item['servicename'];
            } else {
                $item['servicename'] = '';
            }

            if (array_key_exists('enable_xtls', $item)) {
                $item['enable_xtls'] = $item['enable_xtls'];
            } else {
                $item['enable_xtls'] = '';
            }

            if (array_key_exists('flow', $item)) {
                $item['flow'] = $item['flow'];
            } else {
                $item['flow'] = 'xtls-rprx-direct';
            }

            if (array_key_exists('enable_vless', $item)) {
                $item['vtype'] = 'vless://';
            } else {
                $item['vtype'] = 'vmess://';
            }

            if (! array_key_exists('sni', $item)) {
                $item['sni'] = $item['host'];
            }
        }
        return $item;
    }

    public static function ssv2Array($node)
    {
        $server = explode(';', $node->server);
        $item = [
            'host' => 'microsoft.com',
            'path' => '',
            'net' => 'ws',
            'tls' => '',
        ];
        $item['add'] = $server[0];
        if ($server[1] === '0' || $server[1] === '') {
            $item['port'] = 443;
        } else {
            $item['port'] = (int) $server[1];
        }
        if (count($server) >= 4) {
            $item['net'] = $server[3];
            if ($item['net'] === 'ws') {
                $item['path'] = '/';
            } elseif ($item['net'] === 'tls') {
                $item['tls'] = 'tls';
            }
        }
        if (count($server) >= 5) {
            if ($server[4] === 'ws') {
                $item['net'] = 'ws';
            } elseif ($server[4] === 'tls') {
                $item['tls'] = 'tls';
            }
            if (count($server) >= 6 && $server[5] !== '') {
                $item = array_merge($item, URL::parseArgs($server[5]));
            }
            $item = array_merge($item, $node->getArgs());
            if (array_key_exists('server', $item)) {
                $item['add'] = $item['server'];
                unset($item['server']);
            }
            if (array_key_exists('relayserver', $item)) {
                $item['add'] = $item['relayserver'];
                unset($item['relayserver']);
            }
            if (array_key_exists('outside_port', $item)) {
                $item['port'] = (int) $item['outside_port'];
                unset($item['outside_port']);
            }
        }

        if ($item['net'] === 'obfs') {
            if (stripos($server[4], 'http') !== false) {
                $item['obfs'] = 'simple_obfs_http';
            }
            if (stripos($server[4], 'tls') !== false) {
                $item['obfs'] = 'simple_obfs_tls';
            }
        }
        return $item;
    }

    public static function outPort($server, $node_name, $mu_port, $custom_config)
    {
        $node_server = explode(';', $server);
        $node_port = $mu_port;
        $item = $custom_config;

        if (isset($item['port'])) {
            if (strpos($item['port'], '#') !== false) { // 端口偏移，指定端口，格式：8.8.8.8;port=80#1080
                if (strpos($item['port'], '+') !== false) { // 多个单端口节点，格式：8.8.8.8;port=80#1080+443#8443
                    $args_explode = explode('+', $item['port']);
                    foreach ($args_explode as $arg) {
                        if ((int) substr($arg, 0, strpos($arg, '#')) === $mu_port) {
                            $node_port = (int) substr($arg, strpos($arg, '#') + 1);
                        }
                    }
                } else {
                    if ((int) substr($item['port'], 0, strpos($item['port'], '#')) === $mu_port) {
                        $node_port = (int) substr($item['port'], strpos($item['port'], '#') + 1);
                    }
                }
            } else { // 端口偏移，偏移端口，格式：8.8.8.8;port=1000 or 8.8.8.8;port=-1000
                $node_port = $mu_port + (int) $item['port'];
            }
        }

        return [
            'name' => ($_ENV['disable_sub_mu_port'] ? $node_name : $node_name . ' - ' . $node_port . ' 单端口'),
            'address' => $node_server[0],
            'port' => (int) $node_port,
        ];
    }

    public static function getMutilUserOutPortArray($node)
    {
        $type = 0; //偏移
        $port = []; //指定
        $item = $node->getArgs();

        if (isset($item['port'])) {
            if (strpos($item['port'], '#') !== false) {
                if (strpos($item['port'], '+') !== false) {
                    $args_explode = explode('+', $item['port']);
                    foreach ($args_explode as $arg) {
                        $replace_port = substr($arg, strpos($arg, '#') + 1);

                        if (strpos($replace_port, '@') !== false) {
                            $display_port = substr($replace_port, 0, strpos($replace_port, '@'));
                            $backend_port = substr($replace_port, strpos($replace_port, '@') + 1);

                            $port[substr($arg, 0, strpos($arg, '#'))] = [
                                'backend' => (int) $backend_port,
                                'display' => (int) $display_port,
                            ];
                        } else {
                            $user_port = substr($arg, 0, strpos($arg, '#'));

                            $port[$user_port] = [
                                'backend' => (int) $user_port,
                                'display' => (int) $user_port,
                            ];
                        }
                    }
                } else {
                    $replace_port = substr($item['port'], strpos($item['port'], '#') + 1);

                    if (strpos($replace_port, '@') !== false) {
                        $display_port = substr($replace_port, 0, strpos($replace_port, '@'));
                        $backend_port = substr($replace_port, strpos($replace_port, '@') + 1);

                        $port[substr($item['port'], 0, strpos($item['port'], '#'))] = [
                            'backend' => (int) $backend_port,
                            'display' => (int) $display_port,
                        ];
                    } else {
                        $user_port = substr($item['port'], 0, strpos($item['port'], '#'));

                        $port[$user_port] = [
                            'backend' => (int) $user_port,
                            'display' => (int) $user_port,
                        ];
                    }
                }
            } else {
                $type = (int) $item['port'];
            }
        }

        return [
            'type' => $type,
            'port' => $port,
        ];
    }

    /**
     * Add files and sub-directories in a folder to zip file.
     *
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    public static function folderToZip(string $folder, ZipArchive &$zipFile, int $exclusiveLength): void
    {
        $handle = opendir($folder);
        while (($f = readdir($handle)) !== false) {
            if ($f !== '.' && $f !== '..') {
                $filePath = "${folder}/${f}";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    /**
     * 清空文件夹
     *
     * @param string $dirName
     */
    public static function delDirAndFile($dirPath): void
    {
        $handle = opendir($dirPath);
        if ($handle) {
            while (($item = readdir($handle)) !== false) {
                if ($item !== '.' && $item !== '..') {
                    if (is_dir($dirPath . '/' . $item)) {
                        self::delDirAndFile($dirPath . '/' . $item);
                    } else {
                        unlink($dirPath . '/' . $item);
                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     * 重置自增列 ID
     */
    public static function resetAutoIncrement(DatatablesHelper $db, string $table): void
    {
        $maxid = $db->query(
            "SELECT `auto_increment` AS `maxid` FROM `information_schema`.`tables` WHERE `table_schema` = '" . $_ENV['db_database'] . "' AND `table_name` = '" . $table . "'"
        )[0]['maxid'];
        if ($maxid >= 2000000000) {
            $db->query('ALTER TABLE `' . $table . '` auto_increment = 1');
        }
    }

    /**
     * Eloquent 分页链接渲染
     *
     * @param mixed $data
     */
    public static function paginateRender($data): string
    {
        $totalPage = $data->lastPage();
        $currentPage = $data->currentPage();
        $html = '<ul class="pagination pagination-primary justify-content-end">';
        for ($i = 1; $i <= $totalPage; $i++) {
            $active = '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            $page = '<li class="page-item"><a class="page-link" href="' . $data->url($i) . '">' . $i . '</a></li>';
            if ($i === 1) {
                // 当前为第一页
                if ($currentPage === $i) {
                    $html .= '<li class="page-item disabled"><a class="page-link">上一页</a></li>';
                    $html .= $active;
                    if ($i === $totalPage) {
                        $html .= '<li class="page-item disabled"><a class="page-link">下一页</a></li>';
                        continue;
                    }
                } else {
                    $html .= '<li class="page-item"><a class="page-link" href="' . $data->url($currentPage - 1) . '">上一页</a></li>';
                    if ($currentPage > 4) {
                        $html .= '<li class="page-item disabled"><a class="page-link">...</a></li>';
                    } else {
                        $html .= $page;
                    }
                }
            }
            if ($i === $totalPage) {
                // 当前为最后一页
                if ($currentPage === $i) {
                    $html .= $active;
                    $html .= '<li class="page-item disabled"><a class="page-link">下一页</a></li>';
                } else {
                    if ($totalPage - $currentPage > 3) {
                        $html .= '<li class="page-item disabled"><a class="page-link">...</a></li>';
                    } else {
                        $html .= $page;
                    }
                    $html .= '<li class="page-item"><a class="page-link" href="' . $data->url($currentPage + 1) . '">下一页</a></li>';
                }
            }
            if ($i > 1 && $i < $totalPage) {
                // 其他页
                if ($currentPage === $i) {
                    $html .= $active;
                } else {
                    if ($totalPage > 10) {
                        if (
                            ($currentPage > 4 && $i < $currentPage && $i > $currentPage - 3)
                            ||
                            ($totalPage - $currentPage > 4 && $i > $currentPage && $i < $currentPage + 4)
                            ||
                            ($currentPage <= 4 && $i <= 4)
                            ||
                            ($totalPage - $currentPage <= 4 && $i > $currentPage)
                        ) {
                            $html .= $page;
                        }
                        continue;
                    }
                    $html .= $page;
                }
            }
        }
        return $html . '</ul>';
    }

    public static function etag($data)
    {
        return \hash('crc32c', (string) \json_encode($data));
    }

    public static function genSubToken()
    {
        for ($i = 0; $i < 10; $i++) {
            $token = self::genRandomChar(16);
            $is_token_used = Link::where('token', $token)->first();
            if ($is_token_used === null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }

    public static function searchEnvName($name)
    {
        global $_ENV;
        foreach ($_ENV as $configKey => $configValue) {
            if (strtoupper($configKey) === $name) {
                return $configKey;
            }
        }
        return null;
    }
}
