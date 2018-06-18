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
		where TO_DAYS(NOW()) �C TO_DAYS(code.usedatetime) = 1');
		$datatables->edit('number', function ($data) {
            switch ($data['type']) {
              case -1:
                return "��ֵ ".$data['number']." Ԫ";

              case -2:
                return "֧�� ".$data['number']." Ԫ";

              default:
                return "�Ѿ�����";
            }
        });

        $datatables->edit('userid', function ($data) {
            return $data['userid'] == 0 ? 'δʹ��' : $data['userid'];
        });

        $datatables->edit('user_name', function ($data) {
            $user = User::find($data['user_name']);
            if ($user == null) {
                return "δʹ��";
            }
            return $user->user_name;
        });

        $datatables->edit('type', function ($data) {
            switch ($data['type']) {
              case -1:
                return "��ֵ���";
              case -2:
                return "����֧��";
              default:
                return "�Ѿ�����";
            }
        });

        $datatables->edit('usedatetime', function ($data) {
            return $data['usedatetime'] > '2000-1-1 0:0:0' ? $data['usedatetime'] : "δʹ��";
        });

		$adminUser = User::where("is_admin", "=", "1")->get();
        foreach ($adminUser as $user) {
			echo "Send offline mail to user: ".$user->id;
			$subject = Config::get('appName')."-�����ձ�";
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
				"���ʳ�¯�Ĳ����ձ�~".PHP_EOL.
				"�������������:".$sts->getTodayCheckinUser().PHP_EOL.
				"������������:".Tools::flowAutoShow($lastday_total).PHP_EOL.
				"�賿Ҳ��Ŭ������~"
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
