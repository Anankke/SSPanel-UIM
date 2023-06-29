<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Node;
use App\Models\User;
use App\Utils\Tools;
use function strtotime;
use function time;

final class Analytics
{
    public function getTotalUser()
    {
        return User::count();
    }

    public function getCheckinUser()
    {
        return User::where('last_check_in_time', '>', 0)->count();
    }

    public function getTodayCheckinUser()
    {
        return User::where('last_check_in_time', '>', strtotime('today'))->count();
    }

    public function getTrafficUsage(): string
    {
        return Tools::autoBytes(User::sum('u') + User::sum('d'));
    }

    public function getTodayTrafficUsage(): string
    {
        return Tools::autoBytes(User::sum('transfer_today'));
    }

    public function getRawTodayTrafficUsage()
    {
        return User::sum('transfer_today');
    }

    public function getRawGbTodayTrafficUsage(): float
    {
        return Tools::flowToGB(User::sum('transfer_today'));
    }

    public function getLastTrafficUsage(): string
    {
        return Tools::autoBytes(User::sum('u') + User::sum('d') - User::sum('transfer_today'));
    }

    public function getRawLastTrafficUsage()
    {
        return User::sum('u') + User::sum('d') - User::sum('transfer_today');
    }

    public function getRawGbLastTrafficUsage(): float
    {
        return Tools::flowToGB(User::sum('u') + User::sum('d') - User::sum('transfer_today'));
    }

    public function getUnusedTrafficUsage(): string
    {
        return Tools::autoBytes(User::sum('transfer_enable') - User::sum('u') - User::sum('d'));
    }

    public function getRawUnusedTrafficUsage()
    {
        return User::sum('transfer_enable') - User::sum('u') - User::sum('d');
    }

    public function getRawGbUnusedTrafficUsage(): float
    {
        return Tools::flowToGB(User::sum('transfer_enable') - User::sum('u') - User::sum('d'));
    }

    public function getTotalTraffic(): string
    {
        return Tools::autoBytes(User::sum('transfer_enable'));
    }

    public function getRawTotalTraffic()
    {
        return User::sum('transfer_enable');
    }

    public function getRawGbTotalTraffic(): float
    {
        return Tools::flowToGB(User::sum('transfer_enable'));
    }

    public function getOnlineUser($time)
    {
        $time = time() - $time;
        return User::where('last_use_time', '>', $time)->count();
    }

    public function getUnusedUser()
    {
        return User::where('last_use_time', '=', 0)->count();
    }

    public function getTotalNode()
    {
        return Node::count();
    }

    public function getTotalNodes()
    {
        return Node::where('node_heartbeat', '>', 0)->where(
            static function ($query): void {
                $query->Where('sort', '=', 0)
                    ->orWhere('sort', '=', 11)
                    ->orWhere('sort', '=', 14);
            }
        )->count();
    }

    public function getAliveNodes()
    {
        return Node::where(
            static function ($query): void {
                $query->Where('sort', '=', 0)
                    ->orWhere('sort', '=', 11)
                    ->orWhere('sort', '=', 14);
            }
        )->where('node_heartbeat', '>', time() - 90)->count();
    }
}
