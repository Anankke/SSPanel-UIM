<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\{
    Ip,
    User,
    Shop,
    Relay,
    Bought,
    DetectBanLog
};
use App\Services\{
    Auth,
    Mail,
    Config
};
use App\Utils\{
    GA,
    Hash,
    Tools,
    QQWry,
    Radius,
    Cookie
};
use Exception;
use App\Utils\DatatablesHelper;

class UserController extends AdminController
{
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array(
            'op'                    => '操作',
            'querys'                => '查询',
            'id'                    => 'ID',
            'user_name'             => '用户名',
            'remark'                => '备注',
            'email'                 => '邮箱',
            'money'                 => '金钱',
            'im_type'               => '联络方式类型',
            'im_value'              => '联络方式详情',
            'node_group'            => '群组',
            'expire_in'             => '账户过期时间',
            'class'                 => '等级',
            'class_expire'          => '等级过期时间',
            'passwd'                => '连接密码',
            'port'                  => '连接端口',
            'method'                => '加密方式',
            'protocol'              => '连接协议',
            'obfs'                  => '混淆方式',
            'obfs_param'            => '混淆参数',
            'online_ip_count'       => '在线IP数',
            'last_ss_time'          => '上次使用时间',
            'used_traffic'          => '已用流量/GB',
            'enable_traffic'        => '总流量/GB',
            'last_checkin_time'     => '上次签到时间',
            'today_traffic'         => '今日流量/MB',
            'enable'                => '是否启用',
            'reg_date'              => '注册时间',
            'reg_ip'                => '注册IP',
            'auto_reset_day'        => '自动重置流量日',
            'auto_reset_bandwidth'  => '自动重置流量/GB',
            'ref_by'                => '邀请人ID',
            'ref_by_user_name'      => '邀请人用户名',
            'top_up'                => '累计充值'
        );
        $table_config['default_show_column'] = array('op', 'id', 'user_name', 'remark', 'email');
        $table_config['ajax_url'] = 'user/ajax';
        $shops = Shop::where('status', 1)->orderBy('name')->get();
        return $this->view()
            ->assign('shops', $shops)
            ->assign('table_config', $table_config)
            ->display('admin/user/index.tpl');
    }

    public function createNewUser($request, $response, $args)
    {
        # 需要一个 userEmail
        $email = $request->getParam('userEmail');
        $email = trim($email);
        $email = strtolower($email);

        $money   = (int) trim($request->getParam('userMoney'));
        $shop_id = (int) $request->getParam('userShop');

        // not really user input
        //if (!Check::isEmailLegal($email)) {
        //    $res['ret'] = 0;
        //   $res['msg'] = '邮箱无效';
        //   return $response->getBody()->write(json_encode($res));
        //}
        // check email
        $user = User::where('email', $email)->first();
        if ($user != null) {
            $res['ret'] = 0;
            $res['msg'] = '邮箱已经被注册了';
            return $response->getBody()->write(json_encode($res));
        }
        // do reg user
        $user                       = new User();
        $pass                       = Tools::genRandomChar();
        $user->user_name            = $email;
        $user->email                = $email;
        $user->pass                 = Hash::passwordHash($pass);
        $user->passwd               = Tools::genRandomChar(6);
        $user->port                 = Tools::getAvPort();
        $user->t                    = 0;
        $user->u                    = 0;
        $user->d                    = 0;
        $user->method               = Config::getconfig('Register.string.defaultMethod');
        $user->protocol             = Config::getconfig('Register.string.defaultProtocol');
        $user->protocol_param       = Config::getconfig('Register.string.defaultProtocol_param');
        $user->obfs                 = Config::getconfig('Register.string.defaultObfs');
        $user->obfs_param           = Config::getconfig('Register.string.defaultObfs_param');
        $user->forbidden_ip         = $_ENV['reg_forbidden_ip'];
        $user->forbidden_port       = $_ENV['reg_forbidden_port'];
        $user->im_type              = 2;
        $user->im_value             = $email;
        $user->transfer_enable      = Tools::toGB((int) Config::getconfig('Register.string.defaultTraffic'));
        $user->invite_num           = (int) Config::getconfig('Register.string.defaultInviteNum');
        $user->auto_reset_day       = $_ENV['reg_auto_reset_day'];
        $user->auto_reset_bandwidth = $_ENV['reg_auto_reset_bandwidth'];
        $user->money                = ($money != -1 ? $money : 0);
        $user->class_expire         = date('Y-m-d H:i:s', time() + (int) Config::getconfig('Register.string.defaultClass_expire') * 3600);
        $user->class                = (int) Config::getconfig('Register.string.defaultClass');
        $user->node_connector       = (int) Config::getconfig('Register.string.defaultConn');
        $user->node_speedlimit      = (int) Config::getconfig('Register.string.defaultSpeedlimit');
        $user->expire_in            = date('Y-m-d H:i:s', time() + (int) Config::getconfig('Register.string.defaultExpire_in') * 86400);
        $user->reg_date             = date('Y-m-d H:i:s');
        $user->reg_ip               = $_SERVER['REMOTE_ADDR'];
        $user->plan                 = 'A';
        $user->theme                = $_ENV['theme'];

        $groups = explode(',', $_ENV['random_group']);

        $user->node_group = $groups[array_rand($groups)];

        $ga = new GA();
        $secret = $ga->createSecret();

        $user->ga_token = $secret;
        $user->ga_enable = 0;
        if ($user->save()) {
            $res['ret']         = 1;
            $res['msg']         = '新用户注册成功 用户名: ' . $email . ' 随机初始密码: ' . $pass;
            $res['email_error'] = 'success';
            if ($shop_id > 0) {
                $shop = Shop::find($shop_id);
                if ($shop != null) {
                    $bought           = new Bought();
                    $bought->userid   = $user->id;
                    $bought->shopid   = $shop->id;
                    $bought->datetime = time();
                    $bought->renew    = 0;
                    $bought->coupon   = '';
                    $bought->price    = $shop->price;
                    $bought->save();
                    $shop->buy($user);
                } else {
                    $res['msg'] .= '<br/>但是套餐添加失败了，原因是套餐不存在';
                }
            }
            $user->addMoneyLog($user->money);
            $subject            = $_ENV['appName'] . '-新用户注册通知';
            $to                 = $user->email;
            $text               = '您好，管理员已经为您生成账户，用户名: ' . $email . '，登录密码为：' . $pass . '，感谢您的支持。 ';
            try {
                Mail::send($to, $subject, 'newuser.tpl', [
                    'user' => $user, 'text' => $text,
                ], []);
            } catch (Exception $e) {
                $res['email_error'] = $e->getMessage();
            }
            return $response->getBody()->write(json_encode($res));
        }
        $res['ret'] = 0;
        $res['msg'] = '未知错误';
        return $response->getBody()->write(json_encode($res));
    }

    public function buy($request, $response, $args)
    {
        #shop 信息可以通过 App\Controllers\UserController:shop 获得
        # 需要shopId，disableothers，autorenew,userEmail

        $shopId         = $request->getParam('shopId');
        $shop           = Shop::where('id', $shopId)->where('status', 1)->first();
        $disableothers  = $request->getParam('disableothers');
        $autorenew      = $request->getParam('autorenew');
        $email          = $request->getParam('userEmail');
        $user           = User::where('email', '=', $email)->first();
        if ($user == null) {
            $result['ret'] = 0;
            $result['msg'] = '未找到该用户';
            return $response->getBody()->write(json_encode($result));
        }
        if ($shop == null) {
            $result['ret'] = 0;
            $result['msg'] = '请选择套餐';
            return $response->getBody()->write(json_encode($result));
        }
        if ($disableothers == 1) {
            $boughts = Bought::where('userid', $user->id)->get();
            foreach ($boughts as $disable_bought) {
                $disable_bought->renew = 0;
                $disable_bought->save();
            }
        }
        $bought           = new Bought();
        $bought->userid   = $user->id;
        $bought->shopid   = $shop->id;
        $bought->datetime = time();
        if ($autorenew == 0 || $shop->auto_renew == 0) {
            $bought->renew = 0;
        } else {
            $bought->renew = time() + $shop->auto_renew * 86400;
        }

        $price = $shop->price;
        $bought->price = $price;
        $bought->save();

        $shop->buy($user);
        $result['ret'] = 1;
        $result['msg'] = '套餐添加成功';
        return $response->getBody()->write(json_encode($result));
    }

    public function search($request, $response, $args)
    {
        $pageNum = 1;
        $text = $args['text'];
        if (isset($request->getQueryParams()['page'])) {
            $pageNum = $request->getQueryParams()['page'];
        }

        $users = User::where('email', 'LIKE', '%' . $text . '%')->orWhere('user_name', 'LIKE', '%' . $text . '%')->orWhere('im_value', 'LIKE', '%' . $text . '%')->orWhere('port', 'LIKE', '%' . $text . '%')->orWhere('remark', 'LIKE', '%' . $text . '%')->paginate(20, ['*'], 'page', $pageNum);

        //Ip::where("datetime","<",time()-90)->get()->delete();
        $total = Ip::where('datetime', '>=', time() - 90)->orderBy('userid', 'desc')->get();

        $userip = array();
        $useripcount = array();
        $regloc = array();

        $iplocation = new QQWry();
        foreach ($users as $user) {
            $useripcount[$user->id] = 0;
            $userip[$user->id] = array();

            $location = $iplocation->getlocation($user->reg_ip);
            $regloc[$user->id] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        }

        foreach ($total as $single) {
            if (isset($useripcount[$single->userid]) && !isset($userip[$single->userid][$single->ip])) {
                ++$useripcount[$single->userid];
                $location = $iplocation->getlocation($single->ip);
                $userip[$single->userid][$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            }
        }

        return $this->view()->assign('users', $users)->assign('regloc', $regloc)->assign('useripcount', $useripcount)->assign('userip', $userip)->display('admin/user/index.tpl');
    }

    public function sort($request, $response, $args)
    {
        $pageNum = 1;
        $text = $args['text'];
        $asc = $args['asc'];
        if (isset($request->getQueryParams()['page'])) {
            $pageNum = $request->getQueryParams()['page'];
        }

        $users->setPath('/admin/user/sort/' . $text . '/' . $asc);

        //Ip::where("datetime","<",time()-90)->get()->delete();
        $total = Ip::where('datetime', '>=', time() - 90)->orderBy('userid', 'desc')->get();

        $userip = array();
        $useripcount = array();
        $regloc = array();

        $iplocation = new QQWry();
        foreach ($users as $user) {
            $useripcount[$user->id] = 0;
            $userip[$user->id] = array();

            $location = $iplocation->getlocation($user->reg_ip);
            $regloc[$user->id] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        }

        foreach ($total as $single) {
            if (isset($useripcount[$single->userid]) && !isset($userip[$single->userid][$single->ip])) {
                ++$useripcount[$single->userid];
                $location = $iplocation->getlocation($single->ip);
                $userip[$single->userid][$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            }
        }

        return $this->view()->assign('users', $users)->assign('regloc', $regloc)->assign('useripcount', $useripcount)->assign('userip', $userip)->display('admin/user/index.tpl');
    }

    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        return $this->view()->assign('edit_user', $user)->display('admin/user/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        $email1 = $user->email;

        $user->email = $request->getParam('email');

        $email2 = $request->getParam('email');

        $passwd = $request->getParam('passwd');

        Radius::ChangeUserName($email1, $email2, $passwd);

        if ($request->getParam('pass') != '') {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
            $user->clean_link();
        }

        $user->auto_reset_day = $request->getParam('auto_reset_day');
        $user->auto_reset_bandwidth = $request->getParam('auto_reset_bandwidth');
        $origin_port = $user->port;
        $user->port = $request->getParam('port');

        $relay_rules = Relay::where('user_id', $user->id)->where('port', $origin_port)->get();
        foreach ($relay_rules as $rule) {
            $rule->port = $user->port;
            $rule->save();
        }

        $user->addMoneyLog($request->getParam('money') - $user->money);

        $user->passwd           = $request->getParam('passwd');
        $user->protocol         = $request->getParam('protocol');
        $user->protocol_param   = $request->getParam('protocol_param');
        $user->obfs             = $request->getParam('obfs');
        $user->obfs_param       = $request->getParam('obfs_param');
        $user->is_multi_user    = $request->getParam('is_multi_user');
        $user->transfer_enable  = Tools::toGB($request->getParam('transfer_enable'));
        $user->invite_num       = $request->getParam('invite_num');
        $user->method           = $request->getParam('method');
        $user->node_speedlimit  = $request->getParam('node_speedlimit');
        $user->node_connector   = $request->getParam('node_connector');
        $user->enable           = $request->getParam('enable');
        $user->is_admin         = $request->getParam('is_admin');
        $user->ga_enable        = $request->getParam('ga_enable');
        $user->node_group       = $request->getParam('group');
        $user->ref_by           = $request->getParam('ref_by');
        $user->remark           = $request->getParam('remark');
        $user->money            = $request->getParam('money');
        $user->class            = $request->getParam('class');
        $user->class_expire     = $request->getParam('class_expire');
        $user->expire_in        = $request->getParam('expire_in');

        $user->forbidden_ip     = str_replace(PHP_EOL, ',', $request->getParam('forbidden_ip'));
        $user->forbidden_port   = str_replace(PHP_EOL, ',', $request->getParam('forbidden_port'));

        // 手动封禁
        $ban_time = (int) $request->getParam('ban_time');
        if ($ban_time > 0) {
            $user->enable                       = 0;
            $end_time                           = date('Y-m-d H:i:s');
            $user->last_detect_ban_time         = $end_time;
            $DetectBanLog                       = new DetectBanLog();
            $DetectBanLog->user_name            = $user->user_name;
            $DetectBanLog->user_id              = $user->id;
            $DetectBanLog->email                = $user->email;
            $DetectBanLog->detect_number        = '0';
            $DetectBanLog->ban_time             = $ban_time;
            $DetectBanLog->start_time           = strtotime('1989-06-04 00:05:00');
            $DetectBanLog->end_time             = strtotime($end_time);
            $DetectBanLog->all_detect_number    = $user->all_detect_number;
            $DetectBanLog->save();
        }

        if (!$user->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '修改失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '修改成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $user = User::find($id);
        if (!$user->kill_user()) {
            $rs['ret'] = 0;
            $rs['msg'] = '删除失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '删除成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function changetouser($request, $response, $args)
    {
        $userid     = $request->getParam('userid');
        $adminid    = $request->getParam('adminid');
        $user       = User::find($userid);
        $admin      = User::find($adminid);
        $expire_in  = time() + 60 * 60;

        if (!$admin->is_admin || !$user || !Auth::getUser()->isLogin) {
            $rs['ret'] = 0;
            $rs['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($rs));
        }

        Cookie::set([
            'uid'           => $user->id,
            'email'         => $user->email,
            'key'           => Hash::cookieHash($user->pass, $expire_in),
            'ip'            => md5($_SERVER['REMOTE_ADDR'] . $_ENV['key'] . $user->id . $expire_in),
            'expire_in'     => $expire_in,
            'old_uid'       => Cookie::get('uid'),
            'old_email'     => Cookie::get('email'),
            'old_key'       => Cookie::get('key'),
            'old_ip'        => Cookie::get('ip'),
            'old_expire_in' => Cookie::get('expire_in'),
            'old_local'     => $request->getParam('local'),
        ], $expire_in);
        $rs['ret'] = 1;
        $rs['msg'] = '切换成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function ajax($request, $response, $args)
    {
        //得到排序的方式
        $order        = $request->getParam('order')[0]['dir'];
        //得到排序字段的下标
        $order_column = $request->getParam('order')[0]['column'];
        //根据排序字段的下标得到排序字段
        $order_field  = $request->getParam('columns')[$order_column]['data'];
        $limit_start  = $request->getParam('start');
        $limit_length = $request->getParam('length');
        $search       = $request->getParam('search')['value'];

        if ($order_field == 'used_traffic') {
            $order_field = 'u + d';
        } elseif ($order_field == 'enable_traffic') {
            $order_field = 'transfer_enable';
        } elseif ($order_field == 'today_traffic') {
            $order_field = 'u +d - last_day_t';
        } elseif ($order_field == 'querys') {
            $order_field = 'id';
        }

        $users = array();
        $count_filtered = 0;

        $query = User::query();
        if ($search) {
            $v          = (int) (new DatatablesHelper())->query('select version()')[0]['version()'];
            $like_str   = ($v < 8 ? 'LIKE' : 'LIKE binary');
            $query->where('id', 'LIKE', "%$search%")
                ->orwhere('user_name', 'LIKE', "%$search%")
                ->orwhere('email', 'LIKE', "%$search%")
                ->orwhere('passwd', 'LIKE', "%$search%")
                ->orwhere('port', 'LIKE', "%$search%")
                ->orwhere('invite_num', 'LIKE', "%$search%")
                ->orwhere('money', 'LIKE', "%$search%")
                ->orwhere('ref_by', 'LIKE', "%$search%")
                ->orwhere('method', 'LIKE', "%$search%")
                ->orwhere('reg_ip', 'LIKE', "%$search%")
                ->orwhere('node_speedlimit', 'LIKE', "%$search%")
                ->orwhere('im_value', 'LIKE', "%$search%")
                ->orwhere('class', 'LIKE', "%$search%")
                ->orwhere('remark', 'LIKE', "%$search%")
                ->orwhere('node_group', 'LIKE', "%$search%")
                ->orwhere('auto_reset_day', 'LIKE', "%$search%")
                ->orwhere('auto_reset_bandwidth', 'LIKE', "%$search%")
                ->orwhere('protocol', 'LIKE', "%$search%")
                ->orwhere('protocol_param', 'LIKE', "%$search%")
                ->orwhere('obfs', 'LIKE', "%$search%")
                ->orwhere('obfs_param', 'LIKE', "%$search%")
                ->orwhere('reg_date', $like_str, "%$search%")
                ->orwhere('class_expire', $like_str, "%$search%")
                ->orwhere('expire_in', $like_str, "%$search%");
        }
        $query_count = clone $query;
        $users = $query->orderByRaw($order_field . ' ' . $order)
            ->skip($limit_start)->limit($limit_length)
            ->get();
        $count_filtered = $query_count->count();

        $data = array();
        foreach ($users as $user) {
            $tempdata = array();
            //model里是casts所以没法直接 $tempdata=(array)$user
            $tempdata['op']         = '<a class="btn btn-brand" href="/admin/user/' . $user->id . '/edit">编辑</a>
                    <a class="btn btn-brand-accent" id="delete" href="javascript:void(0);" onClick="delete_modal_show(\'' . $user->id . '\')">删除</a>
                    <a class="btn btn-brand" id="changetouser" href="javascript:void(0);" onClick="changetouser_modal_show(\'' . $user->id . '\')">切换为该用户</a>';

            $tempdata['querys']     = '<a class="btn btn-brand" href="/admin/user/' . $user->id . '/bought">套餐</a>
                    <a class="btn btn-brand" href="/admin/user/' . $user->id . '/code">充值</a>
                    <a class="btn btn-brand" href="/admin/user/' . $user->id . '/sublog">订阅</a>
                    <a class="btn btn-brand" href="/admin/user/' . $user->id . '/detect">审计</a>
                    <a class="btn btn-brand" href="/admin/user/' . $user->id . '/traffic">流量</a>
                    <a class="btn btn-brand" href="/admin/user/' . $user->id . '/login">登录</a>';

            $tempdata['id']         = $user->id;
            $tempdata['user_name']  = $user->user_name;
            $tempdata['remark']     = $user->remark;
            $tempdata['email']      = $user->email;
            $tempdata['money']      = $user->money;
            $tempdata['im_value']   = $user->im_value;
            switch ($user->im_type) {
                case 1:
                    $tempdata['im_type'] = '微信';
                    break;
                case 2:
                    $tempdata['im_type'] = 'QQ';
                    break;
                case 3:
                    $tempdata['im_type'] = 'Google+';
                    break;
                default:
                    $tempdata['im_type'] = 'Telegram';
                    $tempdata['im_value'] = '<a href="https://telegram.me/' . $user->im_value . '">' . $user->im_value . '</a>';
            }
            $tempdata['node_group']           = $user->node_group;
            $tempdata['expire_in']            = $user->expire_in;
            $tempdata['class']                = $user->class;
            $tempdata['class_expire']         = $user->class_expire;
            $tempdata['passwd']               = $user->passwd;
            $tempdata['port']                 = $user->port;
            $tempdata['method']               = $user->method;
            $tempdata['protocol']             = $user->protocol;
            $tempdata['obfs']                 = $user->obfs;
            $tempdata['obfs_param']           = $user->obfs_param;
            $tempdata['online_ip_count']      = $user->online_ip_count();
            $tempdata['last_ss_time']         = $user->lastSsTime();
            $tempdata['used_traffic']         = Tools::flowToGB($user->u + $user->d);
            $tempdata['enable_traffic']       = Tools::flowToGB($user->transfer_enable);
            $tempdata['last_checkin_time']    = $user->lastCheckInTime();
            $tempdata['today_traffic']        = Tools::flowToMB($user->u + $user->d - $user->last_day_t);
            $tempdata['enable']               = $user->enable == 1 ? '可用' : '禁用';
            $tempdata['reg_date']             = $user->reg_date;
            $tempdata['reg_ip']               = $user->reg_ip;
            $tempdata['auto_reset_day']       = $user->auto_reset_day;
            $tempdata['auto_reset_bandwidth'] = $user->auto_reset_bandwidth;
            $tempdata['ref_by']               = $user->ref_by;
            if ($user->ref_by == 0) {
                $tempdata['ref_by_user_name'] = '系统邀请';
            } else {
                $ref_user = User::find($user->ref_by);
                if ($ref_user == null) {
                    $tempdata['ref_by_user_name'] = '邀请人已经被删除';
                } else {
                    $tempdata['ref_by_user_name'] = $ref_user->user_name;
                }
            }

            $tempdata['top_up'] = $user->get_top_up();

            $data[] = $tempdata;
        }
        $info = [
            'draw'            => $request->getParam('draw'), // ajax请求次数，作为标识符
            'recordsTotal'    => User::count(),
            'recordsFiltered' => $count_filtered,
            'data'            => $data,
        ];
        return json_encode($info, true);
    }
}
