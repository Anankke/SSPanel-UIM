<?php
namespace App\Command;

use App\Models\DetectLog;
use App\Models\EmailQueue;
use App\Models\EmailVerify;
use App\Models\Ip;
use App\Models\Node;
use App\Models\NodeInfoLog;
use App\Models\NodeOnlineLog;
use App\Models\PasswordReset;
use App\Models\Statistics as StatisticsModel;
use App\Models\StreamMedia;
use App\Models\TelegramSession;
use App\Models\Token;
use App\Models\User;
use App\Models\UserSubscribeLog;
use App\Services\Mail;
use App\Utils\DatatablesHelper;
use App\Utils\Tools;
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

    public function DailyJob()
    {
        ini_set('memory_limit', '-1');

        // 清理各表记录
        $limit = date('Y-m-d H:i:s', time() - 86400 * (int) $_ENV['subscribeLog_keep_days']);
        Ip::where('datetime', '<', time() - 300)->delete();
        Token::where('expire_time', '<', time())->delete();
        DetectLog::where('datetime', '<', time() - 86400 * 3)->delete();
        NodeInfoLog::where('log_time', '<', time() - 86400 * 3)->delete();
        StreamMedia::where('created_at', '<', time() - 86400 * 24)->delete();
        EmailVerify::where('expire_in', '<', time() - 86400 * 3)->delete();
        PasswordReset::where('expire_time', '<', time() - 86400 * 3)->delete();
        TelegramSession::where('datetime', '<', time() - 900)->delete();
        NodeOnlineLog::where('log_time', '<', time() - 86400 * 3)->delete();
        UserSubscribeLog::where('request_time', '<', $limit)->delete();

        // 重置自增ID
        $db = new DatatablesHelper();
        Tools::reset_auto_increment($db, 'node_online_log');
        Tools::reset_auto_increment($db, 'node_info');

        // 记录流量用量
        $lastday_total = 0;
        $users = User::where('enable', '1')->get(['id', 'u', 'd', 'last_day_t']);
        foreach ($users as $user) {
            // 累加总用量
            $lastday_total += (($user->u + $user->d) - $user->last_day_t);
            // 记录单用量
            $traffic = new StatisticsModel;
            $traffic->item = 'user_traffic';
            $usage = (($user->u + $user->d) - $user->last_day_t) / 1048576; // to mb
            $traffic->value = round($usage, 2);
            $traffic->user_id = $user->id;
            $traffic->created_at = time();
            $traffic->save();
            // 重置统计字段
            $user->last_day_t = ($user->u + $user->d);
            $user->save();
        }

        $traffic = new StatisticsModel;
        $traffic->item = 'traffic';
        $traffic->value = round($lastday_total / 1073741824, 2); // to gb
        $traffic->created_at = time();
        $traffic->save();

        // 记录节点流量用量
        $nodes = Node::all();
        $timestamp_limit = strtotime(date('Y-m-d') . '00:03:00');
        foreach ($nodes as $node) {
            $before_usage = StatisticsModel::where('node_id', $node->id)
                ->where('item', 'node_traffic_log')
                ->orderBy('id', 'desc')
                ->first();

            $before_usage_v = (empty($before_usage)) ? '0' : $before_usage->value;

            if ($timestamp_limit > time()) {
                $traffic = new StatisticsModel;
                $traffic->item = 'node_traffic_log';
                $traffic->value = $node->node_bandwidth;
                $traffic->node_id = $node->id;
                $traffic->created_at = time();
                $traffic->save();
            }

            if ($before_usage_v != '0') {
                $usage = new StatisticsModel;
                $usage->item = 'node_traffic';
                // today_usage 记录的实际上是昨天的，因为今天才开始统计
                $today_usage = round(($node->node_bandwidth - $before_usage_v) / 1048576, 2); // to mb
                if ($today_usage > 0) {
                    $usage->value = $today_usage;
                } else {
                    // 如果昨天是重置日
                    if ($node->bandwidthlimit_resetday == (date('j') - 1)) {
                        $usage->value = round($node->node_bandwidth / 1048576, 2); // to mb
                    } else {
                        // 如果昨天不是重置日，但today_usage是负数，那就是在后台将节点流量用量改小了
                        $usage->value = 0;
                    }
                }
                $usage->node_id = $node->id;
                $usage->created_at = time();
                $usage->save();
            }
        }

        // 重置节点流量
        Node::where('bandwidthlimit_resetday', date('d'))->update(['node_bandwidth' => 0]);

        // 更新 IP 库
        if (date('d') == '1' || date('d') == '10' || date('d') == '20') {
            (new Tool($this->argv))->initQQWry();
        }

        echo 'All Done.' . PHP_EOL;
    }

    public function CheckJob()
    {
        //节点掉线检测
        if ($_ENV['enable_detect_offline'] == true) {
            echo '节点掉线检测开始' . PHP_EOL;
            $adminUser = User::where('is_admin', '=', '1')->get();
            $nodes = Node::all();
            foreach ($nodes as $node) {
                if ($node->isNodeOnline() === false && $node->online == true) {
                    foreach ($adminUser as $user) {
                        echo 'Send offline mail to user: ' . $user->id . PHP_EOL;
                        $user->sendMail($_ENV['appName'] . ' - 系统警告', 'news/warn.tpl',
                            [
                                'text' => '管理员您好，系统发现节点 ' . $node->name . ' 掉线了，请您及时处理。',
                            ], [], $_ENV['email_queue']
                        );
                    }

                    $node->online = false;
                    $node->save();
                } elseif ($node->isNodeOnline() === true && $node->online == false) {
                    foreach ($adminUser as $user) {
                        echo 'Send offline mail to user: ' . $user->id . PHP_EOL;
                        $user->sendMail($_ENV['appName'] . ' - 系统提示', 'news/warn.tpl',
                            [
                                'text' => '管理员您好，系统发现节点 ' . $node->name . ' 恢复上线了。',
                            ], [], $_ENV['email_queue']
                        );
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
            $server = $node->get_out_address();
            if (!Tools::is_ip($server) && $node->changeNodeIp($server)) {
                $node->save();
            }
        }
    }

    public function UserJob()
    {
        $users = User::all();
        foreach ($users as $user) {
            // 账户过期检测
            if (strtotime($user->expire_in) < time() && $user->expire_notified == false) {
                if ($_ENV['clear_traffic_after_expire']) {
                    $user->transfer_enable = 0;
                    $user->u = 0;
                    $user->d = 0;
                    $user->last_day_t = 0;
                }
                $user->sendMail($_ENV['appName'] . ' - 您的用户账户已经过期了', 'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账号已经过期了。',
                    ], [], $_ENV['email_queue']
                );
                $user->expire_notified = true;
                $user->save();
            } elseif (strtotime($user->expire_in) > time() && $user->expire_notified == true) {
                $user->expire_notified = false;
                $user->save();
            }
        }
    }
}
