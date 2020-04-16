<?php

namespace App\Command;

use App\Models\{
    Ip,
    Node,
    User,
    Shop,
    Token,
    Bought,
    BlockIp,
    LoginIp,
    DetectLog,
    UnblockIp,
    Speedtest,
    RadiusBan,
    TrafficLog,
    Disconnect,
    EmailVerify,
    DetectBanLog,
    NodeInfoLog,
    NodeOnlineLog,
    TelegramTasks,
    TelegramSession,
    UserSubscribeLog
};
use App\Services\{
    Mail,
    Config
};
use App\Utils\{
    GA,
    QQWry,
    Tools,
    Radius,
    Telegram,
    DatatablesHelper
};
use Exception;
use ArrayObject;
use RuntimeException;
use Ramsey\Uuid\Uuid;

class Job
{
    public static function syncnode()
    {
        $nodes = Node::all();
        $allNodeID = [];

        foreach ($nodes as $node) {
            $allNodeID[] = $node->id;
            $nodeSort = [2, 5, 9, 999];     // 无需更新 IP 的节点类型
            if (!in_array($node->sort, $nodeSort)) {
                $server = $node->getOutServer();
                if (!Tools::is_ip($server) && $node->changeNodeIp($server)) {
                    $node->save();
                }
                if (in_array($node->sort, array(0, 10, 12))) {
                    Tools::updateRelayRuleIp($node);
                }
            }
        }

        // 删除无效的中转
        $allNodeID = implode(', ', $allNodeID);
        $datatables = new DatatablesHelper();
        $datatables->query('DELETE FROM `relay` WHERE `source_node_id` NOT IN(' . $allNodeID . ') OR `dist_node_id` NOT IN(' . $allNodeID . ')');
    }

    public static function backup($full = false)
    {
        ini_set('memory_limit', '-1');
        $to = $_ENV['auto_backup_email'];
        if ($to == null) {
            return false;
        }
        if (!mkdir('/tmp/ssmodbackup/') && !is_dir('/tmp/ssmodbackup/')) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', '/tmp/ssmodbackup/'));
        }
        $db_address_array = explode(':', $_ENV['db_host']);
        if ($full) {
            system('mysqldump --user=' . $_ENV['db_username'] . ' --password=' . $_ENV['db_password'] . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' ' . $_ENV['db_database'] . ' > /tmp/ssmodbackup/mod.sql');
        } else {
            system(
                'mysqldump --user=' . $_ENV['db_username'] . ' --password=' . $_ENV['db_password'] . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' ' . $_ENV['db_database'] . ' announcement auto blockip bought code coupon disconnect_ip link login_ip payback radius_ban shop speedtest ss_invite_code ss_node ss_password_reset ticket unblockip user user_token email_verify detect_list relay paylist> /tmp/ssmodbackup/mod.sql',
                $ret
            );
            system(
                'mysqldump --opt --user=' . $_ENV['db_username'] . ' --password=' . $_ENV['db_password'] . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' -d ' . $_ENV['db_database'] . ' alive_ip ss_node_info ss_node_online_log user_traffic_log detect_log telegram_session >> /tmp/ssmodbackup/mod.sql',
                $ret
            );
            if ($_ENV['enable_radius'] == true) {
                $db_address_array = explode(':', $_ENV['radius_db_host']);
                system(
                    'mysqldump --user=' . $_ENV['radius_db_user'] . ' --password=' . $_ENV['radius_db_password'] . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . '' . $_ENV['radius_db_database'] . '> /tmp/ssmodbackup/radius.sql',
                    $ret
                );
            }
        }

        system('cp ' . BASE_PATH . '/config/.config.php /tmp/ssmodbackup/configbak.php', $ret);
        echo $ret;
        system('zip -r /tmp/ssmodbackup.zip /tmp/ssmodbackup/* -P ' . $_ENV['auto_backup_passwd'], $ret);
        $subject = $_ENV['appName'] . '-备份成功';
        $text = '您好，系统已经为您自动备份，请查看附件，用您设定的密码解压。';
        try {
            Mail::send($to, $subject, 'news/backup.tpl', [
                'text' => $text
            ], [
                '/tmp/ssmodbackup.zip'
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        system('rm -rf /tmp/ssmodbackup', $ret);
        system('rm /tmp/ssmodbackup.zip', $ret);

        if ($_ENV['backup_notify'] == true) {
            Telegram::Send('备份完毕了喵~今天又是安全祥和的一天呢。');
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
        echo 'ok';
    }

    public static function syncnasnode()
    {
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $rule = preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $node->server);
            if (!$rule && (in_array($node->sort, array(0, 10, 12, 13)))) {
                $ip = gethostbyname($node->server);
                $node->node_ip = $ip;
                $node->save();

                Radius::AddNas($node->node_ip, $node->server);
            }
        }
    }

    public static function DailyJob()
    {
        ini_set('memory_limit', '-1');
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $nodeSort = [1, 2, 5, 9, 999];     // 无需重置流量的节点类型
            if (!in_array($node->sort, $nodeSort)) {
                if (date('d') == $node->bandwidthlimit_resetday) {
                    $node->node_bandwidth = 0;
                    $node->save();
                }
            }
        }

        // 清理订阅记录
        UserSubscribeLog::where('request_time', '<', date('Y-m-d H:i:s', time() - 86400 * (int) $_ENV['subscribeLog_keep_days']))->delete();

        Token::where('expire_time', '<', time())->delete();

        NodeInfoLog::where('log_time', '<', time() - 86400 * 3)->delete();
        NodeOnlineLog::where('log_time', '<', time() - 86400 * 3)->delete();
        TrafficLog::where('log_time', '<', time() - 86400 * 3)->delete();
        DetectLog::where('datetime', '<', time() - 86400 * 3)->delete();
        Speedtest::where('datetime', '<', time() - 86400 * 3)->delete();
        EmailVerify::where('expire_in', '<', time() - 86400 * 3)->delete();
        system('rm ' . BASE_PATH . '/storage/*.png', $ret);

        if (Config::getconfig('Telegram.bool.DailyJob')) {
            Telegram::Send(Config::getconfig('Telegram.string.DailyJob'));
        }

        //auto reset
        $boughts = Bought::all();
        $boughted_users = array();
        foreach ($boughts as $bought) {
            $user = User::where('id', $bought->userid)->first();

            if ($user == null) {
                $bought->delete();
                continue;
            }

            $shop = Shop::where('id', $bought->shopid)->first();

            if ($shop == null) {
                $bought->delete();
                continue;
            }

            if ($shop->reset() != 0 && $shop->reset_value() != 0 && $shop->reset_exp() != 0) {
                $boughted_users[] = $bought->userid;
                if ((time() - $shop->reset_exp() * 86400 < $bought->datetime) && (int) ((time() - $bought->datetime) / 86400) % $shop->reset() == 0 && (int) ((time() - $bought->datetime) / 86400) != 0) {
                    echo ('流量重置-' . $user->id . "\n");
                    $user->transfer_enable = Tools::toGB($shop->reset_value());
                    $user->u = 0;
                    $user->d = 0;
                    $user->last_day_t = 0;
                    $user->save();
                    $user->sendMail(
                        $_ENV['appName'] . '-您的流量被重置了',
                        'news/warn.tpl',
                        [
                            'text' => '您好，根据您所订购的订单 ID:' . $bought->id . '，流量已经被重置为' . $shop->reset_value() . 'GB'
                        ],
                        []
                    );
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
            if (date('d') == $user->auto_reset_day) {
                $user->u = 0;
                $user->d = 0;
                $user->last_day_t = 0;
                $user->transfer_enable = $user->auto_reset_bandwidth * 1024 * 1024 * 1024;
                $user->save();
                $user->sendMail(
                    $_ENV['appName'] . '-您的流量被重置了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，根据管理员的设置，流量已经被重置为' . $user->auto_reset_bandwidth . 'GB'
                    ],
                    []
                );
            }
        }

        $qqwry = file_get_contents('https://qqwry.mirror.noc.one/QQWry.Dat?from=sspanel_uim');
        if ($qqwry != '') {
            rename(BASE_PATH . '/storage/qqwry.dat', BASE_PATH . '/storage/qqwry.dat.bak');
            $fp = fopen(BASE_PATH . '/storage/qqwry.dat', 'wb');
            if ($fp) {
                fwrite($fp, $qqwry);
                fclose($fp);
            }
        }

        $iplocation = new QQWry();
        $location = $iplocation->getlocation('8.8.8.8');
        $Userlocation = $location['country'];
        if (iconv('gbk', 'utf-8//IGNORE', $Userlocation) != '美国') {
            unlink(BASE_PATH . '/storage/qqwry.dat');
            rename(BASE_PATH . '/storage/qqwry.dat.bak', BASE_PATH . '/storage/qqwry.dat');
        }

        self::updatedownload();
    }

    //   定时任务开启的情况下，每天自动检测有没有最新版的后端，github源来自Miku
    public static function updatedownload()
    {
        system('cd ' . BASE_PATH . '/public/ssr-download/ && git pull https://github.com/xcxnig/ssr-download.git && git gc');
    }

    public static function CheckJob()
    {
        //在线人数检测
        $users = User::where('node_connector', '>', 0)->get();

        $full_alive_ips = Ip::where('datetime', '>=', time() - 60)->orderBy('ip')->get();

        $alive_ipset = array();

        foreach ($full_alive_ips as $full_alive_ip) {
            $full_alive_ip->ip = Tools::getRealIp($full_alive_ip->ip);
            $is_node = Node::where('node_ip', $full_alive_ip->ip)->first();
            if ($is_node) {
                continue;
            }

            if (!isset($alive_ipset[$full_alive_ip->userid])) {
                $alive_ipset[$full_alive_ip->userid] = new ArrayObject();
            }

            $alive_ipset[$full_alive_ip->userid]->append($full_alive_ip);
        }

        foreach ($users as $user) {
            $alive_ips = ($alive_ipset[$user->id] ?? new ArrayObject());
            $ips = array();

            $disconnected_ips = explode(',', $user->disconnect_ip);

            foreach ($alive_ips as $alive_ip) {
                if (!isset($ips[$alive_ip->ip]) && !in_array($alive_ip->ip, $disconnected_ips)) {
                    $ips[$alive_ip->ip] = 1;
                    if ($user->node_connector < count($ips)) {
                        //暂时封禁
                        $isDisconnect = Disconnect::where('id', '=', $alive_ip->ip)->where(
                            'userid',
                            '=',
                            $user->id
                        )->first();

                        if ($isDisconnect == null) {
                            $disconnect = new Disconnect();
                            $disconnect->userid = $user->id;
                            $disconnect->ip = $alive_ip->ip;
                            $disconnect->datetime = time();
                            $disconnect->save();

                            if ($user->disconnect_ip == null || $user->disconnect_ip == '') {
                                $user->disconnect_ip = $alive_ip->ip;
                            } else {
                                $user->disconnect_ip .= ',' . $alive_ip->ip;
                            }
                            $user->save();
                        }
                    }
                }
            }
        }

        //解封
        $disconnecteds = Disconnect::where('datetime', '<', time() - 300)->get();
        foreach ($disconnecteds as $disconnected) {
            $user = User::where('id', '=', $disconnected->userid)->first();

            $ips = explode(',', $user->disconnect_ip);
            $new_ips = '';
            $first = 1;

            foreach ($ips as $ip) {
                if ($ip != $disconnected->ip && $ip != '') {
                    if ($first == 1) {
                        $new_ips .= $ip;
                        $first = 0;
                    } else {
                        $new_ips .= ',' . $ip;
                    }
                }
            }

            $user->disconnect_ip = $new_ips;

            if ($new_ips == '') {
                $user->disconnect_ip = null;
            }

            $user->save();

            $disconnected->delete();
        }

        //自动续费
        $boughts = Bought::where('renew', '<', time())->where('renew', '<>', 0)->get();
        foreach ($boughts as $bought) {
            /** @var Bought $bought */
            $user = User::where('id', $bought->userid)->first();
            if ($user == null) {
                $bought->delete();
                continue;
            }

            $shop = Shop::where('id', $bought->shopid)->first();
            if ($shop == null) {
                $bought->delete();
                $user->sendMail(
                    $_ENV['appName'] . '-续费失败',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统为您自动续费商品时，发现该商品已被下架，为能继续正常使用，建议您登录用户面板购买新的商品。'
                    ],
                    []
                );
                $bought->is_notified = true;
                $bought->save();
                continue;
            }
            if ($user->money >= $shop->price) {
                $user->money -= $shop->price;
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
                $bought_new->coupon = '';
                $bought_new->save();

                $user->sendMail(
                    $_ENV['appName'] . '-续费成功',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统已经为您自动续费，商品名：' . $shop->name . ',金额:' . $shop->price . ' 元。'
                    ],
                    []
                );

                $bought->is_notified = true;
                $bought->save();
            } elseif ($bought->is_notified == false) {
                $user->sendMail(
                    $_ENV['appName'] . '-续费失败',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统为您自动续费商品名：' . $shop->name . ',金额:' . $shop->price . ' 元 时，发现您余额不足，请及时充值。充值后请稍等系统便会自动为您续费。'
                    ],
                    []
                );
                $bought->is_notified = true;
                $bought->save();
            }
        }

        Ip::where('datetime', '<', time() - 300)->delete();
        UnblockIp::where('datetime', '<', time() - 300)->delete();
        BlockIp::where('datetime', '<', time() - 86400)->delete();
        TelegramSession::where('datetime', '<', time() - 900)->delete();

        $adminUser = User::where('is_admin', '=', '1')->get();

        //节点掉线检测
        if ($_ENV['enable_detect_offline'] == true) {
            echo '节点掉线检测开始' . PHP_EOL;
            $nodes = Node::all();
            foreach ($nodes as $node) {
                if ($node->isNodeOnline() === false && $node->online == true) {
                    if ($_ENV['useScFtqq'] == true && $_ENV['enable_detect_offline_useScFtqq'] == true) {
                        $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
                        $text = '管理员您好，系统发现节点 ' . $node->name . ' 掉线了，请您及时处理。';
                        $postdata = http_build_query(
                            array(
                                'text' => $_ENV['appName'] . '-节点掉线了',
                                'desp' => $text
                            )
                        );
                        $opts = array(
                            'http' =>
                            array(
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $postdata
                            )
                        );
                        $context = stream_context_create($opts);
                        file_get_contents('https://sc.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                    }

                    foreach ($adminUser as $user) {
                        echo 'Send offline mail to user: ' . $user->id . PHP_EOL;
                        $user->sendMail(
                            $_ENV['appName'] . '-系统警告',
                            'news/warn.tpl',
                            [
                                'text' => '管理员您好，系统发现节点 ' . $node->name . ' 掉线了，请您及时处理。'
                            ],
                            []
                        );
                        $notice_text = str_replace(
                            '%node_name%',
                            $node->name,
                            Config::getconfig('Telegram.string.NodeOffline')
                        );
                    }

                    if (Config::getconfig('Telegram.bool.NodeOffline')) {
                        Telegram::Send($notice_text);
                    }

                    $node->online = false;
                    $node->save();
                } elseif ($node->isNodeOnline() === true && $node->online == false) {
                    if ($_ENV['useScFtqq'] == true && $_ENV['enable_detect_offline_useScFtqq'] == true) {
                        $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
                        $text = '管理员您好，系统发现节点 ' . $node->name . ' 恢复上线了。';
                        $postdata = http_build_query(
                            array(
                                'text' => $_ENV['appName'] . '-节点恢复上线了',
                                'desp' => $text
                            )
                        );

                        $opts = array(
                            'http' =>
                            array(
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $postdata
                            )
                        );
                        $context = stream_context_create($opts);
                        file_get_contents('https://sc.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                    }
                    foreach ($adminUser as $user) {
                        echo 'Send offline mail to user: ' . $user->id . PHP_EOL;
                        $user->sendMail(
                            $_ENV['appName'] . '-系统提示',
                            'news/warn.tpl',
                            [
                                'text' => '管理员您好，系统发现节点 ' . $node->name . ' 恢复上线了。'
                            ],
                            []
                        );
                        $notice_text = str_replace(
                            '%node_name%',
                            $node->name,
                            Config::getconfig('Telegram.string.NodeOnline')
                        );
                    }

                    if (Config::getconfig('Telegram.bool.NodeOnline')) {
                        Telegram::Send($notice_text);
                    }

                    $node->online = true;
                    $node->save();
                }
            }
            echo '节点掉线检测结束' . PHP_EOL;
        }


        //登录地检测
        if ($_ENV['login_warn'] == true) {
            echo '异常登录检测开始' . PHP_EOL;
            $iplocation = new QQWry();
            $Logs = LoginIp::where('datetime', '>', time() - 60)->get();
            foreach ($Logs as $log) {
                $UserLogs = LoginIp::where('userid', '=', $log->userid)->orderBy('id', 'desc')->take(2)->get();
                if ($UserLogs->count() == 2) {
                    $i = 0;
                    $Userlocation = '';
                    foreach ($UserLogs as $userlog) {
                        if ($i == 0) {
                            $location = $iplocation->getlocation($userlog->ip);
                            $ip = $userlog->ip;
                            $Userlocation = $location['country'];
                            $i++;
                        } else {
                            $location = $iplocation->getlocation($userlog->ip);
                            $nodes = Node::where('node_ip', 'LIKE', $ip . '%')->first();
                            $nodes2 = Node::where('node_ip', 'LIKE', $userlog->ip . '%')->first();
                            if ($Userlocation != $location['country'] && $nodes == null && $nodes2 == null) {
                                $user = User::where('id', '=', $userlog->userid)->first();
                                echo 'Send warn mail to user: ' . $user->id . '-' . iconv(
                                    'gbk',
                                    'utf-8//IGNORE',
                                    $Userlocation
                                ) . '-' . iconv('gbk', 'utf-8//IGNORE', $location['country']);
                                $text = '您好，系统发现您的账号在 ' . iconv(
                                    'gbk',
                                    'utf-8//IGNORE',
                                    $Userlocation
                                ) . ' 有异常登录，请您自己自行核实登录行为。有异常请及时修改密码。';
                                $user->sendMail(
                                    $_ENV['appName'] . '-系统警告',
                                    'news/warn.tpl',
                                    [
                                        'text' => $text
                                    ],
                                    []
                                );
                            }
                        }
                    }
                }
            }
            echo '异常登录检测结束' . PHP_EOL;
        }

        $users = User::all();
        foreach ($users as $user) {
            $user->uuid = Uuid::uuid3(
                Uuid::NAMESPACE_DNS,
                strval($user->id) . '|' . $user->passwd
            )->toString();
            $user->save();
            if (($user->transfer_enable <= $user->u + $user->d || $user->enable == 0 || (strtotime($user->expire_in) < time() && strtotime($user->expire_in) > 644447105)) && RadiusBan::where(
                'userid',
                $user->id
            )->first() == null) {
                $rb = new RadiusBan();
                $rb->userid = $user->id;
                $rb->save();
                Radius::Delete($user->email);
            }

            if (strtotime($user->expire_in) < time() && $user->expire_notified == false) {
                $user->transfer_enable = 0;
                $user->u = 0;
                $user->d = 0;
                $user->last_day_t = 0;
                $user->sendMail(
                    $_ENV['appName'] . '-您的用户账户已经过期了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账号已经过期了。'
                    ],
                    []
                );
                $user->expire_notified = true;
                $user->save();
            } elseif (strtotime($user->expire_in) > time() && $user->expire_notified == true) {
                $user->expire_notified = false;
                $user->save();
            }


            //余量不足检测
            if ($_ENV['notify_limit_mode'] != false) {
                $user_traffic_left = $user->transfer_enable - $user->u - $user->d;
                $under_limit = false;

                if ($user->transfer_enable != 0) {
                    if (
                        $_ENV['notify_limit_mode'] == 'per' &&
                        $user_traffic_left / $user->transfer_enable * 100 < $_ENV['notify_limit_value']
                    ) {
                        $under_limit = true;
                        $unit_text = '%';
                    }
                } elseif (
                    $_ENV['notify_limit_mode'] == 'mb' &&
                    Tools::flowToMB($user_traffic_left) < $_ENV['notify_limit_value']
                ) {
                    $under_limit = true;
                    $unit_text = 'MB';
                }

                if ($under_limit == true && $user->traffic_notified == false) {
                    $result = $user->sendMail(
                        $_ENV['appName'] . '-您的剩余流量过低',
                        'news/warn.tpl',
                        [
                            'text' => '您好，系统发现您剩余流量已经低于 ' . $_ENV['notify_limit_value'] . $unit_text . ' 。'
                        ],
                        []
                    );
                    if ($result) {
                        $user->traffic_notified = true;
                        $user->save();
                    }
                } elseif ($under_limit == false && $user->traffic_notified == true) {
                    $user->traffic_notified = false;
                    $user->save();
                }
            }

            if (
                $_ENV['account_expire_delete_days'] >= 0 &&
                strtotime($user->expire_in) + $_ENV['account_expire_delete_days'] * 86400 < time() &&
                $user->money <= $_ENV['auto_clean_min_money']
            ) {
                $user->sendMail(
                    $_ENV['appName'] . '-您的用户账户已经被删除了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账户已经过期 ' . $_ENV['account_expire_delete_days'] . ' 天了，帐号已经被删除。'
                    ],
                    []
                );
                $user->kill_user();
                continue;
            }

            if (
                $_ENV['auto_clean_uncheck_days'] > 0 &&
                max(
                    $user->last_check_in_time,
                    strtotime($user->reg_date)
                ) + ($_ENV['auto_clean_uncheck_days'] * 86400) < time() &&
                $user->class == 0 &&
                $user->money <= $_ENV['auto_clean_min_money']
            ) {
                $user->sendMail(
                    $_ENV['appName'] . '-您的用户账户已经被删除了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账号已经 ' . $_ENV['auto_clean_uncheck_days'] . ' 天没签到了，帐号已经被删除。'
                    ],
                    []
                );
                $user->kill_user();
                continue;
            }

            if (
                $_ENV['auto_clean_unused_days'] > 0 &&
                max($user->t, strtotime($user->reg_date)) + ($_ENV['auto_clean_unused_days'] * 86400) < time() &&
                $user->class == 0 &&
                $user->money <= $_ENV['auto_clean_min_money']
            ) {
                $user->sendMail(
                    $_ENV['appName'] . '-您的用户账户已经被删除了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账号已经 ' . $_ENV['auto_clean_unused_days'] . ' 天没使用了，帐号已经被删除。'
                    ],
                    []
                );
                $user->kill_user();
                continue;
            }

            if (
                $user->class != 0 &&
                strtotime($user->class_expire) < time() &&
                strtotime($user->class_expire) > 1420041600
            ) {
                $text = '您好，系统发现您的账号等级已经过期了。';
                $reset_traffic = $_ENV['class_expire_reset_traffic'];
                if ($reset_traffic >= 0) {
                    $user->transfer_enable = Tools::toGB($reset_traffic);
                    $user->u = 0;
                    $user->d = 0;
                    $user->last_day_t = 0;
                    $text .= '流量已经被重置为' . $reset_traffic . 'GB';
                }
                $user->sendMail(
                    $_ENV['appName'] . '-您的账户等级已经过期了',
                    'news/warn.tpl',
                    [
                        'text' => $text
                    ],
                    []
                );
                $user->class = 0;
            }

            // 审计封禁解封
            if ($user->enable == 0) {
                $logs = DetectBanLog::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                if ($logs != null) {
                    if (($logs->end_time + $logs->ban_time * 60) <= time()) {
                        $user->enable = 1;
                    }
                }
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

        if ($_ENV['enable_telegram'] === true) {
            self::Telegram();
        }

        if ($_ENV['enable_auto_detect_ban'] === true) {
            self::DetectBan();
        }
    }

    /**
     * Telegram 任务
     */
    public static function Telegram(): void
    {
        # 删除 tg 消息
        $TelegramTasks = TelegramTasks::where('type', 1)->where('executetime', '<', time())->get();
        foreach ($TelegramTasks as $Task) {
            \App\Utils\Telegram\TelegramTools::SendPost('deleteMessage', ['chat_id' => $Task->chatid, 'message_id' => $Task->messageid]);
            TelegramTasks::where('chatid', $Task->chatid)->where('type', '<>', 1)->where('messageid', $Task->messageid)->delete();
            $Task->delete();
        }
    }

    /**
     * 审计封禁任务
     */
    public static function DetectBan(): void
    {
        echo '审计封禁检查开始.' . PHP_EOL;
        $new_logs = DetectLog::where('status', '=', 0)->orderBy('id', 'asc')->take($_ENV['auto_detect_ban_numProcess'])->get();
        if (count($new_logs) != 0) {

            $user_logs = [];
            foreach ($new_logs as $log) {
                // 分类各个用户的记录数量
                if (!in_array($log->user_id, array_keys($user_logs))) {
                    $user_logs[$log->user_id] = 0;
                }
                $user_logs[$log->user_id]++;
                $log->status = 1;
                $log->save();
            }

            foreach ($user_logs as $userid => $value) {
                // 执行封禁
                $user = User::find($userid);
                if ($user == null) {
                    continue;
                }
                $user->all_detect_number += $value;
                $user->save();

                if ($user->enable == 0 || ($user->is_admin && $_ENV['auto_detect_ban_allow_admin'] === true) || in_array($user->id, $_ENV['auto_detect_ban_allow_users'])) {
                    // 如果用户已被封禁
                    // 如果用户是管理员
                    // 如果属于钦定用户
                    // 则跳过
                    continue;
                }

                if ($_ENV['auto_detect_ban_type'] == 1) {
                    $last_DetectBanLog      = DetectBanLog::where('user_id', $userid)->orderBy('id', 'desc')->first();
                    $last_all_detect_number = ($last_DetectBanLog == null ? 0 : (int) $last_DetectBanLog->all_detect_number);
                    $detect_number          = ($user->all_detect_number - $last_all_detect_number);
                    if ($detect_number >= $_ENV['auto_detect_ban_number']) {
                        $last_detect_ban_time               = $user->last_detect_ban_time;
                        $end_time                           = date('Y-m-d H:i:s');
                        $user->enable                       = 0;
                        $user->last_detect_ban_time         = $end_time;
                        $user->save();
                        $DetectBanLog                       = new DetectBanLog();
                        $DetectBanLog->user_name            = $user->user_name;
                        $DetectBanLog->user_id              = $user->id;
                        $DetectBanLog->email                = $user->email;
                        $DetectBanLog->detect_number        = $detect_number;
                        $DetectBanLog->ban_time             = $_ENV['auto_detect_ban_time'];
                        $DetectBanLog->start_time           = strtotime($last_detect_ban_time);
                        $DetectBanLog->end_time             = strtotime($end_time);
                        $DetectBanLog->all_detect_number    = $user->all_detect_number;
                        $DetectBanLog->save();
                    }
                } else {
                    $number = $user->all_detect_number;
                    $tmp = 0;
                    foreach ($_ENV['auto_detect_ban'] as $key => $value) {
                        if ($number >= $key) {
                            if ($key >= $tmp) {
                                $tmp = $key;
                            }
                        }
                    }
                    if ($tmp != 0) {
                        if ($_ENV['auto_detect_ban'][$tmp]['type'] == 'kill') {
                            $user->kill_user();
                        } else {
                            $last_detect_ban_time               = $user->last_detect_ban_time;
                            $end_time                           = date('Y-m-d H:i:s');
                            $user->enable                       = 0;
                            $user->last_detect_ban_time         = $end_time;
                            $user->save();
                            $DetectBanLog                       = new DetectBanLog();
                            $DetectBanLog->user_name            = $user->user_name;
                            $DetectBanLog->user_id              = $user->id;
                            $DetectBanLog->email                = $user->email;
                            $DetectBanLog->detect_number        = $number;
                            $DetectBanLog->ban_time             = $_ENV['auto_detect_ban'][$tmp]['time'];
                            $DetectBanLog->start_time           = strtotime('1989-06-04 00:05:00');
                            $DetectBanLog->end_time             = strtotime($end_time);
                            $DetectBanLog->all_detect_number    = $number;
                            $DetectBanLog->save();
                        }
                    }
                }
            }
        } else {
            echo '- 暂无新记录.' . PHP_EOL;
        }
        echo '审计封禁检查结束.' . PHP_EOL;
    }

    public static function detectGFW()
    {
        //节点被墙检测
        $last_time = file_get_contents(BASE_PATH . '/storage/last_detect_gfw_time');
        for ($count = 1; $count <= 12; $count++) {
            if (time() - $last_time >= $_ENV['detect_gfw_interval']) {
                $file_interval = fopen(BASE_PATH . '/storage/last_detect_gfw_time', 'wb');
                fwrite($file_interval, time());
                fclose($file_interval);
                $nodes = Node::all();
                $adminUser = User::where('is_admin', '=', '1')->get();
                foreach ($nodes as $node) {
                    if (
                        $node->node_ip == '' ||
                        $node->node_ip == null ||
                        $node->online == false
                    ) {
                        continue;
                    }
                    $api_url = $_ENV['detect_gfw_url'];
                    $api_url = str_replace(
                        array('{ip}', '{port}'),
                        array($node->node_ip, $_ENV['detect_gfw_port']),
                        $api_url
                    );
                    //因为考虑到有v2ray之类的节点，所以不得不使用ip作为参数
                    $result_tcping = false;
                    $detect_time = $_ENV['detect_gfw_count'];
                    for ($i = 1; $i <= $detect_time; $i++) {
                        $json_tcping = json_decode(file_get_contents($api_url), true);
                        if (eval('return ' . $_ENV['detect_gfw_judge'] . ';')) {
                            $result_tcping = true;
                            break;
                        }
                    }
                    if ($result_tcping == false) {
                        //被墙了
                        echo ($node->id . ':false' . PHP_EOL);
                        //判断有没有发送过邮件
                        if ($node->gfw_block == true) {
                            continue;
                        }
                        foreach ($adminUser as $user) {
                            echo 'Send gfw mail to user: ' . $user->id . '-';
                            $user->sendMail(
                                $_ENV['appName'] . '-系统警告',
                                'news/warn.tpl',
                                [
                                    'text' => '管理员您好，系统发现节点 ' . $node->name . ' 被墙了，请您及时处理。'
                                ],
                                []
                            );
                            $notice_text = str_replace(
                                '%node_name%',
                                $node->name,
                                Config::getconfig('Telegram.string.NodeGFW')
                            );
                        }
                        if (Config::getconfig('Telegram.bool.NodeGFW')) {
                            Telegram::Send($notice_text);
                        }
                        $node->gfw_block = true;
                        $node->save();
                    } else {
                        //没有被墙
                        echo ($node->id . ':true' . PHP_EOL);
                        if ($node->gfw_block == false) {
                            continue;
                        }
                        foreach ($adminUser as $user) {
                            echo 'Send gfw mail to user: ' . $user->id . '-';
                            $user->sendMail(
                                $_ENV['appName'] . '-系统提示',
                                'news/warn.tpl',
                                [
                                    'text' => '管理员您好，系统发现节点 ' . $node->name . ' 溜出墙了。'
                                ],
                                []
                            );
                            $notice_text = str_replace(
                                '%node_name%',
                                $node->name,
                                Config::getconfig('Telegram.string.NodeGFW_recover')
                            );
                        }
                        if (Config::getconfig('Telegram.bool.NodeGFW_recover')) {
                            Telegram::Send($notice_text);
                        }
                        $node->gfw_block = false;
                        $node->save();
                    }
                }
                break;
            }

            echo ($node->id . 'interval skip' . PHP_EOL);
            sleep(3);
        }
    }
}
