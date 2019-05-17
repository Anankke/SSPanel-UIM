<?php

namespace App\Command;

use App\Models\Node;
use App\Models\User;
use App\Models\RadiusBan;
use App\Models\LoginIp;
use App\Models\Speedtest;
use App\Models\Shop;
use App\Models\Bought;
use App\Models\Coupon;
use App\Models\Ip;
use App\Models\NodeInfoLog;
use App\Models\NodeOnlineLog;
use App\Models\TrafficLog;
use App\Models\DetectLog;
use App\Models\BlockIp;
use App\Models\TelegramSession;
use App\Models\EmailVerify;
use App\Services\Config;
use App\Utils\Radius;
use App\Utils\Tools;
use App\Services\Mail;
use App\Utils\QQWry;
use App\Utils\GA;
use App\Utils\Telegram;
use CloudXNS\Api;
use App\Models\Disconnect;
use App\Models\UnblockIp;

class Job
{
    public static function syncnode()
    {
        $nodes = Node::all();
        foreach ($nodes as $node) {
            if (in_array($node->sort, array(0, 1, 10, 11, 12, 13))) {
                $server_list = explode(";", $node->server);
                if (!Tools::is_ip($server_list[0])) {
                    if ($node->changeNodeIp($server_list[0])) {
                        $node->save();
                    }
                }
                if (in_array($node->sort, array(0, 10, 12))) {
                    Tools::updateRelayRuleIp($node);
                }
            }
        }
    }

    public static function backup($full = false)
    {
        $to = Config::get('auto_backup_email');
        if ($to == null) {
            return false;
        }
        mkdir('/tmp/ssmodbackup/');
        $db_address_array = explode(':', Config::get('db_host'));
        if ($full) {
            system('mysqldump --user=' . Config::get('db_username') . ' --password=' . Config::get('db_password') . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' ' . Config::get('db_database') . ' > /tmp/ssmodbackup/mod.sql');
        } else {
            system('mysqldump --user=' . Config::get('db_username') . ' --password=' . Config::get('db_password') . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' ' . Config::get('db_database') . ' announcement auto blockip bought code coupon disconnect_ip link login_ip payback radius_ban shop speedtest ss_invite_code ss_node ss_password_reset ticket unblockip user user_token email_verify detect_list relay paylist> /tmp/ssmodbackup/mod.sql', $ret);
            system('mysqldump --opt --user=' . Config::get('db_username') . ' --password=' . Config::get('db_password') . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' -d ' . Config::get('db_database') . ' alive_ip ss_node_info ss_node_online_log user_traffic_log detect_log telegram_session >> /tmp/ssmodbackup/mod.sql', $ret);
            if (Config::get('enable_radius') == 'true') {
                $db_address_array = explode(':', Config::get('radius_db_host'));
                system('mysqldump --user=' . Config::get('radius_db_user') . ' --password=' . Config::get('radius_db_password') . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . '' . Config::get('radius_db_database') . '> /tmp/ssmodbackup/radius.sql', $ret);
            }
        }

        system("cp " . BASE_PATH . "/config/.config.php /tmp/ssmodbackup/configbak.php", $ret);
        echo $ret;
        system("zip -r /tmp/ssmodbackup.zip /tmp/ssmodbackup/* -P " . Config::get('auto_backup_passwd'), $ret);
        $subject = Config::get('appName') . "-备份成功";
        $text = "您好，系统已经为您自动备份，请查看附件，用您设定的密码解压。";
        try {
            Mail::send($to, $subject, 'news/backup.tpl', [
                "text" => $text
            ], ["/tmp/ssmodbackup.zip"
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        system("rm -rf /tmp/ssmodbackup", $ret);
        system("rm /tmp/ssmodbackup.zip", $ret);

        if (Config::get('backup_notify') == 'true') {
            Telegram::Send("备份完毕了喵~今天又是安全祥和的一天呢。");
        }
    }

    public static function UserGa()
    {
        $users = User::all();
        foreach ($users as $user) {
            $ga = new GA();
            $secret = $ga->createSecret();

            $user->ga_token = $secret;
            $user->save();
        }
        echo "ok";
    }

    public static function syncnasnode()
    {
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $rule = preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $node->server);
            if (!$rule && (!$node->sort || $node->sort == 10 || $node->sort == 12 || $node->sort == 13)) {
                $ip = gethostbyname($node->server);
                $node->node_ip = $ip;
                $node->save();

                Radius::AddNas($node->node_ip, $node->server);
            }
        }
    }

    public static function DailyJob()
    {
        $nodes = Node::all();
        foreach ($nodes as $node) {
            if ($node->sort == 0 || $node->sort == 10 || $node->sort == 11 || $node->sort == 12 || $node->sort == 13) {
                if (date("d") == $node->bandwidthlimit_resetday) {
                    $node->node_bandwidth = 0;
                    $node->save();
                }
            }
        }

        NodeInfoLog::where("log_time", "<", time() - 86400 * 3)->delete();
        NodeOnlineLog::where("log_time", "<", time() - 86400 * 3)->delete();
        TrafficLog::where("log_time", "<", time() - 86400 * 3)->delete();
        DetectLog::where("datetime", "<", time() - 86400 * 3)->delete();
        Speedtest::where("datetime", "<", time() - 86400 * 3)->delete();
        EmailVerify::where("expire_in", "<", time() - 86400 * 3)->delete();
        system("rm " . BASE_PATH . "/storage/*.png", $ret);
        Telegram::Send("姐姐姐姐，数据库被清理了，感觉身体被掏空了呢~");

        //auto reset
        $boughts = Bought::all();
        $boughted_users = array();
        foreach ($boughts as $bought) {
            $user = User::where("id", $bought->userid)->first();

            if ($user == null) {
                $bought->delete();
                continue;
            }

            $shop = Shop::where("id", $bought->shopid)->first();

            if ($shop == null) {
                $bought->delete();
                continue;
            }

            if ($shop->reset() != 0 && $shop->reset_value() != 0 && $shop->reset_exp() != 0) {
                $boughted_users[] = $bought->userid;
                if (time() - $shop->reset_exp() * 86400 < $bought->datetime) {
                    if (intval((time() - $bought->datetime) / 86400) % $shop->reset() == 0 && intval((time() - $bought->datetime) / 86400) != 0) {
                        echo("流量重置-" . $user->id . "\n");
                        $user->transfer_enable = Tools::toGB($shop->reset_value());
                        $user->u = 0;
                        $user->d = 0;
                        $user->last_day_t = 0;
                        $user->save();

                        $subject = Config::get('appName') . "-您的流量被重置了";
                        $to = $user->email;
                        $text = "您好，根据您所订购的订单 ID:" . $bought->id . "，流量已经被重置为" . $shop->reset_value() . 'GB';
                        try {
                            Mail::send($to, $subject, 'news/warn.tpl', [
                                "user" => $user, "text" => $text
                            ], [
                            ]);
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            }

        }


        $users = User::all();
        foreach ($users as $user) {
            $user->last_day_t = ($user->u + $user->d);
            $user->save();
            if (in_array($user->id, $boughted_users)) {
                continue;
            }
            if (date("d") == $user->auto_reset_day) {
                $user->u = 0;
                $user->d = 0;
                $user->last_day_t = 0;
                $user->transfer_enable = $user->auto_reset_bandwidth * 1024 * 1024 * 1024;
                $user->save();

                $subject = Config::get('appName') . "-您的流量被重置了";
                $to = $user->email;
                $text = "您好，根据管理员的设置，流量已经被重置为" . $user->auto_reset_bandwidth . 'GB';
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }


        }


        #https://github.com/shuax/QQWryUpdate/blob/master/update.php

        $copywrite = file_get_contents("http://update.cz88.net/ip/copywrite.rar");

        $adminUser = User::where("is_admin", "=", "1")->get();

        $newmd5 = md5($copywrite);
        $oldmd5 = file_get_contents(BASE_PATH . "/storage/qqwry.md5");

        if ($newmd5 != $oldmd5) {
            file_put_contents(BASE_PATH . "/storage/qqwry.md5", $newmd5);
            $qqwry = file_get_contents("http://update.cz88.net/ip/qqwry.rar");
            if ($qqwry != "") {
                $key = unpack("V6", $copywrite)[6];
                for ($i = 0; $i < 0x200; $i++) {
                    $key *= 0x805;
                    $key++;
                    $key = $key & 0xFF;
                    $qqwry[$i] = chr(ord($qqwry[$i]) ^ $key);
                }
                $qqwry = gzuncompress($qqwry);
                rename(BASE_PATH . "/storage/qqwry.dat", BASE_PATH . "/storage/qqwry.dat.bak");
                $fp = fopen(BASE_PATH . "/storage/qqwry.dat", "wb");
                if ($fp) {
                    fwrite($fp, $qqwry);
                    fclose($fp);
                }
            }
        }

        $iplocation = new QQWry();
        $location = $iplocation->getlocation("8.8.8.8");
        $Userlocation = $location['country'];
        if (iconv('gbk', 'utf-8//IGNORE', $Userlocation) != "美国") {
            unlink(BASE_PATH . "/storage/qqwry.dat");
            rename(BASE_PATH . "/storage/qqwry.dat.bak", BASE_PATH . "/storage/qqwry.dat");
        }

        Job::updatedownload();

    }

//   定时任务开启的情况下，每天自动检测有没有最新版的后端，github源来自Miku
    public static function updatedownload()
    {
        system('cd ' . BASE_PATH . "/public/ssr-download/ && git pull https://github.com/xcxnig/ssr-download.git");
    }


    public static function CheckJob()
    {
        //在线人数检测
        $users = User::where('node_connector', '>', 0)->get();

        $full_alive_ips = Ip::where("datetime", ">=", time() - 60)->orderBy("ip")->get();

        $alive_ipset = array();

        foreach ($full_alive_ips as $full_alive_ip) {
            $full_alive_ip->ip = Tools::getRealIp($full_alive_ip->ip);
            $is_node = Node::where("node_ip", $full_alive_ip->ip)->first();
            if ($is_node) {
                continue;
            }

            if (!isset($alive_ipset[$full_alive_ip->userid])) {
                $alive_ipset[$full_alive_ip->userid] = new \ArrayObject();
            }

            $alive_ipset[$full_alive_ip->userid]->append($full_alive_ip);
        }

        foreach ($users as $user) {
            $alive_ips = (isset($alive_ipset[$user->id]) ? $alive_ipset[$user->id] : new \ArrayObject());
            $ips = array();

            $disconnected_ips = explode(",", $user->disconnect_ip);

            foreach ($alive_ips as $alive_ip) {
                if (!isset($ips[$alive_ip->ip]) && !in_array($alive_ip->ip, $disconnected_ips)) {
                    $ips[$alive_ip->ip] = 1;
                    if ($user->node_connector < count($ips)) {
                        //暂时封禁
                        $isDisconnect = Disconnect::where('id', '=', $alive_ip->ip)->where('userid', '=', $user->id)->first();

                        if ($isDisconnect == null) {
                            $disconnect = new Disconnect();
                            $disconnect->userid = $user->id;
                            $disconnect->ip = $alive_ip->ip;
                            $disconnect->datetime = time();
                            $disconnect->save();

                            if ($user->disconnect_ip == null || $user->disconnect_ip == "") {
                                $user->disconnect_ip = $alive_ip->ip;
                            } else {
                                $user->disconnect_ip .= "," . $alive_ip->ip;
                            }
                            $user->save();
                        }
                    }
                }
            }
        }

        //解封
        $disconnecteds = Disconnect::where("datetime", "<", time() - 300)->get();
        foreach ($disconnecteds as $disconnected) {
            $user = User::where('id', '=', $disconnected->userid)->first();

            $ips = explode(",", $user->disconnect_ip);
            $new_ips = "";
            $first = 1;

            foreach ($ips as $ip) {
                if ($ip != $disconnected->ip && $ip != "") {
                    if ($first == 1) {
                        $new_ips .= $ip;
                        $first = 0;
                    } else {
                        $new_ips .= "," . $ip;
                    }
                }
            }

            $user->disconnect_ip = $new_ips;

            if ($new_ips == "") {
                $user->disconnect_ip = null;
            }

            $user->save();

            $disconnected->delete();
        }

        //自动续费
        $boughts = Bought::where("renew", "<", time())->where("renew", "<>", 0)->get();
        foreach ($boughts as $bought) {
            $user = User::where("id", $bought->userid)->first();

            if ($user == null) {
                $bought->delete();
                continue;
            }

            $shop = Shop::where("id", $bought->shopid)->first();
            if ($shop == null) {
                $bought->delete();
                $subject = Config::get('appName') . "-续费失败";
                $to = $user->email;
                $text = "您好，系统为您自动续费商品时，发现该商品已被下架，为能继续正常使用，建议您登录用户面板购买新的商品。";
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                continue;
            }
            if ($user->money >= $shop->price) {
                $user->money = $user->money - $shop->price;
                $user->save();
                $shop->buy($user, 1);
                $bought->renew = 0;
                $bought->save();

                $bought_new = new Bought();
                $bought_new->userid = $user->id;
                $bought_new->shopid = $shop->id;
                $bought_new->datetime = time();
                $bought_new->renew = time() + $shop->auto_renew * 86400;
                $bought_new->price = $shop->price;
                $bought_new->coupon = "";
                $bought_new->save();

                $subject = Config::get('appName') . "-续费成功";
                $to = $user->email;
                $text = "您好，系统已经为您自动续费，商品名：" . $shop->name . ",金额:" . $shop->price . " 元。";
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }

                if (file_exists(BASE_PATH . "/storage/" . $bought->id . ".renew")) {
                    unlink(BASE_PATH . "/storage/" . $bought->id . ".renew");
                }
            } else {
                if (!file_exists(BASE_PATH . "/storage/" . $bought->id . ".renew")) {
                    $subject = Config::get('appName') . "-续费失败";
                    $to = $user->email;
                    $text = "您好，系统为您自动续费商品名：" . $shop->name . ",金额:" . $shop->price . " 元 时，发现您余额不足，请及时充值。充值后请稍等系统便会自动为您续费。";
                    try {
                        Mail::send($to, $subject, 'news/warn.tpl', [
                            "user" => $user, "text" => $text
                        ], [
                        ]);
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                    $myfile = fopen(BASE_PATH . "/storage/" . $bought->id . ".renew", "w+") or die("Unable to open file!");
                    $txt = "1";
                    fwrite($myfile, $txt);
                    fclose($myfile);
                }
            }
        }

        Ip::where("datetime", "<", time() - 300)->delete();
        UnblockIp::where("datetime", "<", time() - 300)->delete();
        BlockIp::where("datetime", "<", time() - 86400)->delete();
        TelegramSession::where("datetime", "<", time() - 900)->delete();

        $adminUser = User::where("is_admin", "=", "1")->get();

        //节点掉线检测
        if (Config::get("enable_detect_offline") == "true") {
            $nodes = Node::all();

            foreach ($nodes as $node) {
                if ($node->isNodeOnline() === false && !file_exists(BASE_PATH . "/storage/" . $node->id . ".offline")) {

                    if (Config::get('useScFtqq') == 'true' && Config::get('enable_detect_offline_useScFtqq') == 'true') {
                        $ScFtqq_SCKEY = Config::get('ScFtqq_SCKEY');
                        $text = "管理员您好，系统发现节点 " . $node->name . " 掉线了，请您及时处理。";
                        $postdata = http_build_query(
                            array(
                                'text' => Config::get('appName') . "-节点掉线了",
                                'desp' => $text
                            ));
                        $opts = array('http' =>
                            array(
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $postdata
                            ));
                        $context = stream_context_create($opts);
                        file_get_contents('https://sc.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                    }

                    foreach ($adminUser as $user) {
                        echo "Send offline mail to user: " . $user->id;
                        $subject = Config::get('appName') . "-系统警告";
                        $to = $user->email;
                        $text = "管理员您好，系统发现节点 " . $node->name . " 掉线了，请您及时处理。";
                        try {
                            Mail::send($to, $subject, 'news/warn.tpl', [
                                "user" => $user, "text" => $text
                            ], [
                            ]);
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }

                        if (Config::get('enable_cloudxns') == 'true' && ($node->sort == 0 || $node->sort == 10 || $node->sort == 12 || $node->sort == 13)) {
                            $api = new Api();
                            $api->setApiKey(Config::get("cloudxns_apikey"));//修改成自己API KEY
                            $api->setSecretKey(Config::get("cloudxns_apisecret"));//修改成自己的SECERET KEY

                            $api->setProtocol(true);

                            $domain_json = json_decode($api->domain->domainList());

                            foreach ($domain_json->data as $domain) {
                                if (strpos($domain->domain, Config::get('cloudxns_domain')) !== false) {
                                    $domain_id = $domain->id;
                                }
                            }

                            $record_json = json_decode($api->record->recordList($domain_id, 0, 0, 2000));

                            foreach ($record_json->data as $record) {
                                if (($record->host . "." . Config::get('cloudxns_domain')) == $node->server) {
                                    $record_id = $record->record_id;

                                    $Temp_node = Node::where('node_class', '<=', $node->node_class)->where(
                                        function ($query) use ($node) {
                                            $query->where("node_group", "=", $node->node_group)
                                                ->orWhere("node_group", "=", 0);
                                        }
                                    )->whereRaw('UNIX_TIMESTAMP()-`node_heartbeat`<300')->first();

                                    if ($Temp_node != null) {
                                        $api->record->recordUpdate($domain_id, $record->host, $Temp_node->server, 'CNAME', 55, 60, 1, '', $record_id);
                                    }

                                    $notice_text = "喵喵喵~ " . $node->name . " 节点掉线了喵~域名解析被切换到了 " . $Temp_node->name . " 上了喵~";
                                }
                            }
                        } else {
                            $notice_text = "喵喵喵~ " . $node->name . " 节点掉线了喵~";
                        }
                    }

                    Telegram::Send($notice_text);

                    $myfile = fopen(BASE_PATH . "/storage/" . $node->id . ".offline", "w+") or die("Unable to open file!");
                    $txt = "1";
                    fwrite($myfile, $txt);
                    fclose($myfile);
                } elseif ($node->isNodeOnline() === true && file_exists(BASE_PATH . "/storage/" . $node->id . ".offline")) {
                    if (Config::get('useScFtqq') == 'true' && Config::get('enable_detect_offline_useScFtqq') == 'true') {
                        $ScFtqq_SCKEY = Config::get('ScFtqq_SCKEY');
                        $text = "管理员您好，系统发现节点 " . $node->name . " 恢复上线了。";
                        $postdata = http_build_query(
                            array(
                                'text' => Config::get('appName') . "-节点掉线了",
                                'desp' => $text
                            ));

                        $opts = array('http' =>
                            array(
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $postdata
                            ));
                        $context = stream_context_create($opts);
                        file_get_contents('https://sc.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                    }
                    foreach ($adminUser as $user) {
                        echo "Send offline mail to user: " . $user->id;
                        $subject = Config::get('appName') . "-系统提示";
                        $to = $user->email;
                        $text = "管理员您好，系统发现节点 " . $node->name . " 恢复上线了。";
                        try {
                            Mail::send($to, $subject, 'news/warn.tpl', [
                                "user" => $user, "text" => $text
                            ], [
                            ]);
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }

                        if (Config::get('enable_cloudxns') == 'true' && ($node->sort == 0 || $node->sort == 10 || $node->sort == 12 || $node->sort == 13)) {
                            $api = new Api();
                            $api->setApiKey(Config::get("cloudxns_apikey"));//修改成自己API KEY
                            $api->setSecretKey(Config::get("cloudxns_apisecret"));//修改成自己的SECERET KEY
                            $api->setProtocol(true);

                            $domain_json = json_decode($api->domain->domainList());

                            foreach ($domain_json->data as $domain) {
                                if (strpos($domain->domain, Config::get('cloudxns_domain')) !== false) {
                                    $domain_id = $domain->id;
                                }
                            }

                            $record_json = json_decode($api->record->recordList($domain_id, 0, 0, 2000));

                            foreach ($record_json->data as $record) {
                                if (($record->host . "." . Config::get('cloudxns_domain')) == $node->server) {
                                    $record_id = $record->record_id;

                                    $api->record->recordUpdate($domain_id, $record->host, $node->getNodeIp(), 'A', 55, 600, 1, '', $record_id);
                                }
                            }

                            $notice_text = "喵喵喵~ " . $node->name . " 节点恢复了喵~域名解析被切换回来了喵~";
                        } else {
                            $notice_text = "喵喵喵~ " . $node->name . " 节点恢复了喵~";
                        }
                    }

                    Telegram::Send($notice_text);

                    unlink(BASE_PATH . "/storage/" . $node->id . ".offline");
                }
            }
        }


        //登录地检测
        if (Config::get("login_warn") == "true") {
            $iplocation = new QQWry();
            $Logs = LoginIp::where("datetime", ">", time() - 60)->get();
            foreach ($Logs as $log) {
                $UserLogs = LoginIp::where("userid", "=", $log->userid)->orderBy("id", "desc")->take(2)->get();
                if ($UserLogs->count() == 2) {
                    $i = 0;
                    $Userlocation = "";
                    foreach ($UserLogs as $userlog) {
                        if ($i == 0) {
                            $location = $iplocation->getlocation($userlog->ip);
                            $ip = $userlog->ip;
                            $Userlocation = $location['country'];
                            $i++;
                        } else {
                            $location = $iplocation->getlocation($userlog->ip);
                            $nodes = Node::where("node_ip", "LIKE", $ip . '%')->first();
                            $nodes2 = Node::where("node_ip", "LIKE", $userlog->ip . '%')->first();
                            if ($Userlocation != $location['country'] && $nodes == null && $nodes2 == null) {
                                $user = User::where("id", "=", $userlog->userid)->first();
                                echo "Send warn mail to user: " . $user->id . "-" . iconv('gbk', 'utf-8//IGNORE', $Userlocation) . "-" . iconv('gbk', 'utf-8//IGNORE', $location['country']);
                                $subject = Config::get('appName') . "-系统警告";
                                $to = $user->email;
                                $text = "您好，系统发现您的账号在 " . iconv('gbk', 'utf-8//IGNORE', $Userlocation) . " 有异常登录，请您自己自行核实登录行为。有异常请及时修改密码。";
                                try {
                                    Mail::send($to, $subject, 'news/warn.tpl', [
                                        "user" => $user, "text" => $text
                                    ], [
                                    ]);
                                } catch (\Exception $e) {
                                    echo $e->getMessage();
                                }
                            }
                        }
                    }
                }
            }
        }

        $users = User::all();
        foreach ($users as $user) {
            if (($user->transfer_enable <= $user->u + $user->d || $user->enable == 0 || (strtotime($user->expire_in) < time() && strtotime($user->expire_in) > 644447105)) && RadiusBan::where("userid", $user->id)->first() == null) {
                $rb = new RadiusBan();
                $rb->userid = $user->id;
                $rb->save();
                Radius::Delete($user->email);
            }

            if (strtotime($user->expire_in) < time() && !file_exists(BASE_PATH . "/storage/" . $user->id . ".expire_in")) {
                $user->transfer_enable = 0;
                $user->u = 0;
                $user->d = 0;
                $user->last_day_t = 0;

                $subject = Config::get('appName') . "-您的用户账户已经过期了";
                $to = $user->email;
                $text = "您好，系统发现您的账号已经过期了。";
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                $myfile = fopen(BASE_PATH . "/storage/" . $user->id . ".expire_in", "w+") or die("Unable to open file!");
                $txt = "1";
                fwrite($myfile, $txt);
                fclose($myfile);
            } elseif (strtotime($user->expire_in) > time() && file_exists(BASE_PATH . "/storage/" . $user->id . ".expire_in")) {
                unlink(BASE_PATH . "/storage/" . $user->id . ".expire_in");
            }


            //余量不足检测
            if (!file_exists(BASE_PATH . "/storage/traffic_notified/")) {
                mkdir(BASE_PATH . "/storage/traffic_notified/");
            }
            if (Config::get('notify_limit_mode') != 'false') {
                $user_traffic_left = $user->transfer_enable - $user->u - $user->d;
                $under_limit = 'false';

                if ($user->transfer_enable != 0) {
                    if (Config::get('notify_limit_mode') == 'per' &&
                        $user_traffic_left / $user->transfer_enable * 100 < Config::get('notify_limit_value')) {
                        $under_limit = 'true';
                        $unit_text = '%';
                    }
                } else if (Config::get('notify_limit_mode') == 'mb' &&
                    Tools::flowToMB($user_traffic_left) < Config::get('notify_limit_value')) {
                    $under_limit = 'true';
                    $unit_text = 'MB';
                }

                if ($under_limit == 'true' && !file_exists(BASE_PATH . "/storage/traffic_notified/" . $user->id . ".userid")) {
                    $subject = Config::get('appName') . " - 您的剩余流量过低";
                    $to = $user->email;
                    $text = '您好，系统发现您剩余流量已经低于 ' . Config::get('notify_limit_value') . $unit_text . ' 。';
                    try {
                        Mail::send($to, $subject, 'news/warn.tpl', [
                            "user" => $user, "text" => $text
                        ], [
                        ]);
                        $myfile = fopen(BASE_PATH . "/storage/traffic_notified/" . $user->id . ".userid", "w+") or die("Unable to open file!");
                        $txt = "1";
                        fwrite($myfile, $txt);
                        fclose($myfile);
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                } else if ($under_limit == 'false') {
                    if (file_exists(BASE_PATH . "/storage/traffic_notified/" . $user->id . ".userid")) {
                        unlink(BASE_PATH . "/storage/traffic_notified/" . $user->id . ".userid");
                    }
                }
            }

            if (Config::get('account_expire_delete_days') >= 0 &&
                strtotime($user->expire_in) + Config::get('account_expire_delete_days') * 86400 < time()
            ) {
                $subject = Config::get('appName') . "-您的用户账户已经被删除了";
                $to = $user->email;
                $text = "您好，系统发现您的账户已经过期 " . Config::get('account_expire_delete_days') . " 天了，帐号已经被删除。";
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }

                $user->kill_user();
                continue;
            }


            if (Config::get('auto_clean_uncheck_days') > 0 &&
                max($user->last_check_in_time, strtotime($user->reg_date)) + (Config::get('auto_clean_uncheck_days') * 86400) < time() &&
                $user->class == 0 &&
                $user->money <= Config::get('auto_clean_min_money')
            ) {
                $subject = Config::get('appName') . "-您的用户账户已经被删除了";
                $to = $user->email;
                $text = "您好，系统发现您的账号已经 " . Config::get('auto_clean_uncheck_days') . " 天没签到了，帐号已经被删除。";
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                $user->kill_user();
                continue;
            }

            if (Config::get('auto_clean_unused_days') > 0 &&
                max($user->t, strtotime($user->reg_date)) + (Config::get('auto_clean_unused_days') * 86400) < time() &&
                $user->class == 0 &&
                $user->money <= Config::get('auto_clean_min_money')
            ) {
                $subject = Config::get('appName') . "-您的用户账户已经被删除了";
                $to = $user->email;
                $text = "您好，系统发现您的账号已经 " . Config::get('auto_clean_unused_days') . " 天没使用了，帐号已经被删除。";
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                $user->kill_user();
                continue;
            }

            if ($user->class != 0 &&
                strtotime($user->class_expire) < time() &&
                strtotime($user->class_expire) > 1420041600
            ) {
                $text = '您好，系统发现您的账号等级已经过期了。';
                $reset_traffic = Config::get('class_expire_reset_traffic');
                if ($reset_traffic >= 0) {
                    $user->transfer_enable = Tools::toGB($reset_traffic);
                    $user->u = 0;
                    $user->d = 0;
                    $user->last_day_t = 0;
                    $text .= '流量已经被重置为' . $reset_traffic . 'GB';
                }
                $subject = Config::get('appName') . "-您的账户等级已经过期了";
                $to = $user->email;
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }

                $user->class = 0;
            }

            $user->save();
        }

        $rbusers = RadiusBan::all();
        foreach ($rbusers as $sinuser) {
            $user = User::find($sinuser->userid);

            if ($user == null) {
                $sinuser->delete();
                continue;
            }

            if ($user->enable == 1 && (strtotime($user->expire_in) > time() || strtotime($user->expire_in) < 644447105) && $user->transfer_enable > $user->u + $user->d) {
                $sinuser->delete();
                Radius::Add($user, $user->passwd);
            }
        }
    }

    public static function detectGFW()
    {
        //节点被墙检测
        $last_time = file_get_contents(BASE_PATH . "/storage/last_detect_gfw_time");
        for ($count = 1; $count <= 12; $count++) {
            if (time() - $last_time >= Config::get("detect_gfw_interval")) {
                $file_interval = fopen(BASE_PATH . "/storage/last_detect_gfw_time", "w");
                fwrite($file_interval, time());
                fclose($file_interval);
                $nodes = Node::all();
                $adminUser = User::where("is_admin", "=", "1")->get();
                foreach ($nodes as $node) {
                    if ($node->node_ip == "" ||
                        $node->node_ip == null ||
                        file_exists(BASE_PATH . "/storage/" . $node->id . "offline") == true) {
                        continue;
                    }
                    $api_url = Config::get("detect_gfw_url");
                    $api_url = str_replace('{ip}', $node->node_ip, $api_url);
                    $api_url = str_replace('{port}', Config::get('detect_gfw_port'), $api_url);
                    //因为考虑到有v2ray之类的节点，所以不得不使用ip作为参数
                    $result_tcping = false;
                    $detect_time = Config::get("detect_gfw_count");
                    for ($i = 1; $i <= $detect_time; $i++) {
                        $json_tcping = json_decode(file_get_contents($api_url), true);
                        if (eval('return ' . Config::get('detect_gfw_judge') . ';')) {
                            $result_tcping = true;
                            break;
                        }
                    }
                    if ($result_tcping == false) {
                        //被墙了
                        echo($node->id . ":false" . PHP_EOL);
                        //判断有没有发送过邮件
                        if (file_exists(BASE_PATH . "/storage/" . $node->id . ".gfw")) {
                            continue;
                        }
                        foreach ($adminUser as $user) {
                            echo "Send gfw mail to user: " . $user->id . "-";
                            $subject = Config::get('appName') . "-系统警告";
                            $to = $user->email;
                            $text = "管理员您好，系统发现节点 " . $node->name . " 被墙了，请您及时处理。";
                            try {
                                Mail::send($to, $subject, 'news/warn.tpl', [
                                    "user" => $user, "text" => $text
                                ], [
                                ]);
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                            if (Config::get('enable_cloudxns') == 'true' && ($node->sort == 0 || $node->sort == 10 || $node->sort == 12 || $node->sort == 13)) {
                                $api = new Api();
                                $api->setApiKey(Config::get("cloudxns_apikey"));
                                //修改成自己API KEY
                                $api->setSecretKey(Config::get("cloudxns_apisecret"));
                                //修改成自己的SECERET KEY
                                $api->setProtocol(true);
                                $domain_json = json_decode($api->domain->domainList());
                                foreach ($domain_json->data as $domain) {
                                    if (strpos($domain->domain, Config::get('cloudxns_domain')) !== false) {
                                        $domain_id = $domain->id;
                                    }
                                }
                                $record_json = json_decode($api->record->recordList($domain_id, 0, 0, 2000));
                                foreach ($record_json->data as $record) {
                                    if (($record->host . "." . Config::get('cloudxns_domain')) == $node->server) {
                                        $record_id = $record->record_id;
                                        $Temp_node = Node::where('node_class', '<=', $node->node_class)->where(
                                            function ($query) use ($node) {
                                                $query->where("node_group", "=", $node->node_group)
                                                    ->orWhere("node_group", "=", 0);
                                            })->whereRaw('UNIX_TIMESTAMP()-`node_heartbeat`<300')->first();
                                        if ($Temp_node != null) {
                                            $api->record->recordUpdate($domain_id, $record->host, $Temp_node->server, 'CNAME', 55, 60, 1, '', $record_id);
                                        }
                                        $notice_text = "喵喵喵~ " . $node->name . " 节点被墙了喵~域名解析被切换到了 " . $Temp_node->name . " 上了喵~";
                                    }
                                }
                            } else {
                                $notice_text = "喵喵喵~ " . $node->name . " 节点被墙了喵~";
                            }
                        }
                        Telegram::Send($notice_text);
                        $file_node = fopen(BASE_PATH . "/storage/" . $node->id . ".gfw", "w+");
                        fclose($file_node);
                    } else {
                        //没有被墙
                        echo($node->id . ":true" . PHP_EOL);
                        if (file_exists(BASE_PATH . "/storage/" . $node->id . ".gfw") == false) {
                            continue;
                        }
                        foreach ($adminUser as $user) {
                            echo "Send gfw mail to user: " . $user->id . "-";
                            $subject = Config::get('appName') . "-系统提示";
                            $to = $user->email;
                            $text = "管理员您好，系统发现节点 " . $node->name . " 溜出墙了。";
                            try {
                                Mail::send($to, $subject, 'news/warn.tpl', [
                                    "user" => $user, "text" => $text
                                ], [
                                ]);
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                            if (Config::get('enable_cloudxns') == 'true' && ($node->sort == 0 || $node->sort == 10 || $node->sort == 12 || $node->sort == 13)) {
                                $api = new Api();
                                $api->setApiKey(Config::get("cloudxns_apikey"));
                                //修改成自己API KEY
                                $api->setSecretKey(Config::get("cloudxns_apisecret"));
                                //修改成自己的SECERET KEY
                                $api->setProtocol(true);
                                $domain_json = json_decode($api->domain->domainList());
                                foreach ($domain_json->data as $domain) {
                                    if (strpos($domain->domain, Config::get('cloudxns_domain')) !== false) {
                                        $domain_id = $domain->id;
                                    }
                                }
                                $record_json = json_decode($api->record->recordList($domain_id, 0, 0, 2000));
                                foreach ($record_json->data as $record) {
                                    if (($record->host . "." . Config::get('cloudxns_domain')) == $node->server) {
                                        $record_id = $record->record_id;
                                        $api->record->recordUpdate($domain_id, $record->host, $node->getNodeIp(), 'A', 55, 600, 1, '', $record_id);
                                    }
                                }
                                $notice_text = "喵喵喵~ " . $node->name . " 节点恢复了喵~域名解析被切换回来了喵~";
                            } else {
                                $notice_text = "喵喵喵~ " . $node->name . " 节点恢复了喵~";
                            }
                        }
                        Telegram::Send($notice_text);
                        unlink(BASE_PATH . "/storage/" . $node->id . ".gfw");
                    }
                }
                break;
            } else {
                echo($node->id . "interval skip" . PHP_EOL);
                sleep(3);
            }
        }
    }

}
