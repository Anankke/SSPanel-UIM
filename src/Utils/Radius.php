<?php

namespace App\Utils;

use App\Models\User;
use App\Models\RadiusRadCheck;
use App\Models\RadiusRadUserGroup;
use App\Models\RadiusNas;
use App\Models\RadiusBan;
use App\Services\Config;

class Radius
{

    /**
     * 添加或者更新密码信息
     */
    public static function Add($user, $pwd)
    {
        if ($_ENV['enable_radius'] == true) {
            $email = $user->email;
            $email = str_replace(array('@', '.'), '', $email);

            $exists = RadiusRadCheck::where('username', $email)->first();

            if ($exists == null) {
                $rb = RadiusBan::where('userid', $user->id)->first();
                if ($rb != null) {
                    return;
                }

                $newRad = new RadiusRadCheck();
                $newRad->username = $email;
                $newRad->attribute = 'Cleartext-Password';
                $newRad->op = ':=';
                $newRad->value = $pwd;
                $newRad->save();

                $newRad = new RadiusRadUserGroup();
                $newRad->username = $email;
                $newRad->groupname = 'user';
                $newRad->priority = '0';
                $newRad->save();
            } else {
                $exists->value = $pwd;
                $exists->save();
            }
        }
    }


    public static function Delete($email)
    {
        if ($_ENV['enable_radius'] == true) {
            $email = str_replace(array('@', '.'), '', $email);


            $exists = RadiusRadCheck::where('username', $email)->first();

            if ($exists != null) {
                RadiusRadCheck::where('username', $email)->delete();
                RadiusRadUserGroup::where('username', $email)->delete();
            }
        }
    }

    public static function ChangeUserName($origin_email, $new_email, $passwd)
    {
        if ($_ENV['enable_radius'] == true) {
            $email1 = str_replace(array('@', '.'), '', $origin_email);
            $email2 = str_replace(array('@', '.'), '', $new_email);

            $exists = RadiusRadCheck::where('username', $email1)->first();

            if ($exists != null) {
                $exists->username = $email2;
                $exists->value = $passwd;
                $exists->save();

                $exists = RadiusRadUserGroup::where('username', $email1)->first();
                $exists->username = $email2;
                $exists->save();
            } else {
                $user = User::where('email', '=', $origin_email)->first();
                $rb = RadiusBan::where('userid', $user->id)->first();
                if ($rb != null) {
                    return;
                }

                $newRad = new RadiusRadCheck();
                $newRad->username = $email2;
                $newRad->attribute = 'Cleartext-Password';
                $newRad->op = ':=';
                $newRad->value = $passwd;
                $newRad->save();

                $newRad = new RadiusRadUserGroup();
                $newRad->username = $email2;
                $newRad->groupname = 'user';
                $newRad->priority = '0';
                $newRad->save();
            }
        }
    }

    public static function AddNas($ip, $name)
    {
        if ($_ENV['enable_radius'] == true) {
            $exists = RadiusNas::where('shortname', $ip)->first();
            if ($exists == null) {
                $exists = new RadiusNas();
                $exists->nasname = $ip;
                $exists->shortname = $ip;
                $exists->type = 'other';
                $exists->secret = $_ENV['radius_secret'];
                $exists->description = $ip;
                $exists->save();
            }
        }
    }

    public static function DelNas($ip)
    {
        if ($_ENV['enable_radius'] == true) {
            RadiusNas::where('shortname', $ip)->delete();
        }
    }

    public static function GetUserName($email)
    {
        return str_replace(array('@', '.'), '', $email);
    }
}
