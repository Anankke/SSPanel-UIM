<?php
namespace App\Models;

use App\Utils\QQWry;
use voku\helper\AntiXSS;

class UserSubscribeLog extends Model
{
    protected $connection = 'default';
    protected $table = 'user_subscribe_log';

    public static function user_is_null($UserSubscribeLog): void
    {
        self::where('user_id', $UserSubscribeLog->user_id)->delete();
    }

    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    public function location(QQWry $QQWry = null)
    {
        if ($QQWry === null) {
            $QQWry = new QQWry();
        }
        $location = $QQWry->getlocation($this->request_ip);
        return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
    }

    public static function addSubscribeLog($user, $type, $ua)
    {
        $log = new UserSubscribeLog();
        $anti_xss = new AntiXSS();
        $log->user_name = $user->user_name;
        $log->user_id = $user->id;
        $log->email = $user->email;
        $log->subscribe_type = $type;
        $log->request_ip = $_SERVER['REMOTE_ADDR'];
        $log->request_time = date('Y-m-d H:i:s');
        $log->request_user_agent = $anti_xss->xss_clean($ua);
        $log->save();
    }
}
