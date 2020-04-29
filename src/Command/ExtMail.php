<?php


namespace App\Command;

use App\Models\User;

class ExtMail extends Command
{
    public $description = ''
        . '├─=: php xcat ExtMail [选项]' . PHP_EOL
        . '│ ├─ sendNoMail ' . PHP_EOL
        . '│ ├─ sendOldMail' . PHP_EOL;

    public function boot()
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                echo '方法不存在.' . PHP_EOL;
            }
        }
    }

    public function sendNoMail()
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->t == 0) {
                echo 'Send daily mail to user: ' . $user->id;
                $user->sendMail(
                    $_ENV['appName'] . '-期待您的回归',
                    'ext/back.tpl',
                    [
                        'text' => '似乎您在' . $_ENV['appName'] . '上的流量一直是 0 呢(P.S:也可能是您没有使用 ss 而使用了其他还不能计入流量的方式....)，如果您在使用上遇到了任何困难，请不要犹豫，登录到' . $_ENV['appName'] . ',您就会知道如何使用了，特别是对于 iOS 用户，最近在使用的优化上大家都付出了很多的努力。期待您的回归～'
                    ],
                    []
                );
            }
        }
    }

    public function sendOldMail()
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->t != 0 && $user->t < 1451577599) {
                echo 'Send daily mail to user: ' . $user->id;
                $user->sendMail(
                    $_ENV['appName'] . '-期待您的回归',
                    'ext/back.tpl',
                    [
                        'text' => '似乎您在 2017 年以来就没有使用过' . $_ENV['appName'] . '了呢，如果您在使用上遇到了任何困难，请不要犹豫，登录到' . $_ENV['appName'] . '，您就会知道如何使用了，特别是对于 iOS 用户，最近在使用的优化上大家都付出了很多的努力。期待您的回归～'
                    ],
                    []
                );
            }
        }
    }
}
