<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Paylist;
use App\Models\User;
use App\Utils\Tools;
use function count;

final class FinanceMail extends Command
{
    public string $description = <<<EOL
├─=: php xcat FinanceMail [选项]
│ ├─ day                     - 日报
│ ├─ week                    - 周报
│ ├─ month                   - 月报
EOL;

    public function boot(): void
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

    public function day(): void
    {
        $today = strtotime('00:00:00');
        $paylists = Paylist::where('status', 1)->whereBetween('datetime', [strtotime('-1 day', $today), $today])->get();
        $text_html = '<table border=1><tr><td>金额</td><td>用户ID</td><td>用户名</td><td>充值时间</td>';

        foreach ($paylists as $paylist) {
            $text_html .= '<tr>';
            $text_html .= '<td>' . $paylist->total . '</td>';
            $text_html .= '<td>' . $paylist->userid . '</td>';
            $text_html .= '<td>' . User::find($paylist->userid)->user_name . '</td>';
            $text_html .= '<td>' . Tools::toDateTime((int) $paylist->datetime) . '</td>';
            $text_html .= '</tr>';
        }

        $text_html .= '</table>';
        $text_html .= '<br>昨日总收入笔数：' . count($paylists) . '<br>昨日总收入金额：' . $paylists->sum('total');
        $adminUser = User::where('is_admin', '=', '1')->get();

        foreach ($adminUser as $user) {
            echo 'Sending email to admin user: ' . $user->id . PHP_EOL;
            $user->sendMail(
                $_ENV['appName'] . '-财务日报',
                'news/finance.tpl',
                [
                    'title' => '财务日报',
                    'text' => $text_html,
                ],
                []
            );
        }
    }

    public function week(): void
    {
        $today = strtotime('00:00:00');
        $paylists = Paylist::where('status', 1)->whereBetween('datetime', [strtotime('-1 week', $today), $today])->get();

        $text_html = '<br>上周总收入笔数：' . count($paylists) . '<br>上周总收入金额：' . $paylists->sum('total');
        $adminUser = User::where('is_admin', '=', '1')->get();

        foreach ($adminUser as $user) {
            echo 'Sending email to admin user: ' . $user->id . PHP_EOL;
            $user->sendMail(
                $_ENV['appName'] . '-财务周报',
                'news/finance.tpl',
                [
                    'title' => '财务周报',
                    'text' => $text_html,
                ],
                []
            );
        }
    }

    public function month(): void
    {
        $today = strtotime('00:00:00');
        $paylists = Paylist::where('status', 1)->whereBetween('datetime', [strtotime('-1 month', $today), $today])->get();

        $text_html = '<br>上月总收入笔数：' . count($paylists) . '<br>上月总收入金额：' . $paylists->sum('total');
        $adminUser = User::where('is_admin', '=', '1')->get();

        foreach ($adminUser as $user) {
            echo 'Sending email to admin user: ' . $user->id . PHP_EOL;
            $user->sendMail(
                $_ENV['appName'] . '-财务月报',
                'news/finance.tpl',
                [
                    'title' => '财务月报',
                    'text' => $text_html,
                ],
                []
            );
        }
    }
}
