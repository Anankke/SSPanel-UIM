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

    public static function generateSSRSubCode(int $userid): string
    {
        $Elink = Link::where('userid', $userid)->first();
        if ($Elink !== null) {
            return $Elink->token;
        }

        $NLink = new Link();
        $NLink->userid = $userid;
        $NLink->token = self::genSubToken();
        $NLink->save();

        return $NLink->token;
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

    /**
     * 工单状态
     */
    public static function getTicketStatus($ticket)
    {
        if ($ticket->status === 'closed') {
            return '已结单';
        }
        if ($ticket->status === 'open_wait_user') {
            return '等待用户回复';
        }
        if ($ticket->status === 'open_wait_admin') {
            return '进行中';
        }
        return '未知';
    }

    /**
     * 工单类型
     */
    public static function getTicketType($ticket)
    {
        if ($ticket->type === 'howto') {
            return '使用';
        }
        if ($ticket->type === 'billing') {
            return '财务';
        }
        if ($ticket->type === 'account') {
            return '账户';
        }
        return '其他';
    }

    /**
     * 节点状态
     */
    public static function getNodeType($node)
    {
        return $node->type ? '显示' : '隐藏';
    }

    /**
     * 节点类型
     */
    public static function getNodeSort($node)
    {
        switch ((int) $node->sort) {
            case 0:
                $sort = 'Shadowsocks';
                break;
            case 9:
                $sort = 'ShadowsocksR 单端口多用户（旧）';
                break;
            case 11:
                $sort = 'V2Ray';
                break;
            case 14:
                $sort = 'Trojan';
                break;
            default:
                $sort = '未知';
        }
        return $sort;
    }

    /**
     * 礼品卡状态
     */
    public static function getGiftCardStatus($giftcard)
    {
        return $giftcard->status ? '已使用' : '未使用';
    }

    /**
     * 商品类型
     */
    public static function getProductType($product)
    {
        if ($product->type === 'tabp') {
            return '时间流量包';
        }
        if ($product->type === 'time') {
            return '时间包';
        }
        if ($product->type === 'bandwidth') {
            return '流量包';
        }
        return '其他';
    }

    /**
     * 商品状态
     */
    public static function getProductStatus($product)
    {
        return $product->status ? '正常' : '下架';
    }

    /**
     * 商品库存
     */
    public static function getProductStock($product)
    {
        if ($product->stock === -1) {
            return '无限制';
        }
        return $product->stock;
    }
}
