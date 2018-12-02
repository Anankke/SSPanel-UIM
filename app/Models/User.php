<?php

namespace App\Models;

/**
 * User Model
 */

use App\Utils\Tools;
use App\Utils\Hash;
use App\Models\InviteCode;
use App\Services\Config;
use App\Utils\GA;
use App\Utils\QQWry;
use App\Models\Link;
use App\Utils\Radius;
use Ramsey\Uuid\Uuid;

class User extends Model
{
    protected $connection = "default";
    protected $table = "user";

    public $isLogin;

    public $isAdmin;

    protected $casts = [
        "t" => 'float',
        "u" => 'float',
        "d" => 'float',
        "port" => 'int',
        "transfer_enable" => 'float',
        "enable" => 'int',
        'is_admin' => 'boolean',
        'is_multi_user' => 'int',
        'node_speedlimit' => 'float',
    ];

    public function getGravatarAttribute()
    {
      //  $hash = md5(strtolower(trim($this->attributes['email'])));
        return "/images/Avatar.jpg";//.$hash;
    }

    public function isAdmin()
    {
        return $this->attributes['is_admin'];
    }

    public function lastSsTime()
    {
        if ($this->attributes['t'] == 0) {
            return "从未使用喵";
        }
        return Tools::toDateTime($this->attributes['t']);
    }

    public function getMuMd5()
    {
        $str = str_replace("%id", $this->attributes['id'], Config::get('mu_regex'));
        $str = str_replace("%suffix", Config::get('mu_suffix'), $str);
        preg_match_all("|%-?[1-9]\d*m|U", $str, $matches, PREG_PATTERN_ORDER);
        foreach ($matches[0] as $key) {
            $key_match = str_replace("%", "", $key);
            $key_match = str_replace("m", "", $key_match);
            $md5 = substr(MD5($this->attributes['id'].$this->attributes['passwd'].$this->attributes['method'].$this->attributes['obfs'].$this->attributes['protocol']),
            ($key_match < 0 ? $key_match : 0),
            abs($key_match));
            $str = str_replace($key, $md5, $str);
        }
        return $str;
    }

    public function lastCheckInTime()
    {
        if ($this->attributes['last_check_in_time'] == 0) {
            return "从未签到";
        }
        return Tools::toDateTime($this->attributes['last_check_in_time']);
    }

    public function regDate()
    {
        return $this->attributes['reg_date'];
    }

    public function updatePassword($pwd)
    {
        $this->pass = Hash::passwordHash($pwd);
        $this->save();
    }

    public function get_forbidden_ip()
    {
        return str_replace(",", PHP_EOL, $this->attributes['forbidden_ip']);
    }

    public function get_forbidden_port()
    {
        return str_replace(",", PHP_EOL, $this->attributes['forbidden_port']);
    }

    public function updateSsPwd($pwd)
    {
        $this->passwd = $pwd;
        $this->save();
    }

    public function updateMethod($method)
    {
        $this->method = $method;
        $this->save();
    }

    public function addInviteCode()
    {
        $uid = $this->attributes['id'];
        $code = new InviteCode();
		while(true){
			$temp_code=Tools::genRandomChar(4);
			if(InviteCode::where('user_id', $uid)->count()==0){
				break;
			}
		}
        $code->code = $temp_code;
        $code->user_id = $uid;
        $code->save();
    }

    public function getUuid() {
        return Uuid::uuid3(Uuid::NAMESPACE_DNS, $this->attributes['id']. '|' .$this->attributes['passwd'])->toString();
    }

    public function trafficUsagePercent()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transferEnable = $this->attributes['transfer_enable'];
        if ($transferEnable == 0) {
            return 0;
        }
        $percent = $total / $transferEnable;
        $percent = round($percent, 2);
        $percent = $percent * 100;
        return $percent;
    }

    public function enableTraffic()
    {
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowAutoShow($transfer_enable);
    }
    public function enableTrafficInGB()
    {
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowToGB($transfer_enable);
    }
    public function usedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        return Tools::flowAutoShow($total);
    }
    public function unusedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowAutoShow($transfer_enable - $total);
    }

    public function TodayusedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d']-$this->attributes['last_day_t'];
        return Tools::flowAutoShow($total);
    }

    public function LastusedTraffic()
    {
        $total = $this->attributes['last_day_t'];
        return Tools::flowAutoShow($total);
    }

    public function isAbleToCheckin()
    {
        $last = $this->attributes['last_check_in_time'];

        $now = time();
        if (date("Ymd", $now)!= date("Ymd", $last)) {
            return true;
        }
        return false;
    }

    /*
     * @param traffic 单位 MB
     */
    public function addTraffic($traffic)
    {
    }

    public function getGAurl()
    {
        $ga = new GA();
        $url = $ga->getUrl(urlencode(Config::get('appName')."-".$this->attributes['user_name']."-两步验证码"), $this->attributes['ga_token']);
        return $url;
    }

    public function inviteCodes()
    {
        $uid = $this->attributes['id'];
        return InviteCode::where('user_id', $uid)->get();
    }

    public function ref_by_user()
    {
        $uid = $this->attributes['ref_by'];
        return User::where('id', $uid)->first();
    }

    public function clean_link()
    {
        $uid = $this->attributes['id'];
        Link::where('userid', $uid)->delete();
    }
    
    public function clear_inviteCodes()
    {
        $uid = $this->attributes['id'];
        InviteCode::where('user_id', $uid)->delete();
    }

    public function online_ip_count()
    {
        $uid = $this->attributes['id'];
        $total = Ip::where("datetime", ">=", time()-90)->where('userid', $uid)->orderBy('userid', 'desc')->get();
        $unique_ip_list = array();
        foreach ($total as $single_record) {
            $single_record->ip = Tools::getRealIp($single_record->ip);
            $is_node = Node::where("node_ip", $single_record->ip)->first();
            if($is_node) {
                continue;
            }

            if (!in_array($single_record->ip, $unique_ip_list)) {
                array_push($unique_ip_list, $single_record->ip);
            }
        }

        return count($unique_ip_list);
    }

    public function kill_user()
    {
        $uid = $this->attributes['id'];
        $email = $this->attributes['email'];

        Radius::Delete($email);

        RadiusBan::where('userid', '=', $uid)->delete();
        Disconnect::where('userid', '=', $uid)->delete();
        Bought::where('userid', '=', $uid)->delete();
        Bought::where('userid', '=', $uid)->delete();
        Ip::where('userid', '=', $uid)->delete();
        Code::where('userid', '=', $uid)->delete();
        DetectLog::where('user_id', '=', $uid)->delete();
        Link::where('userid', '=', $uid)->delete();
        LoginIp::where('userid', '=', $uid)->delete();
        InviteCode::where('user_id', '=', $uid)->delete();
        TelegramSession::where('user_id', '=', $uid)->delete();
        UnblockIp::where('userid', '=', $uid)->delete();
        TrafficLog::where('user_id', '=', $uid)->delete();
        Token::where('user_id', '=', $uid)->delete();
        PasswordReset::where('email', '=', $email)->delete();

        $this->delete();

        return true;
    }

    public function get_table_json_array()
    {
        $id = $this->attributes['id'];
        $today_traffic = Tools::flowToMB($this->attributes['u'] + $this->attributes['d'] - $this->attributes['last_day_t']);
        $is_enable = $this->attributes['enable'] == 1 ? "可用" : "禁用";
        $reg_location = $this->attributes['reg_ip'];
        $account_expire_in = $this->attributes['expire_in'];
        $class_expire_in = $this->attributes['class_expire'];
        $used_traffic = Tools::flowToGB($this->attributes['u'] + $this->attributes['d']);
        $enable_traffic = Tools::flowToGB($this->attributes['transfer_enable']);

        $im_type = '';
        $im_value = $this->attributes['im_value'];
        switch($this->attributes['im_type']) {
            case 1:
              $im_type = '微信';
              break;
            case 2:
              $im_type = 'QQ';
              break;
            case 3:
              $im_type = 'Google+';
              break;
            default:
              $im_type = 'Telegram';
              $im_value = '<a href="https://telegram.me/'.$im_value.'">'.$im_value.'</a>';
        }

        $ref_user = User::find($this->attributes['ref_by']);

        if ($this->attributes['ref_by'] == 0) {
            $ref_user_id = 0;
            $ref_user_name = "系统邀请";
        } else {
            if ($ref_user == null) {
                $ref_user_id = $this->attributes['ref_by'];
                $ref_user_name = "邀请人已经被删除";
            } else {
                $ref_user_id = $this->attributes['ref_by'];
                $ref_user_name = $ref_user->user_name;
            }
        }

        $iplocation = new QQWry();
        $location=$iplocation->getlocation($reg_location);
        $reg_location .= "\n".iconv('gbk', 'utf-8//IGNORE', $location['country'].$location['area']);

        $return_array = Array('DT_RowId' => 'row_1_'.$id, $id, $id,
                              $this->attributes['user_name'], $this->attributes['remark'],
                              $this->attributes['email'], $this->attributes['money'],
                              $im_type, $im_value,
                              $this->attributes['node_group'], $account_expire_in,
                              $this->attributes['class'], $class_expire_in,
                              $this->attributes['passwd'], $this->attributes['port'],
                              $this->attributes['method'],
                              $this->attributes['protocol'], $this->attributes['obfs'],
                              $this->online_ip_count(), $this->lastSsTime(),
                              $used_traffic, $enable_traffic,
                              $this->lastCheckInTime(), $today_traffic,
                              $is_enable, $this->attributes['reg_date'],
                              $reg_location,
                              $this->attributes['auto_reset_day'], $this->attributes['auto_reset_bandwidth'],
                              $ref_user_id, $ref_user_name);
        return $return_array;
    }
}
