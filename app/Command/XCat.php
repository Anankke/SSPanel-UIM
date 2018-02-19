<?php

namespace App\Command;

/***
 * Class XCat
 * @package App\Command
 */

use App\Models\User;
use App\Models\Relay;
use App\Utils\Hash;
use App\Utils\Tools;
use App\Services\Config;

use App\Utils\GA;
use App\Utils\QRcode;

class XCat
{
    public $argv;

    public function __construct($argv)
    {
        $this->argv = $argv;
    }

    public function boot()
    {
        switch ($this->argv[1]) {
            case("install"):
                    return $this->install();
            case("createAdmin"):
                return $this->createAdmin();
            case("resetTraffic"):
                return $this->resetTraffic();
            case("setTelegram"):
                    return $this->setTelegram();
            case("initQQWry"):
                    return $this->initQQWry();
            case("sendDiaryMail"):
                return DailyMail::sendDailyMail();
            case("reall"):
                    return DailyMail::reall();
            case("syncusers"):
                    return SyncRadius::syncusers();
            case("synclogin"):
                    return SyncRadius::synclogin();
            case("syncvpn"):
                return SyncRadius::syncvpn();
            case("nousers"):
                    return ExtMail::sendNoMail();
            case("oldusers"):
                    return ExtMail::sendOldMail();
            case("syncnode"):
                    return Job::syncnode();
            case("syncnasnode"):
                    return Job::syncnasnode();
            case("syncnas"):
                    return SyncRadius::syncnas();
            case("dailyjob"):
                return Job::DailyJob();
            case("checkjob"):
                return Job::CheckJob();
            case("syncduoshuo"):
                return Job::SyncDuoshuo();
            case("userga"):
                return Job::UserGa();
            case("backup"):
                return Job::backup();
            case("initdownload"):
                return $this->initdownload();
            case("updatedownload"):
                return Job::updatedownload();
            case("cleanRelayRule"):
                return $this->cleanRelayRule();
            default:
                return $this->defaultAction();
        }
    }

    public function defaultAction()
    {
        echo "Memo";
    }

    public function cleanRelayRule()
    {
        $rules = Relay::all();
        foreach ($rules as $rule) {
            echo($rule->id."\n");
            if ($rule->source_node_id == 0) {
                echo($rule->id."被删除！\n");
                $rule->delete();
                continue;
            }

            $ruleset = Relay::where('user_id', $rule->user_id)->orwhere('user_id', 0)->get();
            $maybe_rule_id = Tools::has_conflict_rule($rule, $ruleset, $rule->id);
            if ($maybe_rule_id != 0) {
                echo($rule->id."被删除！\n");
                $rule->delete();
            }
        }
    }

    public function install()
    {
        echo "x cat will install ss-panel v3...../n";
    }

    public function initdownload()
    {
        system('git clone https://github.com/esdeathlove/panel-download.git '.BASE_PATH."/public/ssr-download/", $ret);
        echo $ret;
    }

    public function createAdmin()
    {
        $this->initQQWry();
        $this->initdownload();
        echo "add admin/ 创建管理员帐号.....";
        // ask for input
        fwrite(STDOUT, "Enter your email/输入管理员邮箱: ");
        // get input
        $email = trim(fgets(STDIN));
        // write input back
        fwrite(STDOUT, "Enter password for: $email / 为 $email 添加密码 ");
        $passwd = trim(fgets(STDIN));
        echo "Email: $email, Password: $passwd! ";
        fwrite(STDOUT, "Press [Y] to create admin..... 按下[Y]确认来确认创建管理员账户..... ");
        $y = trim(fgets(STDIN));
        if (strtolower($y) == "y") {
            echo "start create admin account";
            // create admin user
            // do reg user
            $user = new User();
            $user->user_name = "admin";
            $user->email = $email;
            $user->pass = Hash::passwordHash($passwd);
            $user->passwd = Tools::genRandomChar(6);
            $user->port = Tools::getLastPort()+1;
            $user->t = 0;
            $user->u = 0;
            $user->d = 0;
            $user->transfer_enable = Tools::toGB(Config::get('defaultTraffic'));
            $user->invite_num = Config::get('inviteNum');
            $user->ref_by = 0;
            $user->is_admin = 1;
            $user->expire_in=date("Y-m-d H:i:s", time()+Config::get('user_expire_in_default')*86400);
            $user->reg_date=date("Y-m-d H:i:s");
            $user->money=0;
            $user->im_type=1;
            $user->im_value="";
            $user->class=0;
            $user->plan='A';
            $user->node_speedlimit=0;
            $user->theme=Config::get('theme');



            $ga = new GA();
            $secret = $ga->createSecret();
            $user->ga_token=$secret;
            $user->ga_enable=0;



            if ($user->save()) {
                echo "Successful/添加成功!";
                return true;
            }
            echo "添加失败";
            return false;
        }
        echo "cancel";
        return false;
    }

    public function resetTraffic()
    {
        try {
            User::where("enable", 1)->update([
            'd' => 0,
            'u' => 0,
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return "reset traffic successful";
    }


    public function setTelegram()
    {
        $bot = new \TelegramBot\Api\BotApi(Config::get('telegram_token'));
        if ($bot->setWebhook(Config::get('baseUrl')."/telegram_callback?token=".Config::get('telegram_request_token')) == 1) {
            echo("设置成功！");
        }
    }

    public function initQQWry()
    {
        echo("downloading....");
        $copywrite = file_get_contents("https://github.com/esdeathlove/qqwry-download/raw/master/copywrite.rar");
        $newmd5 = md5($copywrite);
        file_put_contents(BASE_PATH."/storage/qqwry.md5", $newmd5);
        $qqwry = file_get_contents("https://github.com/esdeathlove/qqwry-download/raw/master/qqwry.rar");
        if ($qqwry != "") {
            $key = unpack("V6", $copywrite)[6];
            for ($i=0; $i<0x200; $i++) {
                $key *= 0x805;
                $key ++;
                $key = $key & 0xFF;
                $qqwry[$i] = chr(ord($qqwry[$i]) ^ $key);
            }
            $qqwry = gzuncompress($qqwry);
            $fp = fopen(BASE_PATH."/storage/qqwry.dat", "wb");
            if ($fp) {
                fwrite($fp, $qqwry);
                fclose($fp);
            }
            echo("finish....");
        }
    }
}
