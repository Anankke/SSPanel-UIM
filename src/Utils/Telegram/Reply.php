<?php

declare(strict_types=1);

namespace App\Utils\Telegram;

use App\Models\User;

final class Reply
{
    /**
     * 用户的流量使用讯息
     */
    public static function getUserTrafficInfo(User $user): string
    {
        $text = [
            '你当前的流量状况：',
            '',
            '今日已使用 ' . $user->todayUsedTrafficPercent() . '% ：' . $user->todayUsedTraffic(),
            '之前已使用 ' . $user->lastUsedTrafficPercent() . '% ：' . $user->lastUsedTraffic(),
            '流量约剩余 ' . $user->unusedTrafficPercent() . '% ：' . $user->unusedTraffic(),
        ];
        return implode(PHP_EOL, $text);
    }

    /**
     * 用户基本讯息
     */
    public static function getUserInfo(User $user): string
    {
        $text = [
            '当前余额：' . $user->money,
            '在线 IP 数：' . ($user->node_iplimit !== 0 ? $user->onlineIpCount() . ' / ' . $user->node_iplimit : $user->onlineIpCount() . ' / 不限制'),
            '端口速率：' . ($user->node_speedlimit > 0 ? $user->node_speedlimit . 'Mbps' : '不限制'),
            '上次使用：' . $user->lastUseTime(),
            '过期时间：' . $user->class_expire,
        ];
        return implode(PHP_EOL, $text);
    }

    /**
     * 获取用户或管理的尊称
     */
    public static function getUserTitle(User $user): string
    {
        if ($user->class > 0) {
            $text = '尊敬的 VIP ' . $user->class . ' 你好：';
        } else {
            $text = '尊敬的用户你好：';
        }
        return $text;
    }

    /**
     * [admin]获取用户信息
     */
    public static function getUserInfoFromAdmin(User $user, int $ChatID): string
    {
        $strArray = [
            '#' . $user->id . ' ' . $user->user_name . ' 的用户信息',
            '',
            '用户邮箱：' . TelegramTools::getUserEmail($user->email, $ChatID),
            '账户余额：' . $user->money,
            '账户状态：' . ((int) $user->is_banned === 1 ? '封禁' : '正常'),
            '用户等级：' . $user->class,
            '剩余流量：' . $user->unusedTraffic(),
            '等级到期：' . $user->class_expire,
            '账户到期：' . $user->expire_in,
        ];
        return implode(PHP_EOL, $strArray);
    }
}
