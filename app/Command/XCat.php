<?php

namespace App\Command;

/***
 * Class XCat
 * @package App\Command
 */

use App\Models\User;
use App\Models\Relay;
use App\Services\Gateway\ChenPay;
use App\Utils\Hash;
use App\Utils\Tools;
use App\Utils\Discord;
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
            case("alipay"):
                return (new ChenPay())->AliPayListen();
            case("wxpay"):
                return (new ChenPay())->WxPayListen();
            case("createAdmin"):
                return $this->createAdmin();
            case("resetTraffic"):
                return $this->resetTraffic();
            case("setDiscord"):
                return Discord::set();
            case("setTelegram"):
                return $this->setTelegram();
            case("initQQWry"):
                return $this->initQQWry();
            case("sendDiaryMail"):
                return DailyMail::sendDailyMail();
            case("sendFinanceMail_day"):
                return FinanceMail::sendFinanceMail_day();
            case("sendFinanceMail_week"):
                return FinanceMail::sendFinanceMail_week();
            case("sendFinanceMail_month"):
                return FinanceMail::sendFinanceMail_month();
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
            case("detectGFW"):
                return Job::detectGFW();
            case("syncnas"):
                return SyncRadius::syncnas();
            case("dailyjob"):
                return Job::DailyJob();
            case("checkjob"):
                return Job::CheckJob();
            case("userga"):
                return Job::UserGa();
            case("backup"):
                return Job::backup(false);
            case("backupfull"):
                return Job::backup(true);
            case("initdownload"):
                return $this->initdownload();
            case("updatedownload"):
                return Job::updatedownload();
            case("cleanRelayRule"):
                return $this->cleanRelayRule();
            case("resetPort"):
                return $this->resetPort();
            case("resetAllPort"):
                return $this->resetAllPort();
            case("update"):
                return Update::update($this);
            case ("sendDailyUsageByTG"):
                return $this->sendDailyUsageByTG();
            case('npmbuild'):
                return $this->npmbuild();
            default:
                return $this->defaultAction();
        }
    }

    public function defaultAction()
    {
        echo(PHP_EOL . "用法： php xcat [选项]" . PHP_EOL);
        echo("常用选项:" . PHP_EOL);
        echo("  createAdmin - 创建管理员帐号" . PHP_EOL);
        echo("  setDiscord - 设置 Discord 机器人" . PHP_EOL);
        echo("  setTelegram - 设置 Telegram 机器人" . PHP_EOL);
        echo("  cleanRelayRule - 清除所有中转规则" . PHP_EOL);
        echo("  resetPort - 重置单个用户端口" . PHP_EOL);
        echo("  resetAllPort - 重置所有用户端口" . PHP_EOL);
        echo("  initdownload - 下载 SSR 程序至服务器" . PHP_EOL);
        echo("  initQQWry - 下载 IP 解析库" . PHP_EOL);
        echo("  resetTraffic - 重置所有用户流量" . PHP_EOL);
        echo("  update - 更新并迁移配置" . PHP_EOL);
    }

    public function resetPort()
    {
        fwrite(STDOUT, "请输入用户id: ");
        $user = User::Where("id", "=", trim(fgets(STDIN)))->first();
        $origin_port = $user->port;

        $user->port = Tools::getAvPort();

        $relay_rules = Relay::where('user_id', $user->id)->where('port', $origin_port)->get();
        foreach ($relay_rules as $rule) {
            $rule->port = $user->port;
            $rule->save();
        }

        if ($user->save()) {
            echo "重置成功!\n";
        }
    }

    public function resetAllPort()
    {
        $users = User::all();
        foreach ($users as $user) {
            $origin_port = $user->port;
            $user->port = Tools::getAvPort();
            echo '$origin_port=' . $origin_port . '&$user->port=' . $user->port . "\n";
            $user->save();
        }
    }

    public function cleanRelayRule()
    {
        $rules = Relay::all();
        foreach ($rules as $rule) {
            echo($rule->id . "\n");
            if ($rule->source_node_id == 0) {
                echo($rule->id . "被删除！\n");
                $rule->delete();
                continue;
            }

            $ruleset = Relay::where('user_id', $rule->user_id)->orwhere('user_id', 0)->get();
            $maybe_rule_id = Tools::has_conflict_rule($rule, $ruleset, $rule->id);
            if ($maybe_rule_id != 0) {
                echo($rule->id . "被删除！\n");
                $rule->delete();
            }
        }
    }

    public function initdownload()
    {
        system('git clone https://github.com/xcxnig/ssr-download.git ' . BASE_PATH . "/public/ssr-download/", $ret);
        echo $ret;
    }

    public function createAdmin()
    {
        echo "add admin/ 创建管理员帐号.....";
        // ask for input
        fwrite(STDOUT, "Enter your email/输入管理员邮箱: ");
        // get input
        $email = trim(fgets(STDIN));
        // write input back
        fwrite(STDOUT, "Enter password for: $email / 为 $email 添加密码: ");
        $passwd = trim(fgets(STDIN));
        echo "Email: $email, Password: $passwd! ";
        fwrite(STDOUT, "Press [Y] to create admin..... 按下[Y]确认来确认创建管理员账户..... \n");
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
            $user->port = Tools::getLastPort() + 1;
            $user->t = 0;
            $user->u = 0;
            $user->d = 0;
            $user->transfer_enable = Tools::toGB(Config::get('defaultTraffic'));
            $user->invite_num = Config::get('inviteNum');
            $user->ref_by = 0;
            $user->is_admin = 1;
            $user->expire_in = date("Y-m-d H:i:s", time() + Config::get('user_expire_in_default') * 86400);
            $user->reg_date = date("Y-m-d H:i:s");
            $user->money = 0;
            $user->im_type = 1;
            $user->im_value = "";
            $user->class = 0;
            $user->plan = 'A';
            $user->node_speedlimit = 0;
            $user->theme = Config::get('theme');

            $ga = new GA();
            $secret = $ga->createSecret();
            $user->ga_token = $secret;
            $user->ga_enable = 0;

            if ($user->save()) {
                echo "Successful/添加成功!\n";
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
                'last_day_t' => 0,
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
        if ($bot->setWebhook(Config::get('baseUrl') . "/telegram_callback?token=" . Config::get('telegram_request_token')) == 1) {
            echo("设置成功！" . PHP_EOL);
        }
    }

    public function initQQWry()
    {
        echo("downloading....");
        $qqwry = file_get_contents("https://qqwry.mirror.noc.one/QQWry.Dat");
        if ($qqwry != "") {
            $fp = fopen(BASE_PATH . "/storage/qqwry.dat", "wb");
            if ($fp) {
                fwrite($fp, $qqwry);
                fclose($fp);
            }
            echo("finish....");
        }
    }

    public function sendDailyUsageByTG()
    {
        $bot = new \TelegramBot\Api\BotApi(Config::get('telegram_token'));
        $users = User::where('telegram_id', ">", 0)->get();
        foreach ($users as $user) {
            $reply_message = "您当前的流量状况：
今日已使用 " . $user->TodayusedTraffic() . " " . number_format(($user->u + $user->d - $user->last_day_t) / $user->transfer_enable * 100, 2) . "%
今日之前已使用 " . $user->LastusedTraffic() . " " . number_format($user->last_day_t / $user->transfer_enable * 100, 2) . "%
未使用 " . $user->unusedTraffic() . " " . number_format(($user->transfer_enable - ($user->u + $user->d)) / $user->transfer_enable * 100, 2) . "%
					                        ";
            try {
                $bot->sendMessage($user->get_user_attributes("telegram_id"), $reply_message, $parseMode = null, $disablePreview = false, $replyToMessageId = null);

            } catch (\TelegramBot\Api\HttpException $e) {
                echo 'Message: 用户: ' . $user->get_user_attributes("user_name") . " 删除了账号或者屏蔽了宝宝";
            }
        }
    }

    public function npmbuild()
    {
        chdir(BASE_PATH . '/uim-index-dev');
        system('npm install');
        system('npm run build');
        system('cp -u ../public/vuedist/index.html ../resources/views/material/index.tpl');
    }
}
