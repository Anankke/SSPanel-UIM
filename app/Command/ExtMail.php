<?php


namespace App\Command;

use App\Models\User;
use App\Services\Config;
use App\Services\Mail;

class ExtMail
{
    public static function sendNoMail()
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->t==0) {
                echo "Send daily mail to user: ".$user->id;
                $subject = Config::get('appName')."-期待您的回归";
                $to = $user->email;
                $text = "似乎您在".Config::get('appName')."上的流量一直是 0 呢(P.S:也可能是您没有使用 ss 而使用了其他还不能计入流量的方式....)，如果您在使用上遇到了任何困难，请不要犹豫，登录到".Config::get('appName').",您就会知道如何使用了，特别是对于 iOS 用户，最近在使用的优化上大家都付出了很多的努力。期待您的回归～" ;
                try {
                    Mail::send($to, $subject, 'ext/back.tpl', [
                    "user" => $user,"text" => $text
                ], [
                ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    public static function sendOldMail()
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->t!=0&&$user->t<1451577599) {
                echo "Send daily mail to user: ".$user->id;
                $subject = Config::get('appName')."-期待您的回归";
                $to = $user->email;
                $text = "似乎您在 2017 年以来就没有使用过".Config::get('appName')."了呢，如果您在使用上遇到了任何困难，请不要犹豫，登录到".Config::get('appName')."，您就会知道如何使用了，特别是对于 iOS 用户，最近在使用的优化上大家都付出了很多的努力。期待您的回归～" ;
                try {
                    Mail::send($to, $subject, 'ext/back.tpl', [
                        "user" => $user,"text" => $text
                    ], [
                    ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }
}
