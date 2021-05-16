<?php

namespace App\Models;

use App\Controllers\LinkController;
use App\Utils\{
    Tools,
    Hash,
    GA,
    Telegram,
    URL
};
use App\Services\{Config, Mail};
use Ramsey\Uuid\Uuid;
use Exception;

/**
 * User Model
 *
 * @property-read   int     $id         ID
 * @todo More property
 * @property        bool    $is_admin           是否管理员
 * @property        bool    $expire_notified    If user is notified for expire
 * @property        bool    $traffic_notified   If user is noticed for low traffic
 */
class User extends Model
{
    protected $connection = 'default';

    protected $table = 'user';

    /**
     * 已登录
     *
     * @var bool
     */
    public $isLogin;

    /**
     * 强制类型转换
     *
     * @var array
     */
    protected $casts = [
        't'               => 'float',
        'u'               => 'float',
        'd'               => 'float',
        'port'            => 'int',
        'transfer_enable' => 'float',
        'enable'          => 'int',
        'is_admin'        => 'boolean',
        'is_multi_user'   => 'int',
        'node_speedlimit' => 'float',
        'sendDailyMail'   => 'int',
        'ref_by'          => 'int'
    ];

    /**
     * Gravatar 头像地址
     */
    public function getGravatarAttribute(): string
    {
        $hash = md5(strtolower(trim($this->email)));
        return 'https://cdn.v2ex.com/gravatar/' . $hash . '?&d=identicon';
    }

    /**
     * 联系方式类型
     */
    public function im_type(): string
    {
        switch ($this->im_type) {
            case 1:
                return '微信';
            case 2:
                return 'QQ';
            case 3:
                return 'Google+';
            default:
                return 'Telegram';
        }
    }

    /**
     * 联系方式
     */
    public function im_value(): string
    {
        switch ($this->im_type) {
            case 1:
            case 2:
            case 3:
                return $this->im_value;
            default:
                return '<a href="https://telegram.me/' . $this->im_value . '">' . $this->im_value . '</a>';
        }
    }

    public function getMuMd5()
    {
        $str = str_replace(
            array('%id', '%suffix'),
            array($this->id, $_ENV['mu_suffix']),
            $_ENV['mu_regex']
        );
        preg_match_all("|%-?[1-9]\d*m|U", $str, $matches, PREG_PATTERN_ORDER);
        foreach ($matches[0] as $key) {
            $key_match = str_replace(array('%', 'm'), '', $key);
            $md5 = substr(
                MD5($this->id . $this->passwd . $this->method . $this->obfs . $this->protocol),
                ($key_match < 0 ? $key_match : 0),
                abs($key_match)
            );
            $str = str_replace($key, $md5, $str);
        }
        return $str;
    }

    /**
     * 最后使用时间
     */
    public function lastSsTime(): string
    {
        return $this->t == 0 ? '从未使用喵' : Tools::toDateTime($this->t);
    }

    /**
     * 最后签到时间
     */
    public function lastCheckInTime(): string
    {
        return $this->last_check_in_time == 0 ? '从未签到' : Tools::toDateTime($this->last_check_in_time);
    }

    /**
     * 更新密码
     *
     * @param string $pwd
     */
    public function updatePassword(string $pwd): bool
    {
        $this->pass = Hash::passwordHash($pwd);
        return $this->save();
    }

    public function get_forbidden_ip()
    {
        return str_replace(',', PHP_EOL, $this->forbidden_ip);
    }

    public function get_forbidden_port()
    {
        return str_replace(',', PHP_EOL, $this->forbidden_port);
    }

    /**
     * 更新连接密码
     *
     * @param string $pwd
     */
    public function updateSsPwd(string $pwd): bool
    {
        $this->passwd = $pwd;
        return $this->save();
    }

    /**
     * 更新加密方式
     *
     * @param string $method
     */
    public function updateMethod(string $method): array
    {
        $return = [
            'ok' => false
        ];
        if ($method == '') {
            $return['msg'] = '非法输入';
            return $return;
        }
        if (!Tools::is_param_validate('method', $method)) {
            $return['msg'] = '加密无效';
            return $return;
        }
        $this->method = $method;
        if (!Tools::checkNoneProtocol($this)) {
            $return['msg'] = '系统检测到您将要设置的加密方式为 none ，但您的协议并不在以下协议【' . implode(',', Config::getSupportParam('allow_none_protocol')) . '】之内，请您先修改您的协议，再来修改此处设置。';
            return $return;
        }
        if (!URL::SSCanConnect($this) && !URL::SSRCanConnect($this)) {
            $return['msg'] = '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。';
            return $return;
        }
        $this->save();
        $return['ok'] = true;
        if (!URL::SSCanConnect($this)) {
            $return['msg']  = '设置成功，但您目前的协议，混淆，加密方式设置会导致 Shadowsocks 原版客户端无法连接，请您自行更换到 ShadowsocksR 客户端。';
        }
        if (!URL::SSRCanConnect($this)) {
            $return['msg']  = '设置成功，但您目前的协议，混淆，加密方式设置会导致 ShadowsocksR 客户端无法连接，请您自行更换到 Shadowsocks 客户端。';
        }
        $return['msg'] = '设置成功，您可自由选用两种客户端来进行连接。';
        return $return;
    }

    /**
     * 生成邀请码
     */
    public function addInviteCode(): string
    {
        while (true) {
            $temp_code = Tools::genRandomChar(4);
            if (InviteCode::where('code', $temp_code)->first() == null) {
                if (InviteCode::where('user_id', $this->id)->count() == 0) {
                    $code          = new InviteCode();
                    $code->code    = $temp_code;
                    $code->user_id = $this->id;
                    $code->save();
                    return $temp_code;
                } else {
                    return (InviteCode::where('user_id', $this->id)->first())->code;
                }
            }
        }
    }

    /**
     * 添加邀请次数
     */
    public function addInviteNum(int $num): bool
    {
        $this->invite_num += $num;
        return $this->save();
    }

    /**
     * 生成新的UUID
     */
    public function generateUUID($s): bool
    {
        $this->uuid = Uuid::uuid3(
            Uuid::NAMESPACE_DNS,
            $this->email . '|' . $s
        );
        return $this->save();
    }

    /*
     * 总流量[自动单位]
     */
    public function enableTraffic(): string
    {
        return Tools::flowAutoShow($this->transfer_enable);
    }

    /*
     * 总流量[GB]，不含单位
     */
    public function enableTrafficInGB(): float
    {
        return Tools::flowToGB($this->transfer_enable);
    }

    /*
     * 已用流量[自动单位]
     */
    public function usedTraffic(): string
    {
        return Tools::flowAutoShow($this->u + $this->d);
    }

    /*
     * 已用流量占总流量的百分比
     */
    public function trafficUsagePercent(): int
    {
        if ($this->transfer_enable == 0) {
            return 0;
        }
        $percent  = ($this->u + $this->d) / $this->transfer_enable;
        $percent  = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 剩余流量[自动单位]
     */
    public function unusedTraffic(): string
    {
        return Tools::flowAutoShow($this->transfer_enable - ($this->u + $this->d));
    }

    /*
     * 剩余流量占总流量的百分比
     */
    public function unusedTrafficPercent(): int
    {
        if ($this->transfer_enable == 0) {
            return 0;
        }
        $unused   = $this->transfer_enable - ($this->u + $this->d);
        $percent  = $unused / $this->transfer_enable;
        $percent  = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 今天使用的流量[自动单位]
     */
    public function TodayusedTraffic(): string
    {
        return Tools::flowAutoShow($this->u + $this->d - $this->last_day_t);
    }

    /*
     * 今天使用的流量占总流量的百分比
     */
    public function TodayusedTrafficPercent(): int
    {
        if ($this->transfer_enable == 0) {
            return 0;
        }
        $Todayused = $this->u + $this->d - $this->last_day_t;
        $percent   = $Todayused / $this->transfer_enable;
        $percent   = round($percent, 2);
        $percent  *= 100;
        return $percent;
    }

    /*
     * 今天之前已使用的流量[自动单位]
     */
    public function LastusedTraffic(): string
    {
        return Tools::flowAutoShow($this->last_day_t);
    }

    /*
     * 今天之前已使用的流量占总流量的百分比
     */
    public function LastusedTrafficPercent(): int
    {
        if ($this->transfer_enable == 0) {
            return 0;
        }
        $Lastused = $this->last_day_t;
        $percent  = $Lastused / $this->transfer_enable;
        $percent  = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 是否可以签到
     */
    public function isAbleToCheckin(): bool
    {
        return date('Ymd') != date('Ymd', $this->last_check_in_time);
    }

    public function getGAurl()
    {
        $ga = new GA();
        $url = $ga->getUrl(
            urlencode($_ENV['appName'] . '-' . $this->user_name . '-两步验证码'),
            $this->ga_token
        );
        return $url;
    }

    /**
     * 获取用户的邀请码
     */
    public function getInviteCodes(): ?InviteCode
    {
        return InviteCode::where('user_id', $this->id)->first();
    }

    /**
     * 用户的邀请人
     */
    public function ref_by_user(): ?User
    {
        return self::find($this->ref_by);
    }

    /**
     * 用户邀请人的用户名
     */
    public function ref_by_user_name(): string
    {
        if ($this->ref_by == 0) {
            return '系统邀请';
        } else {
            if ($this->ref_by_user() == null) {
                return '邀请人已经被删除';
            } else {
                return $this->ref_by_user()->user_name;
            }
        }
    }

    /**
     * 删除用户的订阅链接
     */
    public function clean_link()
    {
        Link::where('userid', $this->id)->delete();
    }

    /**
     * 获取用户的订阅链接
     */
    public function getSublink()
    {
        return LinkController::GenerateSSRSubCode($this->id);
    }

    /**
     * 删除用户的邀请码
     */
    public function clear_inviteCodes()
    {
        InviteCode::where('user_id', $this->id)->delete();
    }

    /**
     * 在线 IP 个数
     */
    public function online_ip_count(): int
    {
        // 根据 IP 分组去重
        $total = Ip::where('datetime', '>=', time() - 90)->where('userid', $this->id)->orderBy('userid', 'desc')->groupBy('ip')->get();
        $ip_list = [];
        foreach ($total as $single_record) {
            $ip = Tools::getRealIp($single_record->ip);
            if (Node::where('node_ip', $ip)->first() != null) {
                continue;
            }
            $ip_list[] = $ip;
        }
        return count($ip_list);
    }

    /**
     * 销户
     */
    public function kill_user(): bool
    {
        $uid   = $this->id;
        $email = $this->email;

        Bought::where('userid', '=', $uid)->delete();
        Code::where('userid', '=', $uid)->delete();
        DetectBanLog::where('user_id', '=', $uid)->delete();
        DetectLog::where('user_id', '=', $uid)->delete();
        EmailVerify::where('email', $email)->delete();
        InviteCode::where('user_id', '=', $uid)->delete();
        Ip::where('userid', '=', $uid)->delete();
        Link::where('userid', '=', $uid)->delete();
        LoginIp::where('userid', '=', $uid)->delete();
        PasswordReset::where('email', '=', $email)->delete();
        TelegramSession::where('user_id', '=', $uid)->delete();
        Token::where('user_id', '=', $uid)->delete();
        UnblockIp::where('userid', '=', $uid)->delete();
        UserSubscribeLog::where('user_id', '=', $uid)->delete();

        $this->delete();

        return true;
    }

    /**
     * 累计充值金额
     */
    public function get_top_up(): float
    {
        $number = Code::where('userid', $this->id)->sum('number');
        return is_null($number) ? 0.00 : round($number, 2);
    }

    /**
     * 获取累计收入
     *
     * @param string $req
     */
    public function calIncome(string $req): float
    {
        switch ($req) {
            case "yesterday":
                $number = Code::whereDate('usedatetime', '=', date('Y-m-d', strtotime('-1 days')))->sum('number');
                break;
            case "today":
                $number = Code::whereDate('usedatetime', '=', date('Y-m-d'))->sum('number');
                break;
            case "this month":
                $number = Code::whereYear('usedatetime', '=', date('Y'))->whereMonth('usedatetime', '=', date('m'))->sum('number');
                break;
            case "last month":
                $number = Code::whereYear('usedatetime', '=', date('Y'))->whereMonth('usedatetime', '=', date('m', strtotime('last month')))->sum('number');
                break;
            default:
                $number = Code::sum('number');
                break;
        }
        return is_null($number) ? 0.00 : round($number, 2);
    }

    /**
     * 获取付费用户总数
     */
    public function paidUserCount(): int
    {
        return self::where('class', '!=', '0')->count();
    }

    /**
     * 获取用户被封禁的理由
     */
    public function disableReason(): string
    {
        $reason_id = DetectLog::where('user_id', $this->id)->orderBy('id', 'DESC')->first();
        $reason    = DetectRule::find($reason_id->list_id);
        if (is_null($reason)) {
            return '特殊原因被禁用，了解详情请联系管理员';
        }
        return $reason->text;
    }

    /**
     * 最后一次被封禁的时间
     */
    public function last_detect_ban_time(): string
    {
        return ($this->last_detect_ban_time == '1989-06-04 00:05:00' ? '未被封禁过' : $this->last_detect_ban_time);
    }

    /**
     * 当前解封时间
     */
    public function relieve_time(): string
    {
        $logs = DetectBanLog::where('user_id', $this->id)->orderBy('id', 'desc')->first();
        if ($this->enable == 0 && $logs != null) {
            $time = ($logs->end_time + $logs->ban_time * 60);
            return date('Y-m-d H:i:s', $time);
        } else {
            return '当前未被封禁';
        }
    }

    /**
     * 累计被封禁的次数
     */
    public function detect_ban_number(): int
    {
        return DetectBanLog::where('user_id', $this->id)->count();
    }

    /**
     * 最后一次封禁的违规次数
     */
    public function user_detect_ban_number(): int
    {
        $logs = DetectBanLog::where('user_id', $this->id)->orderBy('id', 'desc')->first();
        return $logs->detect_number;
    }

    /**
     * 签到
     */
    public function checkin(): array
    {
        $return = [
            'ok'  => true,
            'msg' => ''
        ];
        if (!$this->isAbleToCheckin()) {
            $return['ok']  = false;
            $return['msg'] = '您似乎已经签到过了...';
        } else {
            $traffic = random_int((int) $_ENV['checkinMin'], (int) $_ENV['checkinMax']);
            $this->transfer_enable += Tools::toMB($traffic);
            $this->last_check_in_time = time();
            $this->save();
            $return['msg'] = '获得了 ' . $traffic . 'MB 流量.';
        }

        return $return;
    }

    /**
     * 更新协议
     *
     * @param string $Protocol
     */
    public function setProtocol($Protocol): array
    {
        $return = [
            'ok'  => true,
            'msg' => '设置成功，您可自由选用客户端来连接。'
        ];
        if ($Protocol == '') {
            $return['ok']   = false;
            $return['msg']  = '非法输入';
            return $return;
        }
        if (!Tools::is_param_validate('protocol', $Protocol)) {
            $return['ok']   = false;
            $return['msg']  = '协议无效';
            return $return;
        }
        $this->protocol = $Protocol;
        if (!Tools::checkNoneProtocol($this)) {
            $return['ok']   = false;
            $return['msg']  = '系统检测到您目前的加密方式为 none ，但您将要设置为的协议并不在以下协议【' . implode(',', Config::getSupportParam('allow_none_protocol')) . '】之内，请您先修改您的加密方式，再来修改此处设置。';
            return $return;
        }
        if (!URL::SSCanConnect($this) && !URL::SSRCanConnect($this)) {
            $return['ok']   = false;
            $return['msg']  = '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。';
            return $return;
        }
        $this->save();
        if (!URL::SSCanConnect($this)) {
            $return['ok']   = true;
            $return['msg']  = '设置成功，但您目前的协议，混淆，加密方式设置会导致 Shadowsocks 原版客户端无法连接，请您自行更换到 ShadowsocksR 客户端。';
        }
        if (!URL::SSRCanConnect($this)) {
            $return['ok']   = true;
            $return['msg']  = '设置成功，但您目前的协议，混淆，加密方式设置会导致 ShadowsocksR 客户端无法连接，请您自行更换到 Shadowsocks 客户端。';
        }
        return $return;
    }

    /**
     * 更新混淆
     *
     * @param string $Obfs
     */
    public function setObfs($Obfs): array
    {
        $return = [
            'ok'  => true,
            'msg' => '设置成功，您可自由选用客户端来连接。'
        ];
        if ($Obfs == '') {
            $return['ok']   = false;
            $return['msg']  = '非法输入';
            return $return;
        }
        if (!Tools::is_param_validate('obfs', $Obfs)) {
            $return['ok']   = false;
            $return['msg']  = '混淆无效';
            return $return;
        }
        $this->obfs = $Obfs;
        if (!URL::SSCanConnect($this) && !URL::SSRCanConnect($this)) {
            $return['ok']   = false;
            $return['msg']  = '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。';
            return $return;
        }
        $this->save();
        if (!URL::SSCanConnect($this)) {
            $return['ok']   = true;
            $return['msg']  = '设置成功，但您目前的协议，混淆，加密方式设置会导致 Shadowsocks 原版客户端无法连接，请您自行更换到 ShadowsocksR 客户端。';
        }
        if (!URL::SSRCanConnect($this)) {
            $return['ok']   = true;
            $return['msg']  = '设置成功，但您目前的协议，混淆，加密方式设置会导致 ShadowsocksR 客户端无法连接，请您自行更换到 Shadowsocks 客户端。';
        }
        return $return;
    }

    /**
     * 解绑 Telegram
     */
    public function TelegramReset(): array
    {
        $return = [
            'ok'  => true,
            'msg' => '解绑成功.'
        ];
        $telegram_id = $this->telegram_id;
        $this->telegram_id = 0;
        if ($this->save()) {
            if (
                $_ENV['enable_telegram'] === true
                &&
                Config::getconfig('Telegram.bool.group_bound_user') === true
                &&
                Config::getconfig('Telegram.bool.unbind_kick_member') === true
                &&
                !$this->is_admin
            ) {
                \App\Utils\Telegram\TelegramTools::SendPost(
                    'kickChatMember',
                    [
                        'chat_id'   => $_ENV['telegram_chatid'],
                        'user_id'   => $telegram_id,
                    ]
                );
            }
        } else {
            $return = [
                'ok'  => false,
                'msg' => '解绑失败.'
            ];
        }

        return $return;
    }

    /**
     * 更新端口
     *
     * @param int $Port
     */
    public function setPort($Port): array
    {
        $PortOccupied = User::pluck('port')->toArray();
        if (in_array($Port, $PortOccupied) == true) {
            return [
                'ok'  => false,
                'msg' => '端口已被占用'
            ];
        }
        $this->port = $Port;
        $this->save();
        return [
            'ok'  => true,
            'msg' => $this->port
        ];
    }

    /**
     * 重置端口
     */
    public function ResetPort(): array
    {
        $price = $_ENV['port_price'];
        if ($this->money < $price) {
            return [
                'ok'  => false,
                'msg' => '余额不足'
            ];
        }
        $this->money -= $price;
        $Port = Tools::getAvPort();
        $this->setPort($Port);
        $this->save();
        return [
            'ok'  => true,
            'msg' => $this->port
        ];
    }

    /**
     * 指定端口
     *
     * @param int $Port
     */
    public function SpecifyPort($Port): array
    {
        $price = $_ENV['port_price_specify'];
        if ($this->money < $price) {
            return [
                'ok'  => false,
                'msg' => '余额不足'
            ];
        }
        if ($Port < $_ENV['min_port'] || $Port > $_ENV['max_port'] || Tools::isInt($Port) == false) {
            return [
                'ok'  => false,
                'msg' => '端口不在要求范围内'
            ];
        }
        $PortOccupied = User::pluck('port')->toArray();
        if (in_array($Port, $PortOccupied) == true) {
            return [
                'ok'  => false,
                'msg' => '端口已被占用'
            ];
        }
        $this->money -= $price;
        $this->setPort($Port);
        $this->save();
        return [
            'ok'  => true,
            'msg' => '钦定成功'
        ];
    }

    /**
     * 用户下次流量重置时间
     */
    public function valid_use_loop(): string
    {
        $boughts = Bought::where('userid', $this->id)->orderBy('id', 'desc')->get();
        $data = [];
        foreach ($boughts as $bought) {
            $shop = $bought->shop();
            if ($shop != null && $bought->valid()) {
                $data[] = $bought->reset_time();
            }
        }
        if (count($data) == 0) {
            return '未购买套餐.';
        }
        if (count($data) == 1) {
            return $data[0];
        }
        return '多个有效套餐无法显示.';
    }

    /**
     * 手动修改用户余额时增加充值记录，受限于 Config
     *
     * @param mixed $total 金额
     */
    public function addMoneyLog($total): void
    {
        if ($_ENV['money_from_admin'] && $total != 0) {
            $codeq              = new Code();
            $codeq->code        = ($total > 0 ? '管理员赏赐' : '管理员惩戒');
            $codeq->isused      = 1;
            $codeq->type        = -1;
            $codeq->number      = $total;
            $codeq->usedatetime = date('Y-m-d H:i:s');
            $codeq->userid      = $this->id;
            $codeq->save();
        }
    }

    /**
     * 发送邮件
     *
     * @param string $subject
     * @param string $template
     * @param array  $ary
     * @param array  $files
     */
    public function sendMail(string $subject, string $template, array $ary = [], array $files = [], $is_queue = false): bool
    {
        $result = false;
        if ($is_queue) {
            $new_emailqueue = new EmailQueue;
            $new_emailqueue->to_email = $this->email;
            $new_emailqueue->subject = $subject;
            $new_emailqueue->template = $template;
            $new_emailqueue->time = time();
            $new_emailqueue->array = json_encode($ary);
            $new_emailqueue->save();
            return true;
        }
        // 验证邮箱地址是否正确
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            // 发送邮件
            try {
                Mail::send(
                    $this->email,
                    $subject,
                    $template,
                    array_merge(
                        [
                            'user' => $this
                        ],
                        $ary
                    ),
                    $files
                );
                $result = true;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        return $result;
    }

    /**
     * 发送 Telegram 讯息
     *
     * @param string $text
     */
    public function sendTelegram(string $text): bool
    {
        $result = false;
        if ($this->telegram_id > 0) {
            Telegram::Send(
                $text,
                $this->telegram_id
            );
            $result = true;
        }
        return $result;
    }

    /**
     * 发送每日流量报告
     *
     * @param string $ann 公告
     */
    public function sendDailyNotification(string $ann = ''): void
    {
        $lastday = (($this->u + $this->d) - $this->last_day_t) / 1024 / 1024;
        switch ($this->sendDailyMail) {
            case 0:
                return;
            case 1:
                echo 'Send daily mail to user: ' . $this->id;
                $this->sendMail(
                    $_ENV['appName'] . '-每日流量报告以及公告',
                    'news/daily-traffic-report.tpl',
                    [
                        'user'    => $this,
                        'text'    => '下面是系统中目前的公告:<br><br>' . $ann . '<br><br>晚安！',
                        'lastday' => $lastday
                    ],
                    []
                );
                break;
            case 2:
                echo 'Send daily Telegram message to user: ' . $this->id;
                $text  = date('Y-m-d') . ' 流量使用报告' . PHP_EOL . PHP_EOL;
                $text .= '流量总计：' . $this->enableTraffic() . PHP_EOL;
                $text .= '已用流量：' . $this->usedTraffic() . PHP_EOL;
                $text .= '剩余流量：' . $this->unusedTraffic() . PHP_EOL;
                $text .= '今日使用：' . $lastday . 'MB';
                $this->sendTelegram(
                    $text
                );
                break;
        }
    }

    /**
     * 记录登录 IP
     *
     * @param string $ip
     * @param int    $type 登录失败为 1
     */
    public function collectLoginIP(string $ip, int $type = 0): bool
    {
        $loginip           = new LoginIp();
        $loginip->ip       = $ip;
        $loginip->userid   = $this->id;
        $loginip->datetime = time();
        $loginip->type     = $type;

        return $loginip->save();
    }
}
