<?php

declare(strict_types=1);

namespace App\Utils\Telegram;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserMoneyLog;
use App\Utils\Tools;
use function array_map;
use function count;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function date;
use function explode;
use function implode;
use function in_array;
use function is_numeric;
use function json_encode;
use function number_format;
use function str_pad;
use function stripos;
use function strlen;
use function strpos;
use function strtotime;
use function substr;
use function time;
use function trim;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;
use const PHP_EOL;

final class TelegramTools
{
    /**
     * 搜索用户
     *
     * @param int $value  搜索值
     * @param string $method 查找列
     */
    public static function getUser(int $value, string $method = 'telegram_id')
    {
        return User::where($method, $value)->first();
    }

    /**
     * Sends a POST request to Telegram Bot API.
     * 伪异步，无结果返回.
     *
     * @param $Method
     * @param $Params
     */
    public static function sendPost($Method, $Params): void
    {
        $URL = 'https://api.telegram.org/bot' . $_ENV['telegram_token'] . '/' . $Method;
        $POSTData = json_encode($Params);
        $C = curl_init();
        curl_setopt($C, CURLOPT_URL, $URL);
        curl_setopt($C, CURLOPT_POST, 1);
        curl_setopt($C, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf-8']);
        curl_setopt($C, CURLOPT_POSTFIELDS, $POSTData);
        curl_setopt($C, CURLOPT_TIMEOUT, 1);
        curl_exec($C);
        curl_close($C);
    }

    /**
     * 用户识别搜索字段
     *
     * @return array
     */
    public static function getUserSearchMethods(): array
    {
        return [
            'id' => [],
            'email' => ['email'],
            'port' => ['port'],
        ];
    }

    /**
     * 操作字段
     *
     * @return array
     */
    public static function getUserActionOption(): array
    {
        return [
            'is_admin' => ['is_admin'],
            'banned' => ['banned'],
            'money' => ['remark_user_option_money'],
            'port' => ['port'],
            'transfer_enable' => ['transfer_enable'],
            'passwd' => ['passwd'],
            'method' => ['method'],
            'invite_num' => ['invite_num'],
            'node_group' => ['node_group'],
            'class' => ['class'],
            'class_expire' => ['class_expire'],
            'expire_in' => ['expire_in'],
            'node_speedlimit' => ['node_speedlimit'],
            'node_iplimit' => ['node_iplimit'],
        ];
    }

    /**
     * 待定
     *
     * @param $User
     * @param $useOptionMethod
     * @param $value
     * @param $ChatID
     *
     * @return array
     */
    public static function operationUser($User, $useOptionMethod, $value, $ChatID): array
    {
        $Email = self::getUserEmail($User->email, $ChatID);
        $old = $User->$useOptionMethod;
        $useOptionMethodName = self::getUserActionOption()[$useOptionMethod][0];
        switch ($useOptionMethod) {
            // ##############
            case 'is_banned':
            case 'is_admin':
                $strArray = [
                    '// 支持的写法',
                    '// [启用|是] —— 字面意思',
                    '// [禁用|否] —— 字面意思',
                ];
                if (str_contains($value, ' ')) {
                    return [
                        'ok' => false,
                        'msg' => '处理出错，不支持的写法.' . PHP_EOL . PHP_EOL . self::strArrayToCode($strArray),
                    ];
                }
                if (in_array($value, ['启用', '是'])) {
                    $User->$useOptionMethod = 1;
                    $new = '启用';
                } elseif (in_array($value, ['禁用', '否'])) {
                    $User->$useOptionMethod = 0;
                    $new = '禁用';
                } else {
                    return [
                        'ok' => false,
                        'msg' => '处理出错，不支持的写法.' . PHP_EOL . PHP_EOL . self::strArrayToCode($strArray),
                    ];
                }
                $old = ($old ? '启用' : '禁用');
                break;
                // ##############
            case 'port':
                // 支持正整数或 0 随机选择
                if (! is_numeric($value) || str_starts_with((string) $value, '-')) {
                    return [
                        'ok' => false,
                        'msg' => '提供的端口非数值，如要随机重置请指定为 0.',
                    ];
                }
                if ((int) $value === 0) {
                    $value = Tools::getAvPort();
                }
                $temp = $User->setPort($value);
                if ($temp['ok'] === false) {
                    $strArray = [
                        '目标用户：' . $Email,
                        '欲修改项：' . $useOptionMethodName . '[' . $useOptionMethod . ']',
                        '当前值为：' . $old,
                        '欲修改为：' . $value,
                        '错误详情：' . $temp['msg'],
                    ];
                    return [
                        'ok' => false,
                        'msg' => self::strArrayToCode($strArray),
                    ];
                }
                $new = $User->$useOptionMethod;
                $User->$useOptionMethod = $new;
                break;
                // ##############
            case 'transfer_enable':
                $strArray = [
                    '// 支持的写法，不支持单位 b，不区分大小写',
                    '// 支持的单位：kb | mb | gb | tb | pb',
                    '//  2kb —— 指定为该值得流量',
                    '// +2kb —— 增加流量',
                    '// -2kb —— 减少流量',
                    '// *2   —— 以当前流量做乘法，不支持填写单位',
                    '// /2   —— 以当前流量做除法，不支持填写单位',
                ];
                if (str_contains($value, ' ')) {
                    return [
                        'ok' => false,
                        'msg' => '处理出错，不支持的写法.' . PHP_EOL . PHP_EOL . self::strArrayToCode($strArray),
                    ];
                }
                $new = self::trafficMethod($User->$useOptionMethod, $value);
                if ($new === null) {
                    return [
                        'ok' => false,
                        'msg' => '处理出错，不支持的写法.' . PHP_EOL . PHP_EOL . self::strArrayToCode($strArray),
                    ];
                }
                $User->$useOptionMethod = $new;
                $old = Tools::autoBytes($old);
                $new = Tools::autoBytes($new);
                break;
                // ##############
            case 'expire_in':
            case 'class_expire':
                $strArray = [
                    '// 支持的写法，单位：天',
                    '// 使用天数设置不能包含空格',
                    '//  2 —— 以当前时间为基准的天数设置',
                    '// +2 —— 增加天数',
                    '// -2 —— 减少天数',
                    '// 指定日期，在日期与时间中含有一个空格',
                    '// 2020-02-30 —— 指定日期',
                    '// 2020-02-30 08:00:00 —— 指定日期精确到秒',
                ];
                if (
                    str_starts_with($value, '+')
                    ||
                    str_starts_with($value, '-')
                ) {
                    $operator = substr($value, 0, 1);
                    $number = substr($value, 1);
                    if (is_numeric($number)) {
                        $number *= 86400;
                        $old_time = strtotime($old);
                        $new = ($operator === '+' ? $old_time + $number : $old_time - $number);
                        $new = date('Y-m-d H:i:s', (int) $new);
                    } else {
                        if (strtotime($value) === false) {
                            return [
                                'ok' => false,
                                'msg' => '处理出错，不支持的写法.' . PHP_EOL . PHP_EOL . self::strArrayToCode($strArray),
                            ];
                        }
                        $new = strtotime($value);
                        $new = date('Y-m-d H:i:s', $new);
                    }
                } else {
                    $number = $value;
                    if (is_numeric($value)) {
                        $number *= 86400;
                        $new = time() + $number;
                        $new = date('Y-m-d H:i:s', (int) $new);
                    } else {
                        if (strtotime($value) === false) {
                            return [
                                'ok' => false,
                                'msg' => '处理出错，不支持的写法.' . PHP_EOL . PHP_EOL . self::strArrayToCode($strArray),
                            ];
                        }
                        $new = strtotime($value);
                        $new = date('Y-m-d H:i:s', $new);
                    }
                }
                $User->$useOptionMethod = $new;
                break;
                // ##############
            case 'method':
            case 'passwd':
            case 'money':
                $strArray = [
                    '// 参数值中不允许有空格，结果会含小数 2 位',
                    '// +2  —— 增加余额',
                    '// -2  —— 减少余额',
                    '// *2  —— 以当前余额做乘法',
                    '// /2  —— 以当前余额做除法',
                ];
                $value = explode(' ', $value)[0];
                $new = self::computingMethod($User->$useOptionMethod, $value, true);
                if ($new === null) {
                    return [
                        'ok' => false,
                        'msg' => '处理出错，不支持的写法.'  . PHP_EOL . PHP_EOL . self::strArrayToCode($strArray),
                    ];
                }
                $User->$useOptionMethod = $new;
                break;
                // ##############
            case 'class':
            case 'invite_num':
            case 'node_group':
            case 'node_iplimit':
            case 'node_speedlimit':
                $strArray = [
                    '// 参数值中不允许有空格',
                    '// +2  —— 增加值',
                    '// -2  —— 减少值',
                    '// *2  —— 以当前值做乘法',
                    '// /2  —— 以当前值做除法',
                ];
                $value = explode(' ', $value)[0];
                $new = self::computingMethod($User->$useOptionMethod, $value);
                if ($new === null) {
                    return [
                        'ok' => false,
                        'msg' => '处理出错，不支持的写法.'  . PHP_EOL . PHP_EOL . self::strArrayToCode($strArray),
                    ];
                }
                $User->$useOptionMethod = $new;
                break;
                // ##############
            default:
                return [
                    'ok' => false,
                    'msg' => '尚不支持.',
                ];
        }
        if ($User->save()) {
            if ($useOptionMethod === 'money') {
                $diff = $new - $old;
                $remark = ($diff > 0 ? '管理员添加余额' : '管理员扣除余额');
                (new UserMoneyLog())->addMoneyLog($User->id, (float) $old, (float) $new, (float) $diff, $remark);
            }
            $strArray = [
                '目标用户：' . $Email,
                '被修改项：' . $useOptionMethodName . '[' . $useOptionMethod . ']',
                '修改前为：' . $old,
                '修改后为：' . $new,
            ];
            return [
                'ok' => true,
                'msg' => self::strArrayToCode($strArray),
            ];
        }
        return [
            'ok' => false,
            'msg' => '保存出错',
        ];
    }

    /**
     * 获取用户邮箱
     *
     * @param string $email  邮箱
     * @param int    $ChatID 会话 ID
     */
    public static function getUserEmail(string $email, int $ChatID): string
    {
        if (Setting::obtain('enable_user_email_group_show') || $ChatID > 0) {
            return $email;
        }
        $a = strpos($email, '@');
        if ($a === false) {
            return $email;
        }
        $string = substr($email, $a);
        return $a === 1 ? '*' . $string : substr($email, 0, 1) . str_pad('', $a - 1, '*') . $string;
    }

    /**
     * 分割字符串
     *
     * @param string $Str       源字符串
     * @param string $Delimiter 分割定界符
     * @param int    $Quantity  最大返回数量
     *
     * @return array
     */
    public static function strExplode(string $Str, string $Delimiter, int $Quantity = 10): array
    {
        $return = [];
        $Str = trim($Str);
        for ($x = 0; $x <= 10; $x++) {
            if (str_contains($Str, $Delimiter) && count($return) < $Quantity - 1) {
                $temp = substr($Str, 0, strpos($Str, $Delimiter));
                $return[] = $temp;
                $Str = trim(substr($Str, strlen($temp)));
            } else {
                $return[] = $Str;
                break;
            }
        }
        return $return;
    }

    /**
     * 查找字符串是否是某个方法的别名
     *
     * @param array  $MethodGroup 方法别名的数组
     * @param string $Search      被搜索的字符串
     */
    public static function getOptionMethod(array $MethodGroup, string $Search): string
    {
        $useMethod = '';
        foreach ($MethodGroup as $MethodName => $Remarks) {
            if (strlen($MethodName) === strlen($Search) && stripos($MethodName, $Search) === 0) {
                $useMethod = $MethodName;
                break;
            }
            if (count($Remarks) >= 1) {
                foreach ($Remarks as $Remark) {
                    if (strlen($Remark) === strlen($Search) && stripos($Remark, $Search) === 0) {
                        $useMethod = $MethodName;
                        break 2;
                    }
                }
            }
        }
        return $useMethod;
    }

    /**
     * 使用 $Value 给定的运算式与 $Source 计算结果
     *
     * @param string $Source         源数值
     * @param string $Value          运算式含增改数值
     * @param bool   $FloatingNumber 是否格式化为浮点数
     */
    public static function computingMethod(string $Source, string $Value, bool $FloatingNumber = false): ?string
    {
        if (
            (str_starts_with($Value, '+')
                ||
                str_starts_with($Value, '-')
                ||
                str_starts_with($Value, '*')
                ||
                str_starts_with($Value, '/'))
            &&
            is_numeric(substr($Value, 1))
        ) {
            $Source = match (substr($Value, 0, 1)) {
                '+' => (int) $Source + (int) substr($Value, 1),
                '-' => (int) $Source - (int) substr($Value, 1),
                '*' => (int) $Source * (int) substr($Value, 1),
                '/' => (int) $Source / (int) substr($Value, 1),
                default => null,
            };
        } else {
            if (is_numeric($Value)) {
                $Source = $Value;
            } else {
                $Source = null;
            }
        }
        if ($Source !== null) {
            $Source = ($FloatingNumber === false
                ? number_format($Source, 0, '.', '')
                : number_format($Source, 2, '.', ''));
        }
        return $Source;
    }

    /**
     * 使用 $Value 给定的运算式及流量单位与 $Source 计算结果
     *
     * @param string $Source 源数值
     * @param string $Value  运算式含增改数值
     */
    public static function trafficMethod(string $Source, string $Value): ?int
    {
        if (
            str_starts_with($Value, '+')
            ||
            str_starts_with($Value, '-')
            ||
            str_starts_with($Value, '*')
            ||
            str_starts_with($Value, '/')
        ) {
            $operator = substr($Value, 0, 1);
            if (! in_array($operator, ['*', '/'])) {
                $number = Tools::autoBytesR(substr($Value, 1));
            } else {
                $number = substr($Value, 1, strlen($Value) - 1);
                if (! is_numeric($number)) {
                    return null;
                }
            }
            if ($number === null) {
                return null;
            }

            $Source = match ($operator) {
                '+' => (int) $Source + (int) $number,
                '-' => (int) $Source - (int) $number,
                '*' => (int) $Source * (int) $number,
                '/' => (int) $Source / (int) $number,
                default => null,
            };
        } else {
            if (is_numeric($Value)) {
                if ((int) $Value === 0) {
                    $Source = 0;
                } else {
                    $Source = Tools::autoBytesR($Value . 'KB');
                }
            } else {
                $Source = Tools::autoBytesR($Value);
            }
        }
        return $Source;
    }

    /**
     * 字符串数组转 TG HTML 等宽字符串
     *
     * @param array $strArray 字符串数组
     */
    public static function strArrayToCode(array $strArray): string
    {
        return implode(
            PHP_EOL,
            array_map(
                static function ($item) {
                    return '<code>' . $item . '</code>';
                },
                $strArray
            )
        );
    }
}
