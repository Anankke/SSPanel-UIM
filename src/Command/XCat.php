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
use App\Services\Config;
use App\Services\DefaultConfig;

use App\Utils\GA;
use Exception;
use TelegramBot\Api\BotApi;

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
            case ('alipay'):
                return (new ChenPay())->AliPayListen();
            case ('wxpay'):
                return (new ChenPay())->WxPayListen();
            case ('createAdmin'):
                return $this->createAdmin();
            case ('resetTraffic'):
                return $this->resetTraffic();
            case ('setTelegram'):
                return $this->setTelegram();
            case ('initQQWry'):
                return $this->initQQWry();
            case ('sendDiaryMail'):
                return DailyMail::sendDailyMail();
            case ('sendFinanceMail_day'):
                return FinanceMail::sendFinanceMail_day();
            case ('sendFinanceMail_week'):
                return FinanceMail::sendFinanceMail_week();
            case ('sendFinanceMail_month'):
                return FinanceMail::sendFinanceMail_month();
            case ('reall'):
                return DailyMail::reall();
            case ('syncusers'):
                return SyncRadius::syncusers();
            case ('synclogin'):
                return SyncRadius::synclogin();
            case ('syncvpn'):
                return SyncRadius::syncvpn();
            case ('nousers'):
                return ExtMail::sendNoMail();
            case ('oldusers'):
                return ExtMail::sendOldMail();
            case ('syncnode'):
                return Job::syncnode();
            case ('syncnasnode'):
                return Job::syncnasnode();
            case ('detectGFW'):
                return Job::detectGFW();
            case ('syncnas'):
                return SyncRadius::syncnas();
            case ('dailyjob'):
                return Job::DailyJob();
            case ('checkjob'):
                return Job::CheckJob();
            case ('userga'):
                return Job::UserGa();
            case ('backup'):
                return Job::backup(false);
            case ('backupfull'):
                return Job::backup(true);
            case ('initdownload'):
                return $this->initdownload();
            case ('updatedownload'):
                return Job::updatedownload();
            case ('cleanRelayRule'):
                return $this->cleanRelayRule();
            case ('resetPort'):
                return $this->resetPort();
            case ('resetAllPort'):
                return $this->resetAllPort();
            case ('update'):
                return Update::update($this);
            case ('sendDailyUsageByTG'):
                return $this->sendDailyUsageByTG();
            case ('npmbuild'):
                return $this->npmbuild();
            case ('getCookie'):
                return $this->getCookie();
            case ('detectConfigs'):
                return $this->detectConfigs();
            case ('portAutoChange'):
                return PortAutoChange::index();
            case ('initdocuments'):
                return $this->initdocuments();
            default:
                return $this->defaultAction();
        }
    }

    public function defaultAction()
    {
        echo (PHP_EOL . '用法： php xcat [选项]' . PHP_EOL);
        echo ('常用选项:' . PHP_EOL);
        echo ('  createAdmin - 创建管理员帐号' . PHP_EOL);
        echo ('  setTelegram - 设置 Telegram 机器人' . PHP_EOL);
        echo ('  cleanRelayRule - 清除所有中转规则' . PHP_EOL);
        echo ('  resetPort - 重置单个用户端口' . PHP_EOL);
        echo ('  resetAllPort - 重置所有用户端口' . PHP_EOL);
        echo ('  initdownload - 下载 SSR 程序至服务器' . PHP_EOL);
        echo ('  initQQWry - 下载 IP 解析库' . PHP_EOL);
        echo ('  resetTraffic - 重置所有用户流量' . PHP_EOL);
        echo ('  update - 更新并迁移配置' . PHP_EOL);
        echo ('  detectConfigs - 检查数据库内新增的配置' . PHP_EOL);
        echo ('  initdocuments - 下载用户使用文档至服务器' . PHP_EOL);
        echo ('  portAutoChange - [实验]  SS 单端口被墙自动换' . PHP_EOL);
    }

    public function resetPort()
    {
        fwrite(STDOUT, '请输入用户id: ');
        $user = User::Where('id', '=', trim(fgets(STDIN)))->first();
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
            echo ($rule->id . "\n");
            if ($rule->source_node_id == 0) {
                echo ($rule->id . "被删除！\n");
                $rule->delete();
                continue;
            }

            $ruleset = Relay::where('user_id', $rule->user_id)->orwhere('user_id', 0)->get();
            $maybe_rule_id = Tools::has_conflict_rule($rule, $ruleset, $rule->id);
            if ($maybe_rule_id != 0) {
                echo ($rule->id . "被删除！\n");
                $rule->delete();
            }
        }
    }

    public function initdownload()
    {
        system('git clone --depth=3 https://github.com/xcxnig/ssr-download.git ' . BASE_PATH . '/public/ssr-download/ && git gc', $ret);
        echo $ret;
    }

    public function createAdmin()
    {
        if (count($this->argv) === 2) {
            echo 'add admin/ 创建管理员帐号.....';
            // ask for input
            fwrite(STDOUT, 'Enter your email/输入管理员邮箱: ');
            // get input
            $email = trim(fgets(STDIN));
            // write input back
            fwrite(STDOUT, "Enter password for: $email / 为 $email 添加密码: ");
            $passwd = trim(fgets(STDIN));
            echo "Email: $email, Password: $passwd! ";
            fwrite(STDOUT, "Press [y] to create admin..... 按下[Y]确认来确认创建管理员账户..... \n");
            $y = trim(fgets(STDIN));
        } elseif (count($this->argv) === 4) {
            [,, $email, $passwd] = $this->argv;
            $y = 'y';
        }

        if (strtolower($y) == 'y') {
            echo 'start create admin account';
            // create admin user
            // do reg user
            $user                   = new User();
            $user->user_name        = 'admin';
            $user->email            = $email;
            $user->pass             = Hash::passwordHash($passwd);
            $user->passwd           = Tools::genRandomChar(6);
            $user->port             = Tools::getLastPort() + 1;
            $user->t                = 0;
            $user->u                = 0;
            $user->d                = 0;
            $user->transfer_enable  = Tools::toGB((int) Config::getconfig('Register.string.defaultTraffic'));
            $user->invite_num       = (int) Config::getconfig('Register.string.defaultInviteNum');
            $user->ref_by           = 0;
            $user->is_admin         = 1;
            $user->expire_in        = date('Y-m-d H:i:s', time() + (int) Config::getconfig('Register.string.defaultExpire_in') * 86400);
            $user->reg_date         = date('Y-m-d H:i:s');
            $user->money            = 0;
            $user->im_type          = 1;
            $user->im_value         = '';
            $user->class            = 0;
            $user->plan             = 'A';
            $user->node_speedlimit  = 0;
            $user->theme            = $_ENV['theme'];

            $ga                     = new GA();
            $secret                 = $ga->createSecret();
            $user->ga_token         = $secret;
            $user->ga_enable        = 0;

            if ($user->save()) {
                echo "Successful/添加成功!\n";
                return true;
            }
            echo '添加失败';
            return false;
        }
        echo 'cancel';
        return false;
    }

    public function resetTraffic()
    {
        try {
            User::where('enable', 1)->update([
                'd' => 0,
                'u' => 0,
                'last_day_t' => 0,
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return 'reset traffic successful';
    }

    public function setTelegram()
    {
        if ($_ENV['use_new_telegram_bot'] === true) {
            $WebhookUrl = ($_ENV['baseUrl'] . '/telegram_callback?token=' . $_ENV['telegram_request_token']);
            $telegram = new \Telegram\Bot\Api($_ENV['telegram_token']);
            $telegram->removeWebhook();
            if ($telegram->setWebhook(['url' => $WebhookUrl])) {
                echo ('New Bot @' . $telegram->getMe()->getUsername() . ' 设置成功！');
            }
        } else {
            $bot = new BotApi($_ENV['telegram_token']);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf('https://api.telegram.org/bot%s/deleteWebhook', $_ENV['telegram_token']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $deleteWebhookReturn = json_decode(curl_exec($ch));
            curl_close($ch);
            if ($deleteWebhookReturn->ok && $deleteWebhookReturn->result && $bot->setWebhook($_ENV['baseUrl'] . '/telegram_callback?token=' . $_ENV['telegram_request_token']) == 1) {
                echo ('Old Bot 设置成功！' . PHP_EOL);
            }
        }
    }

    public function initQQWry()
    {
        echo ('开始下载纯真 IP 数据库....');
        $qqwry = file_get_contents('https://qqwry.mirror.noc.one/QQWry.Dat?from=sspanel_uim');
        if ($qqwry != '') {
            $fp = fopen(BASE_PATH . '/storage/qqwry.dat', 'wb');
            if ($fp) {
                fwrite($fp, $qqwry);
                fclose($fp);
                echo ('纯真 IP 数据库下载成功！');
            } else {
                echo ('纯真 IP 数据库保存失败！');
            }
        } else {
            echo ('下载失败！请重试，或在 https://github.com/SukkaW/qqwry-mirror/issues/new 反馈！');
        }
    }

    public function sendDailyUsageByTG()
    {
        $bot = new BotApi($_ENV['telegram_token']);
        $users = User::where('telegram_id', '>', 0)->get();
        foreach ($users as $user) {
            $u = $user->u;
            $d = $user->d;
            $last_day_t = $user->last_day_t;
            $transfer_enable = $user->transfer_enable;
            $reply_message = '您当前的流量状况：' . PHP_EOL .
                sprintf(
                    '今天已使用 %s %s%%',
                    $user->TodayusedTraffic(),
                    number_format(($u + $d - $last_day_t) / $transfer_enable * 100, 2)
                ) . PHP_EOL .
                sprintf(
                    '今天前已使用 %s %s%%',
                    $user->LastusedTraffic(),
                    number_format($last_day_t / $transfer_enable * 100, 2)
                ) . PHP_EOL .
                sprintf(
                    '剩余 %s %s%%',
                    $user->unusedTraffic(),
                    number_format(($transfer_enable - ($u + $d)) / $transfer_enable * 100, 2)
                );
            $bot->sendMessage(
                $user->get_user_attributes('telegram_id'),
                $reply_message,
                $parseMode = null,
                $disablePreview = false,
                $replyToMessageId = null
            );
        }
    }

    public function npmbuild()
    {
        chdir(BASE_PATH . '/uim-index-dev');
        system('npm install');
        system('npm run build');
        system('cp -u ../public/vuedist/index.html ../resources/views/material/index.tpl');
    }

    public function getCookie()
    {
        if (count($this->argv) === 3) {
            $user = User::find($this->argv[2]);
            $expire_in = 86400 + time();
            echo Hash::cookieHash($user->pass, $expire_in) . ' ' . $expire_in;
        }
    }

    public function initdocuments()
    {
        system('git clone https://github.com/GeekQu/PANEL_DOC.git ' . BASE_PATH . "/public/docs/", $ret);
        echo $ret;
    }

    public function detectConfigs()
    {
        echo DefaultConfig::detectConfigs();
    }
}
