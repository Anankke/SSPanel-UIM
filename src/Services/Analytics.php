<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Node;
use App\Models\Paylist;
use App\Models\User;
use App\Utils\Tools;
use function floatval;
use function is_null;
use function round;
use function strtotime;
use function time;

final class Analytics
{
    /**
     * 获取累计收入
     *
     * @param string $req
     *
     * @return float
     */
    public static function getIncome(string $req): float
    {
        $today = strtotime('00:00:00');
        $number = match ($req) {
            'today' => Paylist::where('status', 1)
                ->whereBetween('datetime', [$today, time()])
                ->sum('total'),
            'yesterday' => Paylist::where('status', 1)
                ->whereBetween('datetime', [strtotime('-1 day', $today), $today])
                ->sum('total'),
            'this month' => Paylist::where('status', 1)
                ->whereBetween('datetime', [strtotime('first day of this month 00:00:00'), time()])
                ->sum('total'),
            default => Paylist::where('status', 1)->sum('total'),
        };

        return is_null($number) ? 0.00 : round(floatval($number), 2);
    }

    public static function getTotalUser()
    {
        return User::count();
    }

    public static function getCheckinUser()
    {
        return User::where('last_check_in_time', '>', 0)->count();
    }

    public static function getTodayCheckinUser()
    {
        return User::where('last_check_in_time', '>', strtotime('today'))->count();
    }

    public static function getTrafficUsage(): string
    {
        return Tools::autoBytes(User::sum('u') + User::sum('d'));
    }

    public static function getTodayTrafficUsage(): string
    {
        return Tools::autoBytes(User::sum('transfer_today'));
    }

    public static function getRawTodayTrafficUsage()
    {
        return User::sum('transfer_today');
    }

    public static function getRawGbTodayTrafficUsage(): float
    {
        return Tools::flowToGB(User::sum('transfer_today'));
    }

    public static function getLastTrafficUsage(): string
    {
        return Tools::autoBytes(User::sum('u') + User::sum('d') - User::sum('transfer_today'));
    }

    public static function getRawLastTrafficUsage()
    {
        return User::sum('u') + User::sum('d') - User::sum('transfer_today');
    }

    public static function getRawGbLastTrafficUsage(): float
    {
        return Tools::flowToGB(User::sum('u') + User::sum('d') - User::sum('transfer_today'));
    }

    public static function getUnusedTrafficUsage(): string
    {
        return Tools::autoBytes(User::sum('transfer_enable') - User::sum('u') - User::sum('d'));
    }

    public static function getRawUnusedTrafficUsage()
    {
        return User::sum('transfer_enable') - User::sum('u') - User::sum('d');
    }

    public static function getRawGbUnusedTrafficUsage(): float
    {
        return Tools::flowToGB(User::sum('transfer_enable') - User::sum('u') - User::sum('d'));
    }

    public static function getTotalTraffic(): string
    {
        return Tools::autoBytes(User::sum('transfer_enable'));
    }

    public static function getRawTotalTraffic()
    {
        return User::sum('transfer_enable');
    }

    public static function getRawGbTotalTraffic(): float
    {
        return Tools::flowToGB(User::sum('transfer_enable'));
    }

    public static function getTotalNode()
    {
        return Node::where('node_heartbeat', '>', 0)->count();
    }

    public static function getAliveNode()
    {
        return Node::where('node_heartbeat', '>', time() - 90)->count();
    }

    public static function getInactiveUser()
    {
        return User::where('is_inactive', 1)->count();
    }

    public static function getActiveUser()
    {
        return User::where('is_inactive', 0)->count();
    }
}
