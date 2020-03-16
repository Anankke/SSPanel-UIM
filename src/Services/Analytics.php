<?php

namespace App\Services;

use App\Models\User;
use App\Models\Node;
use App\Utils\Tools;

class Analytics
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

    public function getTrafficUsage()
    {
        $total = User::sum('u') + User::sum('d');
        return Tools::flowAutoShow($total);
    }

    public function getTodayTrafficUsage()
    {
        $total = User::sum('u') + User::sum('d') - User::sum('last_day_t');
        return Tools::flowAutoShow($total);
    }


    public function getRawTodayTrafficUsage()
    {
        return User::sum('u') + User::sum('d') - User::sum('last_day_t');
    }

    public function getLastTrafficUsage()
    {
        $total = User::sum('last_day_t');
        return Tools::flowAutoShow($total);
    }


    public function getRawLastTrafficUsage()
    {
        return User::sum('last_day_t');
    }

    public function getUnusedTrafficUsage()
    {
        $total = User::sum('transfer_enable') - User::sum('u') - User::sum('d');
        return Tools::flowAutoShow($total);
    }

    public function getRawUnusedTrafficUsage()
    {
        return User::sum('transfer_enable') - User::sum('u') - User::sum('d');
    }


    public function getTotalTraffic()
    {
        $total = User::sum('transfer_enable');
        return Tools::flowAutoShow($total);
    }

    public function getRawTotalTraffic()
    {
        return User::sum('transfer_enable');
    }

    public function getOnlineUser($time)
    {
        $time = time() - $time;
        return User::where('t', '>', $time)->count();
    }

    public function getUnusedUser()
    {
        return User::where('t', '=', 0)->count();
    }

    public function getTotalNode()
    {
        return Node::count();
    }

    public function getTotalNodes()
    {
        return Node::where('node_heartbeat', '>', 0)->where(
            static function ($query) {
                $query->Where('sort', '=', 0)
                    ->orWhere('sort', '=', 10)
                    ->orWhere('sort', '=', 11)
                    ->orWhere('sort', '=', 12)
                    ->orWhere('sort', '=', 13);
            }
        )->count();
    }

    public function getAliveNodes()
    {
        return Node::where(
            static function ($query) {
                $query->Where('sort', '=', 0)
                    ->orWhere('sort', '=', 10)
                    ->orWhere('sort', '=', 11)
                    ->orWhere('sort', '=', 12)
                    ->orWhere('sort', '=', 13);
            }
        )->where('node_heartbeat', '>', time() - 90)->count();
    }
}
