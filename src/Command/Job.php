<?php

namespace App\Command;

use App\Models\Ip;
use App\Models\Node;
use App\Models\User;
use App\Models\Shop;
use App\Models\Token;
use App\Models\Bought;
use App\Models\BlockIp;
use App\Models\DetectLog;
use App\Models\UnblockIp;
use App\Models\EmailVerify;
use App\Models\DetectBanLog;
use App\Models\EmailQueue;
use App\Models\NodeInfoLog;
use App\Models\NodeOnlineLog;
use App\Models\TelegramSession;
use App\Models\UserSubscribeLog;
use App\Services\Config;
use App\Services\Mail;
use App\Utils\Tools;
use App\Utils\Telegram;
use App\Utils\DatatablesHelper;
use Exception;

class Job extends Command
{
    public $description = ''
        . '├─=: php xcat Job [选项]' . PHP_EOL
        . '│ ├─ SendMail                - 处理邮件队列' . PHP_EOL
        . '│ ├─ DailyJob                - 每日任务' . PHP_EOL
        . '│ ├─ CheckJob                - 检查任务，每分钟' . PHP_EOL
        . '│ ├─ UserJob                 - 用户账户相关任务，每小时' . PHP_EOL;

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
    public function SendMail()
    {
        if (file_exists(BASE_PATH . '/storage/email_queue')) {
            echo "程序正在运行中" . PHP_EOL;
            return false;
        }
        $myfile = fopen(BASE_PATH . '/storage/email_queue', 'wb+') or die('Unable to open file!');
        $txt = '1';
        fwrite($myfile, $txt);
        fclose($myfile);
        // 分块处理，节省内存
        EmailQueue::chunkById(1000, function ($email_queues) {
            foreach ($email_queues as $email_queue) {
                try {
                    Mail::send($email_queue->to_email, $email_queue->subject, $email_queue->template, json_decode($email_queue->array), []);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                echo '发送邮件至 ' . $email_queue->to_email . PHP_EOL;
                $email_queue->delete();
            }
        });
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

        // ------- 重置节点流量，排除无需重置流量的节点类型
        Node::where('bandwidthlimit_resetday', date('d'))->update(['node_bandwidth' => 0]);
        // ------- 重置节点流量

        // ------- 清理各表记录
        UserSubscribeLog::where('request_time', '<', date('Y-m-d H:i:s', time() - 86400 * (int)$_ENV['subscribeLog_keep_days']))->delete();
        Token::where('expire_time', '<', time())->delete();
        NodeInfoLog::where('log_time', '<', time() - 86400 * 3)->delete();
        NodeOnlineLog::where('log_time', '<', time() - 86400 * 3)->delete();
        DetectLog::where('datetime', '<', time() - 86400 * 3)->delete();
        EmailVerify::where('expire_in', '<', time() - 86400 * 3)->delete();
        Ip::where('datetime', '<', time() - 300)->delete();
        UnblockIp::where('datetime', '<', time() - 300)->delete();
        BlockIp::where('datetime', '<', time() - 86400)->delete();
        TelegramSession::where('datetime', '<', time() - 900)->delete();
        // ------- 清理各表记录

        // ------- 清理 TG 二维码登录的图片
        system('rm ' . BASE_PATH . '/storage/*.png', $ret);
        // ------- 清理 TG 二维码登录的图片

        // ------- 重置自增 ID
        $db = new DatatablesHelper();
        Tools::reset_auto_increment($db, 'ss_node_online_log');
        Tools::reset_auto_increment($db, 'ss_node_info');
        // ------- 重置自增 ID

        // ------- 用户流量重置
        // 取消已下架的商品不支持重置的限制，因为目前没有库存限制
        $shopid  = Shop::where('content->reset', '<>', 0)->where('content->reset_value', '<>', 0)->where('content->reset_exp', '<>', 0)->pluck('id')->toArray();
        // 用 UserID 分组倒序取最新一条包含周期重置商品的购买记录
        $boughts = Bought::whereIn('shopid', $shopid)->orderBy('id', 'desc')->groupBy('userid')->get();
        $bought_users = array();
        foreach ($boughts as $bought) {
            /** @var Bought $bought */
            $user = $bought->user();
            if ($user == null) {
                continue;
            }
            $shop = $bought->shop();
            if ($shop == null) {
                $bought->delete();
                continue;
            }
            $bought_users[] = $bought->userid;
            if ($bought->valid() && $bought->used_days() % $shop->reset() == 0 && $bought->used_days() != 0) {
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
                    [],
                    $_ENV['email_queue']
                );
            }
        }
        // ------- 用户流量重置

        User::chunkById(1000, function ($users) use ($bought_users) {
            foreach ($users as $user) {
                /** @var User $user */
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
                        [],
                        $_ENV['email_queue']
                    );
                }
            }
        });

        // ------- 更新 IP 库
        (new Tool($this->argv))->initQQWry();
        // ------- 更新 IP 库

        // ------- 发送每日系统运行报告
        if (Config::getconfig('Telegram.bool.DailyJob')) {
            Telegram::Send(Config::getconfig('Telegram.string.DailyJob'));
        }
        // ------- 发送每日系统运行报告
    }

    /**
     * 检查任务，每分钟
     *
     * @return void
     */
    public function CheckJob()
    {
        //节点掉线检测
        if ($_ENV['enable_detect_offline'] == true) {
            echo '节点掉线检测开始' . PHP_EOL;
            $adminUser = User::where('is_admin', '=', '1')->get();
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
                        file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                    }

                    foreach ($adminUser as $user) {
                        echo 'Send offline mail to user: ' . $user->id . PHP_EOL;
                        $user->sendMail(
                            $_ENV['appName'] . '-系统警告',
                            'news/warn.tpl',
                            [
                                'text' => '管理员您好，系统发现节点 ' . $node->name . ' 掉线了，请您及时处理。'
                            ],
                            [],
                            $_ENV['email_queue']
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
                        file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                    }
                    foreach ($adminUser as $user) {
                        echo 'Send offline mail to user: ' . $user->id . PHP_EOL;
                        $user->sendMail(
                            $_ENV['appName'] . '-系统提示',
                            'news/warn.tpl',
                            [
                                'text' => '管理员您好，系统发现节点 ' . $node->name . ' 恢复上线了。'
                            ],
                            [],
                            $_ENV['email_queue']
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

        //更新节点 IP，每分钟
        $nodes = Node::get();
        foreach ($nodes as $node) {
            /** @var Node $node */
            $server = $node->get_out_address();
            if (!Tools::is_ip($server) && $node->changeNodeIp($server)) {
                $node->save();
            }
        }
    }

    /**
     * 用户账户相关任务，每小时
     *
     * @return void
     */
    public function UserJob()
    {
        $users = User::all();
        foreach ($users as $user) {
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
                    [],
                    $_ENV['email_queue']
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

                if ($user->transfer_enable != 0 && $user->class != 0) {
                    if (
                        $_ENV['notify_limit_mode'] == 'per' &&
                        $user_traffic_left / $user->transfer_enable * 100 < $_ENV['notify_limit_value']
                    ) {
                        $under_limit = true;
                        $unit_text = '%';
                    } elseif (
                        $_ENV['notify_limit_mode'] == 'mb' &&
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
                        [],
                        $_ENV['email_queue']
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
                    [],
                    $_ENV['email_queue']
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
                    [],
                    $_ENV['email_queue']
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
                    [],
                    $_ENV['email_queue']
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
                    [],
                    $_ENV['email_queue']
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

        //自动续费
        $boughts = Bought::where('renew', '<', time() + 60)->where('renew', '<>', 0)->get();
        foreach ($boughts as $bought) {
            /** @var Bought $bought */
            $user = $bought->user();
            if ($user == null) {
                continue;
            }

            $shop = $bought->shop();
            if ($shop == null) {
                $bought->delete();
                $user->sendMail(
                    $_ENV['appName'] . '-续费失败',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统为您自动续费商品时，发现该商品已被下架，为能继续正常使用，建议您登录用户面板购买新的商品。'
                    ],
                    [],
                    $_ENV['email_queue']
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

                $bought_new           = new Bought();
                $bought_new->userid   = $user->id;
                $bought_new->shopid   = $shop->id;
                $bought_new->datetime = time();
                $bought_new->renew    = time() + $shop->auto_renew * 86400;
                $bought_new->price    = $shop->price;
                $bought_new->coupon   = '';
                $bought_new->save();

                $user->sendMail(
                    $_ENV['appName'] . '-续费成功',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统已经为您自动续费，商品名：' . $shop->name . ',金额:' . $shop->price . ' 元。'
                    ],
                    [],
                    $_ENV['email_queue']
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
                    [],
                    $_ENV['email_queue']
                );
                $bought->is_notified = true;
                $bought->save();
            }
        }
    }
}
