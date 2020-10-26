<?php

namespace App\Models;

use App\Utils\{
    Tools,
    Hash,
    GA,
    QQWry,
    Radius,
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
 * @property        bool    $expire_notified    If user is notified for expire
 * @property        bool    $traffic_notified   If user is noticed for low traffic
 */

class User extends Model
{
    protected $connection = 'default';

    protected $table = 'user';

    public $isLogin;

    public $isAdmin;

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
        'sendDailyMail'   => 'int'
    ];

    public function getGravatarAttribute()
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return 'https://cdn.v2ex.com/gravatar/' . $hash . "?&d=identicon";
    }

    public function isAdmin()
    {
        return $this->attributes['is_admin'];
    }

    public function lastSsTime()
    {
        if ($this->attributes['t'] == 0) {
            return '从未使用喵';
        }
        return Tools::toDateTime($this->attributes['t']);
    }

    public function getMuMd5()
    {
        $str = str_replace(
            array('%id', '%suffix'),
            array($this->attributes['id'], $_ENV['mu_suffix']),
            $_ENV['mu_regex']
        );
        preg_match_all("|%-?[1-9]\d*m|U", $str, $matches, PREG_PATTERN_ORDER);
        foreach ($matches[0] as $key) {
            $key_match = str_replace(array('%', 'm'), '', $key);
            $md5 = substr(
                MD5($this->attributes['id'] . $this->attributes['passwd'] . $this->attributes['method'] . $this->attributes['obfs'] . $this->attributes['protocol']),
                ($key_match < 0 ? $key_match : 0),
                abs($key_match)
            );
            $str = str_replace($key, $md5, $str);
        }
        return $str;
    }

    public function lastCheckInTime()
    {
        if ($this->attributes['last_check_in_time'] == 0) {
            return '从未签到';
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
        return str_replace(',', PHP_EOL, $this->attributes['forbidden_ip']);
    }

    public function get_forbidden_port()
    {
        return str_replace(',', PHP_EOL, $this->attributes['forbidden_port']);
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
        while (true) {
            $temp_code = Tools::genRandomChar(4);
            if (InviteCode::where('user_id', $uid)->count() == 0) {
                break;
            }
        }
        $code->code = $temp_code;
        $code->user_id = $uid;
        $code->save();
    }

    public function getUuid()
    {
        $uuid = $this->attributes['uuid'];
        if ($uuid == '') {
            $uuid =  Uuid::uuid3(
                Uuid::NAMESPACE_DNS,
                $this->attributes['id'] . '|' . $this->attributes['passwd']
            )->toString();
        }
        
        return $uuid;
    }

    /*
     * 总流量
     */
    public function enableTraffic()
    {
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowAutoShow($transfer_enable);
    }

    /*
     * 总流量[GB]
     */
    public function enableTrafficInGB()
    {
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowToGB($transfer_enable);
    }

    /*
     * 已用流量
     */
    public function usedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        return Tools::flowAutoShow($total);
    }

    /*
     * 已用流量占总流量的百分比
     */
    public function trafficUsagePercent()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transferEnable = $this->attributes['transfer_enable'];
        if ($transferEnable == 0) {
            return 0;
        }
        $percent = $total / $transferEnable;
        $percent = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 剩余流量
     */
    public function unusedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowAutoShow($transfer_enable - $total);
    }

    /*
     * 剩余流量占总流量的百分比
     */
    public function unusedTrafficPercent()
    {
        $transferEnable = $this->attributes['transfer_enable'];
        if ($transferEnable == 0) {
            return 0;
        }
        $unusedTraffic = $transferEnable - ($this->attributes['u'] + $this->attributes['d']);
        $percent = $unusedTraffic / $transferEnable;
        $percent = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 今天使用的流量
     */
    public function TodayusedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'] - $this->attributes['last_day_t'];
        return Tools::flowAutoShow($total);
    }

    /*
     * 今天使用的流量占总流量的百分比
     */
    public function TodayusedTrafficPercent()
    {
        $transferEnable = $this->attributes['transfer_enable'];
        if ($transferEnable == 0) {
            return 0;
        }
        $TodayusedTraffic = $this->attributes['u'] + $this->attributes['d'] - $this->attributes['last_day_t'];
        $percent = $TodayusedTraffic / $transferEnable;
        $percent = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 今天之前已使用的流量
     */
    public function LastusedTraffic()
    {
        $total = $this->attributes['last_day_t'];
        return Tools::flowAutoShow($total);
    }

    /*
     * 今天之前已使用的流量占总流量的百分比
     */
    public function LastusedTrafficPercent()
    {
        $transferEnable = $this->attributes['transfer_enable'];
        if ($transferEnable == 0) {
            return 0;
        }
        $LastusedTraffic = $this->attributes['last_day_t'];
        $percent = $LastusedTraffic / $transferEnable;
        $percent = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 是否可以签到
     */
    public function isAbleToCheckin()
    {
        $last = $this->attributes['last_check_in_time'];

        $now = time();
        return date('Ymd', $now) != date('Ymd', $last);
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
        $url = $ga->getUrl(
            urlencode($_ENV['appName'] . '-' . $this->attributes['user_name'] . '-两步验证码'),
            $this->attributes['ga_token']
        );
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
        return self::where('id', $uid)->first();
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
        $total = Ip::where('datetime', '>=', time() - 90)->where('userid', $uid)->orderBy('userid', 'desc')->get();
        $unique_ip_list = array();
        foreach ($total as $single_record) {
            $single_record->ip = Tools::getRealIp($single_record->ip);
            $is_node = Node::where('node_ip', $single_record->ip)->first();
            if ($is_node) {
                continue;
            }

            if (!in_array($single_record->ip, $unique_ip_list)) {
                $unique_ip_list[] = $single_record->ip;
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
        UserSubscribeLog::where('user_id', '=', $uid)->delete();
        DetectBanLog::where('user_id', '=', $uid)->delete();
        TelegramTasks::where('userid', '=', $uid)->delete();

        $this->delete();

        return true;
    }

    public function get_table_json_array()
    {
        $id = $this->attributes['id'];
        $today_traffic = Tools::flowToMB($this->attributes['u'] + $this->attributes['d'] - $this->attributes['last_day_t']);
        $is_enable = $this->attributes['enable'] == 1 ? '可用' : '禁用';
        $reg_location = $this->attributes['reg_ip'];
        $account_expire_in = $this->attributes['expire_in'];
        $class_expire_in = $this->attributes['class_expire'];
        $used_traffic = Tools::flowToGB($this->attributes['u'] + $this->attributes['d']);
        $enable_traffic = Tools::flowToGB($this->attributes['transfer_enable']);

        $im_type = '';
        $im_value = $this->attributes['im_value'];
        switch ($this->attributes['im_type']) {
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
                $im_value = '<a href="https://telegram.me/' . $im_value . '">' . $im_value . '</a>';
        }

        $ref_user = self::find($this->attributes['ref_by']);

        if ($this->attributes['ref_by'] == 0) {
            $ref_user_id = 0;
            $ref_user_name = '系统邀请';
        } elseif ($ref_user == null) {
            $ref_user_id = $this->attributes['ref_by'];
            $ref_user_name = '邀请人已经被删除';
        } else {
            $ref_user_id = $this->attributes['ref_by'];
            $ref_user_name = $ref_user->user_name;
        }

        $iplocation = new QQWry();
        $location = $iplocation->getlocation($reg_location);
        $reg_location .= "\n" . iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);

        $return_array = array(
            'DT_RowId' => 'row_1_' . $id,
            $id,
            $id,
            $this->attributes['user_name'],
            $this->attributes['remark'],
            $this->attributes['email'],
            $this->attributes['money'],
            $im_type,
            $im_value,
            $this->attributes['node_group'],
            $account_expire_in,
            $this->attributes['class'],
            $class_expire_in,
            $this->attributes['passwd'],
            $this->attributes['port'],
            $this->attributes['method'],
            $this->attributes['protocol'],
            $this->attributes['obfs'],
            $this->attributes['obfs_param'],
            $this->online_ip_count(),
            $this->lastSsTime(),
            $used_traffic,
            $enable_traffic,
            $this->lastCheckInTime(),
            $today_traffic,
            $is_enable,
            $this->attributes['reg_date'],
            $reg_location,
            $this->attributes['auto_reset_day'],
            $this->attributes['auto_reset_bandwidth'],
            $ref_user_id,
            $ref_user_name
        );
        return $return_array;
    }

    public function get_user_attributes($key)
    {
        return $this->attributes[$key];
    }

    public function get_top_up()
    {
        $codes = Code::where('userid', $this->attributes['id'])->get();
        $top_up = 0;
        foreach ($codes as $code) {
            $top_up += $code->number;
        }
        return round($top_up, 2);
    }

    public function calIncome($req)
    {
        switch ($req) {
            case "yesterday":
                $number = Code::whereDate('usedatetime', '=', date('Y-m-d', strtotime('-1 days')))->sum('number');
                break;
            case "today":
                $number = Code::whereDate('usedatetime', '=', date('Y-m-d'))->sum('number');
                break;
            case "this month":
                $number = Code::whereYear('usedatetime','=',date('Y'))->whereMonth('usedatetime', '=', date('m'))->sum('number');
                break;
            case "last month":
                $number = Code::whereYear('usedatetime','=',date('Y'))->whereMonth('usedatetime', '=', date('m', strtotime('last month')))->sum('number');
                break;
            default:
                $number = Code::sum('number');
                break;
        }
        return is_null($number) ? 0 : $number;
    }

    public function paidUserCount()
    {
        return self::where('class', '!=', '0')->count();
    }

    public function disableReason()
    {
        $reason_id = DetectLog::where('user_id', '=', $this->attributes['id'])->orderBy('id', 'DESC')->first();
        $reason = DetectRule::where('id', '=', $reason_id->list_id)->get();
        return $reason[0]->text;
    }

    // 最后一次被封禁的时间
    public function last_detect_ban_time(): string
    {
        return ($this->attributes['last_detect_ban_time'] == '1989-06-04 00:05:00'
            ? '未被封禁过'
            : $this->attributes['last_detect_ban_time']);
    }

    // 当前解封时间
    public function relieve_time(): string
    {
        $logs = DetectBanLog::where('user_id', $this->attributes['id'])->orderBy('id', 'desc')->first();
        if ($this->attributes['enable'] == 0 && $logs != null) {
            $time = ($logs->end_time + $logs->ban_time * 60);
            return date('Y-m-d H:i:s', $time);
        } else {
            return '当前未被封禁';
        }
    }

    // 累计被封禁的次数
    public function detect_ban_number(): int
    {
        $logs = DetectBanLog::where('user_id', $this->attributes['id'])->get();
        return count($logs);
    }

    // 最后一次封禁的违规次数
    public function user_detect_ban_number(): int
    {
        $logs = DetectBanLog::where('user_id', $this->attributes['id'])->orderBy("id", "desc")->first();
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
     * 更新加密方式
     *
     * @param string $method
     */
    public function setMethod($method): array
    {
        $return = [
            'ok'  => true,
            'msg' => '设置成功，您可自由选用两种客户端来进行连接。'
        ];
        if ($method == '') {
            $return['ok']   = false;
            $return['msg']  = '非法输入';
            return $return;
        }
        if (!Tools::is_param_validate('method', $method)) {
            $return['ok']   = false;
            $return['msg']  = '加密无效';
            return $return;
        }
        $this->method = $method;
        if (!Tools::checkNoneProtocol($this)) {
            $return['ok']   = false;
            $return['msg']  = '系统检测到您将要设置的加密方式为 none ，但您的协议并不在以下协议【' . implode(',', Config::getSupportParam('allow_none_protocol')) . '】之内，请您先修改您的协议，再来修改此处设置。';
            return $return;
        }
        if (!URL::SSCanConnect($this) && !URL::SSRCanConnect($this)) {
            $return['ok']   = false;
            $return['msg']  = '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。';
            return $return;
        }
        $this->updateMethod($method);
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
                !$this->isAdmin()
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
        $origin_port    = $this->port;
        $this->port     = $Port;
        $relay_rules    = Relay::where('user_id', $this->id)->where('port', $origin_port)->get();
        foreach ($relay_rules as $rule) {
            $rule->port = $this->port;
            $rule->save();
        }
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
    public function sendMail(string $subject, string $template, array $ary = [], array $files = [],$is_queue = false): bool
    {
        $result = false;
        if($is_queue){
            $new_emailqueue = new EmailQueue;
            $new_emailqueue->to_email = $this->email;
            $new_emailqueue -> subject = $subject;
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
     * 获取转发规则
     */
    public function getRelays()
    {
        return (!Tools::is_protocol_relay($this)
            ? []
            : Relay::where('user_id', $this->id)->orwhere('user_id', 0)->orderBy('id', 'asc')->get());
    }
}
