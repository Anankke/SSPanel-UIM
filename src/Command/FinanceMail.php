<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\User;
use App\Services\Config;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;

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
        $datatables = new Datatables(new MySQL(Config::getDbConfig()));
        $datatables->query(
            'SELECT code.number, code.userid, code.usedatetime FROM code 
            WHERE TO_DAYS(NOW()) - TO_DAYS(code.usedatetime) = 1 
            AND code.type = -1 
            AND code.isused = 1'
        );
        $text_array = $datatables->generate()->toArray();
        $codes = $text_array['data'];
        $text_html = '<table border=1><tr><td>金额</td><td>用户ID</td><td>用户名</td><td>充值时间</td>';
        $income_total = 0.00;
        foreach ($codes as $code) {
            $text_html .= '<tr>';
            $text_html .= '<td>' . $code[0] . '</td>';
            $text_html .= '<td>' . $code[1] . '</td>';
            $user = User::find($code[1]);
            $text_html .= '<td>' . $user->user_name . '</td>';
            $text_html .= '<td>' . $code[2] . '</td>';
            $text_html .= '</tr>';
            $income_total += $code[0];
        }

        $text_html .= '</table>';
        $text_html .= '<br>昨日总收入笔数：' . $text_array['recordsTotal'] . '<br>昨日总收入金额：' . $income_total;

        $adminUser = User::where('is_admin', '=', '1')->get();
        foreach ($adminUser as $user) {
            echo 'Send offline mail to user: ' . $user->id;
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
        $datatables = new Datatables(new MySQL(Config::getDbConfig()));
        $datatables->query(
            'SELECT code.number FROM code 
            WHERE DATEDIFF(NOW(),code.usedatetime) <= 7 
            AND DATEDIFF(NOW(),code.usedatetime) >= 1 
            AND code.isused = 1'
        );
        //每周的第一天是周日，因此统计周日～周六的七天
        $text_array = $datatables->generate()->toArray();
        $codes = $text_array['data'];
        $text_html = '';
        $income_total = 0.00;
        foreach ($codes as $code) {
            $income_total += $code[0];
        }

        $text_html .= '<br>上周总收入笔数：' . $text_array['recordsTotal'] . '<br>上周总收入金额：' . $income_total;

        $adminUser = User::where('is_admin', '=', '1')->get();
        foreach ($adminUser as $user) {
            echo 'Send offline mail to user: ' . $user->id;
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
        $datatables = new Datatables(new MySQL(Config::getDbConfig()));
        $datatables->query(
            'SELECT code.number FROM code 
            WHERE DATE_FORMAT(code.usedatetime,\'%Y-%m\')=DATE_FORMAT(date_sub(curdate(), interval 1 month),\'%Y-%m\') 
            AND code.type = -1 
            AND code.isused = 1'
        );
        $text_array = $datatables->generate()->toArray();
        $codes = $text_array['data'];
        $text_html = '';
        $income_total = 0.00;
        foreach ($codes as $code) {
            $income_total += $code[0];
        }
        $text_html .= '<br>上月总收入笔数：' . $text_array['recordsTotal'] . '<br>上月总收入金额：' . $income_total;

        $adminUser = User::where('is_admin', '=', '1')->get();
        foreach ($adminUser as $user) {
            echo 'Send offline mail to user: ' . $user->id;
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
