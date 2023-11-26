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
        $paylist = new Paylist();
        $number = match ($req) {
            'today' => $paylist->where('status', 1)
                ->whereBetween('datetime', [$today, time()])
                ->sum('total'),
            'yesterday' => $paylist->where('status', 1)
                ->whereBetween('datetime', [strtotime('-1 day', $today), $today])
                ->sum('total'),
            'this month' => $paylist->where('status', 1)
                ->whereBetween('datetime', [strtotime('first day of this month 00:00:00'), time()])
                ->sum('total'),
            default => $paylist->where('status', 1)->sum('total'),
        };

        return is_null($number) ? 0.00 : round(floatval($number), 2);
    }

    public static function getTotalUser(): int
    {
        return (new User())->count();
    }

    public static function getCheckinUser(): int
    {
        return (new User())->where('last_check_in_time', '>', 0)->count();
    }

    public static function getTodayCheckinUser(): int
    {
        return (new User())->where('last_check_in_time', '>', strtotime('today'))->count();
    }

    public static function getTrafficUsage(): string
    {
        return Tools::autoBytes((new User())->sum('u') + (new User())->sum('d'));
    }

    public static function getTodayTrafficUsage(): string
    {
        return Tools::autoBytes((new User())->sum('transfer_today'));
    }

    public static function getRawTodayTrafficUsage()
    {
        return (new User())->sum('transfer_today');
    }

    public static function getRawGbTodayTrafficUsage(): float
    {
        return Tools::flowToGB((new User())->sum('transfer_today'));
    }

    public static function getLastTrafficUsage(): string
    {
        return Tools::autoBytes((new User())->sum('u') + (new User())->sum('d') - (new User())->sum('transfer_today'));
    }

    public static function getRawLastTrafficUsage()
    {
        return (new User())->sum('u') + (new User())->sum('d') - (new User())->sum('transfer_today');
    }

    public static function getRawGbLastTrafficUsage(): float
    {
        return Tools::flowToGB((new User())->sum('u') + (new User())->sum('d') - (new User())->sum('transfer_today'));
    }

    public static function getUnusedTrafficUsage(): string
    {
        return Tools::autoBytes((new User())->sum('transfer_enable') - (new User())->sum('u') - (new User())->sum('d'));
    }

    public static function getRawUnusedTrafficUsage()
    {
        return (new User())->sum('transfer_enable') - (new User())->sum('u') - (new User())->sum('d');
    }

    public static function getRawGbUnusedTrafficUsage(): float
    {
        return Tools::flowToGB((new User())->sum('transfer_enable') - (new User())->sum('u') - (new User())->sum('d'));
    }

    public static function getTotalTraffic(): string
    {
        return Tools::autoBytes((new User())->sum('transfer_enable'));
    }

    public static function getRawTotalTraffic()
    {
        return (new User())->sum('transfer_enable');
    }

    public static function getRawGbTotalTraffic(): float
    {
        return Tools::flowToGB((new User())->sum('transfer_enable'));
    }

    public static function getTotalNode(): int
    {
        return (new Node())->where('node_heartbeat', '>', 0)->count();
    }

    public static function getAliveNode(): int
    {
        return (new Node())->where('node_heartbeat', '>', time() - 90)->count();
    }

    public static function getInactiveUser(): int
    {
        return (new User())->where('is_inactive', 1)->count();
    }

    public static function getActiveUser(): int
    {
        return (new User())->where('is_inactive', 0)->count();
    }
}
