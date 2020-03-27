<?php


namespace App\Command;

use App\Models\User;
use App\Utils\Telegram;
use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

class FinanceMail
{
    public static function sendFinanceMail_day()
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query(
            'select code.number, code.userid, code.usedatetime from code
		where TO_DAYS(NOW()) - TO_DAYS(code.usedatetime) = 1 and code.type = -1 and code.isused= 1'
        );
        $text_json = $datatables->generate();
        $text_array = json_decode($text_json, true);
        $codes = $text_array['data'];
        $text_html = '<table border=1><tr><td>金额</td><td>用户ID</td><td>用户名</td><td>充值时间</td>';
        $income_count = 0;
        $income_total = 0.00;
        foreach ($codes as $code) {
            $text_html .= '<tr>';
            $text_html .= '<td>' . $code['number'] . '</td>';
            $text_html .= '<td>' . $code['userid'] . '</td>';
            $user = User::find($code['userid']);
            $text_html .= '<td>' . $user->user_name . '</td>';
            $text_html .= '<td>' . $code['usedatetime'] . '</td>';
            $text_html .= '</tr>';
            ++$income_count;
            $income_total += $code['number'];
        }
        //易付通的单独表
        $datatables2 = new Datatables(new DatatablesHelper());
        $datatables2->query('select COUNT(*) as "count_yft" from INFORMATION_SCHEMA.TABLES where TABLE_NAME = "yft_order_info"');
        $count_yft = $datatables2->generate();
        if (strpos($count_yft, '"count_yft":1')) {
            $datatables2->query(
                'select yft_order_info.price, yft_order_info.user_id, yft_order_info.create_time from yft_order_info
				where TO_DAYS(NOW()) - TO_DAYS(yft_order_info.create_time) = 1 and yft_order_info.state= 1'
            );
            $text_json2 = $datatables2->generate();
            $text_array2 = json_decode($text_json2, true);
            $codes2 = $text_array2['data'];
            foreach ($codes2 as $code2) {
                $text_html .= '<tr>';
                $text_html .= '<td>' . $code2['price'] . '</td>';
                $text_html .= '<td>' . $code2['user_id'] . '</td>';
                $user = User::find($code2['user_id']);
                $text_html .= '<td>' . $user->user_name . '</td>';
                $text_html .= '<td>' . $code2['create_time'] . '</td>';
                $text_html .= '</tr>';
                ++$income_count;
                $income_total += $code['price'];
            }
        }
        $text_html .= '</table>';
        $text_html .= '<br>昨日总收入笔数：' . $income_count . '<br>昨日总收入金额：' . $income_total;

        $adminUser = User::where('is_admin', '=', '1')->get();
        foreach ($adminUser as $user) {
            echo 'Send offline mail to user: ' . $user->id;
            $user->sendMail(
                $_ENV['appName'] . '-财务日报',
                'news/finance.tpl',
                [
                    'title' => '财务日报',
                    'text'  => $text_html
                ],
                []
            );
        }

        if ($_ENV['finance_public']) {
            Telegram::Send(
                '新鲜出炉的财务日报~' . PHP_EOL .
                '昨日总收入笔数:' . $income_count . PHP_EOL .
                '昨日总收入金额:' . $income_total . PHP_EOL .
                '凌晨也在努力工作~'
            );
        }
    }

    public static function sendFinanceMail_week()
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query(
            'SELECT code.number FROM code
		WHERE DATEDIFF(NOW(),code.usedatetime) <=7 AND DATEDIFF(NOW(),code.usedatetime) >=1 AND code.isused = 1'
        );
        //每周的第一天是周日，因此统计周日～周六的七天
        $text_json = $datatables->generate();
        $text_array = json_decode($text_json, true);
        $codes = $text_array['data'];
        $text_html = '';
        $income_count = 0;
        $income_total = 0.00;
        foreach ($codes as $code) {
            ++$income_count;
            $income_total += $code['number'];
        }
        //易付通的单独表
        $datatables2 = new Datatables(new DatatablesHelper());
        $datatables2->query('select COUNT(*) as "count_yft" from INFORMATION_SCHEMA.TABLES where TABLE_NAME = "yft_order_info"');
        $count_yft = $datatables2->generate();
        if (strpos($count_yft, '"count_yft":1')) {
            $datatables2->query(
                'select yft_order_info.price from yft_order_info
				where yearweek(date_format(yft_order_info.create_time,\'%Y-%m-%d\')) = yearweek(now())-1 and yft_order_info.state= 1'
            );
            //每周的第一天是周日，因此统计周日～周六的七天
            $text_json2 = $datatables2->generate();
            $text_array2 = json_decode($text_json2, true);
            $codes2 = $text_array2['data'];
            foreach ($codes2 as $code2) {
                ++$income_count;
                $income_total += $code2['price'];
            }
        }

        $text_html .= '<br>上周总收入笔数：' . $income_count . '<br>上周总收入金额：' . $income_total;

        $adminUser = User::where('is_admin', '=', '1')->get();
        foreach ($adminUser as $user) {
            echo 'Send offline mail to user: ' . $user->id;
            $user->sendMail(
                $_ENV['appName'] . '-财务周报',
                'news/finance.tpl',
                [
                    'title' => '财务周报',
                    'text'  => $text_html
                ],
                []
            );
        }

        if ($_ENV['finance_public']) {
            Telegram::Send(
                '新鲜出炉的财务周报~' . PHP_EOL .
                '上周总收入笔数:' . $income_count . PHP_EOL .
                '上周总收入金额:' . $income_total . PHP_EOL .
                '周末也在努力工作~'
            );
        }
    }

    public static function sendFinanceMail_month()
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query(
            'select code.number from code
		where date_format(code.usedatetime,\'%Y-%m\')=date_format(date_sub(curdate(), interval 1 month),\'%Y-%m\') and code.type = -1 and code.isused= 1'
        );
        $text_json = $datatables->generate();
        $text_array = json_decode($text_json, true);
        $codes = $text_array['data'];
        $text_html = '';
        $income_count = 0;
        $income_total = 0.00;
        foreach ($codes as $code) {
            ++$income_count;
            $income_total += $code['number'];
        }
        $text_html .= '<br>上月总收入笔数：' . $income_count . '<br>上月总收入金额：' . $income_total;

        $adminUser = User::where('is_admin', '=', '1')->get();
        foreach ($adminUser as $user) {
            echo 'Send offline mail to user: ' . $user->id;
            $user->sendMail(
                $_ENV['appName'] . '-财务月报',
                'news/finance.tpl',
                [
                    'title' => '财务月报',
                    'text'  => $text_html
                ],
                []
            );
        }

        if ($_ENV['finance_public']) {
            Telegram::Send(
                '新鲜出炉的财务月报~' . PHP_EOL .
                '上月总收入笔数:' . $income_count . PHP_EOL .
                '上月总收入金额:' . $income_total . PHP_EOL .
                '月初也在努力工作~'
            );
        }
    }
}
