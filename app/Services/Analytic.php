<?php

namespace App\Services;

use App\Models\User;
use App\Utils\Tools;

class Analytic
{
    public function userCount()
    {
        return  User::all()->count();
    }

    public function checkinUserCount()
    {
        return User::where('last_checkin_time', '>', 1)->count();
    }

    public function activedUserCount()
    {
        return User::where('t', '>', 1)->count();
    }

    public function totalTraffic()
    {
        $u = User::all()->sum('u');
        $d = User::all()->sum('d');
        return Tools::flowAutoShow($u + $d);
    }
}
