<?php

namespace App\Utils\Telegram;

use App\Models\Bought;

class Reply
{
    /**
     * 用户的流量使用讯息
     *
     * @param \App\Models\User $user
     *
     * @return string
     */
    public static function getUserTrafficInfo($user)
    {
        $text = [
            '您当前的流量状况：',
            '',
            '今日已使用 ' . $user->TodayusedTrafficPercent() . '% ：' . $user->TodayusedTraffic(),
            '之前已使用 ' . $user->LastusedTrafficPercent() . '% ：' . $user->LastusedTraffic(),
            '流量约剩余 ' . $user->unusedTrafficPercent() . '% ：' . $user->unusedTraffic(),
        ];
        return implode(PHP_EOL, $text);
    }

    /**
     * 用户基本讯息
     *
     * @param \App\Models\User $user
     *
     * @return string
     */
    public static function getUserInfo($user)
    {
        $text = [
            '当前余额：' . $user->money,
            '在线设备：' . ($user->node_connector != 0 ? $user->online_ip_count() . ' / ' . $user->node_connector : $user->online_ip_count() . ' / 不限制'),
            '端口速率：' . ($user->node_speedlimit != 0 ? $user->node_speedlimit . 'Mbps' : '无限制'),
            '上次使用：' . $user->lastSsTime(),
            '过期时间：' . $user->class_expire,
        ];
        return implode(PHP_EOL, $text);
    }

    /**
     * 获取用户或管理的尊称
     *
     * @param \App\Models\User $user
     *
     * @return string
     */
    public static function getUserTitle($user)
    {
        if ($user->class > 0) {
            $text = '尊敬的 VIP ' . $user->class . ' 您好：';
        } else {
            $text = '尊敬的用户您好：';
        }
        return $text;
    }

    /**
     * [admin]获取用户购买记录
     *
     * @param \App\Models\User $user
     *
     * @return array
     */
    public static function getUserBoughts($user)
    {
        $boughts = Bought::where('userid', $user->id)->orderBy('id', 'desc')->get();
        $data = [];
        foreach ($boughts as $bought) {
            $shop = $bought->shop();
            if ($shop == null) {
                $bought->delete();
                continue;
            }
            if ($bought->valid()) {
                $strArray = [];
                $strArray[] = ' - 商品套餐名称：' . $shop->name;
                $strArray[] = ' - 套餐购买时间：' . $bought->datetime();
                $strArray[] = ' - 套餐自动续费：' . ($bought->renew == 0 ? '不自动续费' : $bought->renew_date());
                $strArray[] = ' - 下次流量重置：' . $bought->reset_time();
                $strArray[] = ' - 套餐过期时间：' . $bought->exp_time();
                $strArray[] = '';
                $data[] = implode(PHP_EOL, $strArray);
            }
        }
        return $data;
    }

    /**
     * [admin]获取用户信息
     *
     * @param \App\Models\User $user
     * @param int              $ChatID
     *
     * @return string
     */
    public static function getUserInfoFromAdmin($user, $ChatID)
    {
        $strArray = [
            '#' . $user->id . ' ' . $user->user_name . ' 的用户信息',
            '',
            '用户邮箱：' . TelegramTools::getUserEmail($user->email, $ChatID),
            '账户余额：' . $user->money,
            '是否启用：' . ((int) $user->enable == 1 ? '启用' : '禁用'),
            '用户等级：' . $user->class,
            '剩余流量：' . $user->unusedTraffic(),
            '等级到期：' . $user->class_expire,
            '账户到期：' . $user->expire_in,
            '套餐详情：'
        ];
        $boughts = self::getUserBoughts($user);
        if (count($boughts) != 0) {
            $strArray = array_merge($strArray, $boughts);
        } else {
            $strArray[] = ' - 该用户无生效套餐.';
        }
        return implode(PHP_EOL, $strArray);
    }
}
