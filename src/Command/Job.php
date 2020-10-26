<?php

namespace App\Command;

use App\Models\Ip;
use App\Models\Node;
use App\Models\User;
use App\Models\Shop;
use App\Models\Token;
use App\Models\Bought;
use App\Models\BlockIp;
use App\Models\LoginIp;
use App\Models\DetectLog;
use App\Models\UnblockIp;
use App\Models\Speedtest;
use App\Models\RadiusBan;
use App\Models\TrafficLog;
use App\Models\Disconnect;
use App\Models\EmailVerify;
use App\Models\DetectBanLog;
use App\Models\NodeInfoLog;
use App\Models\NodeOnlineLog;
use App\Models\TelegramTasks;
use App\Models\TelegramSession;
use App\Models\UserSubscribeLog;
use App\Services\Config;
use App\Utils\GA;
use App\Utils\QQWry;
use App\Utils\Telegram\TelegramTools;
use App\Utils\Tools;
use App\Utils\Radius;
use App\Utils\Telegram;
use App\Utils\DatatablesHelper;
use ArrayObject;
use Ramsey\Uuid\Uuid;

class Job extends Command
{
    public $description = ''
    . '├─=: php xcat Job [选项]' . PHP_EOL
    . '│ ├─ UserGa                  - 二次验证' . PHP_EOL
    . '│ ├─ DailyJob                - 每日任务' . PHP_EOL
    . '│ ├─ CheckJob                - 检查任务，每分钟' . PHP_EOL
    . '│ ├─ updatedownload          - 检查客户端更新' . PHP_EOL;

    public function boot()
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                echo '方法不存在.' . PHP_EOL;
            }
        }
    }
    /**
     * 发邮件
     *
     * @return void
     */
    public function SendMail(){
        if(file_exists(BASE_PATH . '/storage/email_queue')){
            echo "程序正在运行中".PHP_EOL;
            return false;
        }
        $myfile = fopen(BASE_PATH . '/storage/email_queue', 'wb+') or die('Unable to open file!');
                $txt = '1';
                fwrite($myfile, $txt);
                fclose($myfile);
        $email_queues = EmailQueue::all();
        foreach($email_queues as $email_queue){
            try {
                    Mail::send($email_queue->to_email, $email_queue->subject, $email_queue->template, json_decode($email_queue->array), [
                    ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            echo '发送邮件至 '.$email_queue->to_email.PHP_EOL;
            $email_queue->delete();
        }
        unlink(BASE_PATH . '/storage/email_queue');
    }
    /**
     * 每日任务
     *
     * @return void
     */
    public function DailyJob()
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
        UserSubscribeLog::where(
            'request_time',
            '<',
            date('Y-m-d H:i:s', time() - 86400 * (int)$_ENV['subscribeLog_keep_days'])
        )->delete();

        Token::where('expire_time', '<', time())->delete();
        NodeInfoLog::where('log_time', '<', time() - 86400 * 3)->delete();
        NodeOnlineLog::where('log_time', '<', time() - 86400 * 3)->delete();
        TrafficLog::where('log_time', '<', time() - 86400 * 3)->delete();
        DetectLog::where('datetime', '<', time() - 86400 * 3)->delete();
        Speedtest::where('datetime', '<', time() - 86400 * 3)->delete();
        EmailVerify::where('expire_in', '<', time() - 86400 * 3)->delete();
        system('rm ' . BASE_PATH . '/storage/*.png', $ret);
        
        $db = new DatatablesHelper();
        
        Tools::reset_auto_increment($db, 'user_traffic_log');
        Tools::reset_auto_increment($db, 'ss_node_online_log');
        Tools::reset_auto_increment($db, 'ss_node_info');

        if (Config::getconfig('Telegram.bool.DailyJob')) {
            Telegram::Send(Config::getconfig('Telegram.string.DailyJob'));
        }

        //auto reset
        $boughts = Bought::all();
        $bought_users = array();
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
                $bought_users[] = $bought->userid;
                if ((time() - $shop->reset_exp() * 86400 < $bought->datetime) && (int)((time(
                            ) - $bought->datetime) / 86400) % $shop->reset() == 0 && (int)((time(
                            ) - $bought->datetime) / 86400) != 0) {
                    echo('流量重置-' . $user->id . "\n");
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
                        [],$_ENV['email_queue']
                    );
                }
            }
        }


        $users = User::all();
        foreach ($users as $user) {
            $user->last_day_t = ($user->u + $user->d);
            $user->save();
            if (in_array($user->id, $bought_users)) {
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
                    [],$_ENV['email_queue']
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
        if (iconv('gbk', 'utf-8//IGNORE', $Userlocation) !== '美国') {
            unlink(BASE_PATH . '/storage/qqwry.dat');
            rename(BASE_PATH . '/storage/qqwry.dat.bak', BASE_PATH . '/storage/qqwry.dat');
        }

        $this->updatedownload();
    }

    /**
     * 检查任务，每分钟
     *
     * @return void
     */
    public function CheckJob()
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
                    [],$_ENV['email_queue']
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
                    [],$_ENV['email_queue']
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
                    [],$_ENV['email_queue']
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
                            [],$_ENV['email_queue']
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
                            [],$_ENV['email_queue']
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
                                    [],$_ENV['email_queue']
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
                $user->id . '|' . $user->passwd
            );
            $user->save();
            if (($user->transfer_enable <= $user->u + $user->d || $user->enable == 0 || (strtotime(
                            $user->expire_in
                        ) < time() && strtotime($user->expire_in) > 644447105)) && RadiusBan::where(
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
                    [],$_ENV['email_queue']
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

                if ($user->transfer_enable != 0 && $user->class !=0) {
                    if ($_ENV['notify_limit_mode'] == 'per' &&
                        $user_traffic_left / $user->transfer_enable * 100 < $_ENV['notify_limit_value']
                    ) {
                        $under_limit = true;
                        $unit_text = '%';
                    }
                    elseif ($_ENV['notify_limit_mode'] == 'mb' &&
                        Tools::flowToMB($user_traffic_left) < $_ENV['notify_limit_value']
                    ) {
                        $under_limit = true;
                        $unit_text = 'MB';
                    }
                }

                if ($under_limit == true && $user->traffic_notified == false) {
                    $result = $user->sendMail(
                        $_ENV['appName'] . '-您的剩余流量过低',
                        'news/warn.tpl',
                        [
                            'text' => '您好，系统发现您剩余流量已经低于 ' . $_ENV['notify_limit_value'] . $unit_text . ' 。'
                        ],
                        [],$_ENV['email_queue']
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

            if ($_ENV['account_expire_delete_days'] >= 0 &&
                strtotime($user->expire_in) + $_ENV['account_expire_delete_days'] * 86400 < time() &&
                $user->money <= $_ENV['auto_clean_min_money']
            ) {
                $user->sendMail(
                    $_ENV['appName'] . '-您的用户账户已经被删除了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账户已经过期 ' . $_ENV['account_expire_delete_days'] . ' 天了，帐号已经被删除。'
                    ],
                    [],$_ENV['email_queue']
                );
                $user->kill_user();
                continue;
            }

            if ($_ENV['auto_clean_uncheck_days'] > 0 &&
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
                    [],$_ENV['email_queue']
                );
                $user->kill_user();
                continue;
            }

            if ($_ENV['auto_clean_unused_days'] > 0 &&
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
                    [],$_ENV['email_queue']
                );
                $user->kill_user();
                continue;
            }

            if ($user->class != 0 &&
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
                    [],$_ENV['email_queue']
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

            if ($user->enable == 1 && (strtotime($user->expire_in) > time() || strtotime(
                        $user->expire_in
                    ) < 644447105) && $user->transfer_enable > $user->u + $user->d) {
                $sinuser->delete();
                Radius::Add($user, $user->passwd);
            }
        }

        if ($_ENV['enable_telegram'] === true) {
            $this->Telegram();
        }

        //更新节点 IP，每分钟
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
        $datatables->query(
            'DELETE FROM `relay` WHERE `source_node_id` NOT IN(' . $allNodeID . ') OR `dist_node_id` NOT IN(' . $allNodeID . ')'
        );
    }

    /**
     * Telegram 任务
     */
    public function Telegram(): void
    {
        # 删除 tg 消息
        $TelegramTasks = TelegramTasks::where('type', 1)->where('executetime', '<', time())->get();
        foreach ($TelegramTasks as $Task) {
            TelegramTools::SendPost(
                'deleteMessage',
                ['chat_id' => $Task->chatid, 'message_id' => $Task->messageid]
            );
            TelegramTasks::where('chatid', $Task->chatid)->where('type', '<>', 1)->where(
                'messageid',
                $Task->messageid
            )->delete();
            $Task->delete();
        }
    }

    /**
     * 定时任务开启的情况下，每天自动检测有没有最新版的后端，github源来自Miku
     *
     * @return void
     */
    public function updatedownload()
    {
        system(
            'cd ' . BASE_PATH . '/public/ssr-download/ && git pull https://github.com/xcxnig/ssr-download.git --rebase && git gc'
        );
    }

    /**
     * 二次验证
     *
     * @return void
     */
    public function UserGa()
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
}
