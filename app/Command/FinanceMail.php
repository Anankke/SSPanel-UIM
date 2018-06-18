<?php


namespace App\Command;

use App\Models\User;
use App\Models\Ann;
use App\Models\Code;
use App\Services\Config;
use App\Services\Mail;
use App\Services\Analytics;
use App\Utils\Telegram;
use App\Utils\Tools;

use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

class FinanceMail
{
    public static function sendFinanceMail_day()
    {
		$datatables = new Datatables(new DatatablesHelper());
        $datatables->query(
		'Select code.id,code.code,code.type,code.number,code.userid,code.userid as user_name,code.usedatetime 
		from code
		where TO_DAYS(NOW()) – TO_DAYS(code.usedatetime) = 1');
		$datatables->edit('number', function ($data) {
            switch ($data['type']) {
              case -1:
                return "充值 ".$data['number']." 元";

              case -2:
                return "支出 ".$data['number']." 元";

              default:
                return "已经废弃";
            }
        });

        $datatables->edit('userid', function ($data) {
            return $data['userid'] == 0 ? '未使用' : $data['userid'];
        });

        $datatables->edit('user_name', function ($data) {
            $user = User::find($data['user_name']);
            if ($user == null) {
                return "未使用";
            }
            return $user->user_name;
        });

        $datatables->edit('type', function ($data) {
            switch ($data['type']) {
              case -1:
                return "充值金额";
              case -2:
                return "财务支出";
              default:
                return "已经废弃";
            }
        });

        $datatables->edit('usedatetime', function ($data) {
            return $data['usedatetime'] > '2000-1-1 0:0:0' ? $data['usedatetime'] : "未使用";
        });

		$adminUser = User::where("is_admin", "=", "1")->get();
        foreach ($adminUser as $user) {
			echo "Send offline mail to user: ".$user->id;
			$subject = Config::get('appName')."-财务日报";
			$to = $user->email;
			$text = $datatables->generate();
			try {
			Mail::send($to, $subject, 'news/finance_detail.tpl', [
			"user" => $user,"text" => $text;
			], [
			]);
			} catch (Exception $e) {
			echo $e->getMessage();
			}
		}
        
		if (Config::get("finance_pulic")=="true") {
			$sts = new Analytics();    
			Telegram::Send(
				"新鲜出炉的财务日报~".PHP_EOL.
				"昨日总收入笔数:".$sts->getTodayCheckinUser().PHP_EOL.
				"昨日总收入金额:".Tools::flowAutoShow($lastday_total).PHP_EOL.
				"凌晨也在努力工作~"
			);
		}
    }

	public static function sendFinanceMail_week()
	{
	
	}

	public static function sendFinanceMail_month()
	{
	
	}


    public static function reall()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->last_day_t=($user->u+$user->d);
            $user->save();
        }
    }
}
