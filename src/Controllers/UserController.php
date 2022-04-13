<?php
namespace App\Controllers;

use App\Services\{
    Auth,
    Captcha,
    Config,
    Payment
};
use App\Models\{
    Ip,
    Ann,
    Code,
    Node,
    Shop,
    User,
    Token,
    Bought,
    Coupon,
    Product,
    Payback,
    BlockIp,
    LoginIp,
    Setting,
    UnblockIp,
    DetectLog,
    DetectRule,
    InviteCode,
    StreamMedia,
    EmailVerify,
    ProductOrder,
    UserSubscribeLog
};
use App\Utils\{
    GA,
    URL,
    Hash,
    Check,
    QQWry,
    Tools,
    Cookie,
    Telegram,
    ClientProfiles,
    DatatablesHelper,
    TelegramSessionManager
};
use voku\helper\AntiXSS;
use Ramsey\Uuid\Uuid;
use Slim\Http\{
    Request,
    Response
};

class UserController extends BaseController
{
    public function productIndex($request, $response, $args)
    {
        $products = Product::where('status', '1')->get();
        $product_lists = [
            'tatp' => '时间流量包',
            'time' => '时间包',
            'traffic' => '流量包',
            'other' => '其他商品',
        ];
        $products_count_tatp = Product::where('status', '1')->where('type', 'tatp')->count();
        $products_count_time = Product::where('status', '1')->where('type', 'time')->count();
        $products_count_traffic = Product::where('status', '1')->where('type', 'traffic')->count();

        return $response->write(
            $this->view()
                ->assign('products', $products)
                ->assign('product_lists', $product_lists)
                ->assign('products_count_tatp', $products_count_tatp)
                ->assign('products_count_time', $products_count_time)
                ->assign('products_count_traffic', $products_count_traffic)
                ->display('user/product.tpl')
        );
    }

    public function couponCheck($request, $response, $args)
    {
        $coupon_code = trim($request->getParam('coupon'));
        $product_id = $request->getParam('product_id');

        try {
            $coupon = Coupon::where('coupon', $coupon_code)->first();
            if ($coupon == null) {
                throw new \Exception('优惠码不存在');
            }
            if ($coupon->product_limit != '0') {
                $scope = explode(',', $coupon->product_limit);
                if (!in_array($product_id, $scope)) {
                    throw new \Exception('优惠码不适用于此商品');
                }
            }
            if ($coupon->use_count > $coupon->total_limit) {
                throw new \Exception('优惠码已达总使用限制');
            }
            if (time() > $coupon->expired_at) {
                throw new \Exception('优惠码已过期');
            }
            // 差一个检查用户使用此优惠码限制的代码
        } catch (\Exception $e) {
            $res['ret'] = 0;
            $res['msg'] = $e->getMessage();
            return $response->withJson($res);
        }

        $res['ret'] = 1;
        $res['discount'] = $coupon->discount;
        return $response->withJson($res);
    }

    public function createOrder($request, $response, $args)
    {
        $user = $this->user;
        $coupon_code = $request->getParam('coupon');
        $product_id = $request->getParam('product_id');

        $product = Product::find($product_id);

        try {
            if ($coupon_code != '') {
                $coupon = Coupon::where('coupon', $coupon_code)->first();
                if ($coupon == null) {
                    throw new \Exception('优惠码不存在');
                }
                if ($coupon->product_limit != '0') {
                    $scope = explode(',', $coupon->product_limit);
                    if (!in_array($product_id, $scope)) {
                        throw new \Exception('优惠码不适用于此商品');
                    }
                }
                if ($coupon->use_count > $coupon->total_limit) {
                    throw new \Exception('优惠码已达总使用限制');
                }
                if (time() > $coupon->expired_at) {
                    throw new \Exception('优惠码已过期');
                }
            }

            $order = new ProductOrder;
            $order->no = substr(md5(time()), 20);
            $order->user_id = $user->id;
            $order->product_id = $product->id;
            $order->product_name = $product->name;
            $order->product_type = $product->type;
            $order->product_content = $product->translate;
            $order->product_price = $product->price;
            $order->order_coupon = (empty($coupon)) ? null : $coupon_code;
            $order->order_price = (empty($coupon)) ? $product->price : $product->price * $coupon->discount;
            $order->order_status = 'pending_payment';
            $order->created_at = time();
            $order->updated_at = time();
            $order->expired_at = time() + 600;
            $order->paid_at = time();
            $order->paid_action = json_encode(['action' => 'buy_product', 'params' => $product->id]);
            $order->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'order_id' => $order->no
        ]);
    }

    public function orderDetails($request, $response, $args)
    {
        $order_no = $args['no'];
        $order = ProductOrder::where('user_id', $this->user->id)
        ->where('no', $order_no)
        ->first();

        return $response->write(
            $this->view()
                ->assign('order', $order)
                ->display('user/order/read.tpl')
        );
    }

    public function orderIndex($request, $response, $args)
    {
        $orders = ProductOrder::where('user_id', $this->user->id)->get();

        return $response->write(
            $this->view()
                ->assign('orders', $orders)
                ->display('user/order.tpl')
        );
    }

    public function processOrder($request, $response, $args)
    {
        $user = $this->user;
        $payment = $request->getParam('method');
        $order_no = $request->getParam('order_no');

        $order = ProductOrder::where('user_id', $user->id)
        ->where('no', $order_no)
        ->first();

        $product = Product::find($order->product_id);
        if ($order->order_coupon != null) {
            $coupon = Coupon::where('coupon', $order->order_coupon)->first();
        }

        try {
            if (time() > $order->expired_at) {
                throw new \Exception('此订单已过期');
            }
            if ($order->order_status == 'paid') {
                throw new \Exception('此订单已支付');
            }
            if ($product->stock <= 0) {
                throw new \Exception('商品库存不足');
            }
            if ($payment == 'balance') {
                if ($user->money < ($order->order_price / 100)) {
                    throw new \Exception('账户余额不足');
                }

                $user->money -= $order->order_price / 100;
                $user->save();

                $order->order_status = 'paid';
                $order->updated_at = time();
                $order->paid_at = time();
                $order->save();

                $product->stock -= 1; // 减库存
                $product->sales += 1; // 加销量
                $product->save();

                if (!empty($coupon)) {
                    $coupon->use_count += 1;
                    $coupon->amount_count += ($order->product_price - $order->order_price) / 100;
                    $coupon->save();
                }
            } else {

            }
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '购买成功'
        ]);
    }

    public function resetPort($request, $response, $args)
    {
        $temp = $this->user->ResetPort();
        return $response->withJson([
            'ret' => ($temp['ok'] == true ? 1 : 0),
            'msg' => '新的端口是 '. $temp['msg']
        ]);
    }

    public function profile($request, $response, $args)
    {
        $use_logs = Ip::where('userid', $this->user->id)
        ->where('datetime', '>=', time() - 300)
        ->get();

        $totallogin = LoginIp::where('userid', $this->user->id)
        ->orderBy('datetime', 'desc')
        ->where('type', '0')
        ->take(10)
        ->get();

        return $response->write(
            $this->view()
                ->assign('use_logs', $use_logs)
                ->assign('userloginip', $totallogin)
                ->registerClass('Tools', Tools::class)
                ->display('user/profile.tpl')
        );
    }

    public function invite($request, $response, $args)
    {
        $code = InviteCode::where('user_id', $this->user->id)->first();
        if ($code == null) {
            $this->user->addInviteCode();
            $code = InviteCode::where('user_id', $this->user->id)->first();
        }

        $paybacks = Payback::where('ref_by', $this->user->id)->get();
        $paybacks_sum = Payback::where('ref_by', $this->user->id)->sum('ref_get');
        $invite_url = $_ENV['baseUrl'] . '/auth/register?code=' . $code->code;

        return $this->view()
            ->assign('code', $code)
            ->assign('paybacks', $paybacks)
            ->assign('paybacks_sum', $paybacks_sum)
            ->assign('invite_url', $invite_url)
            ->display('user/invite.tpl');
    }

    public function isHTTPS()
    {
        define('HTTPS', false);
        if (defined('HTTPS') && HTTPS) {
            return true;
        }
        if (!isset($_SERVER)) {
            return false;
        }
        if (!isset($_SERVER['HTTPS'])) {
            return false;
        }
        if ($_SERVER['HTTPS'] === 1) {  //Apache
            return true;
        }
        if ($_SERVER['HTTPS'] === 'on') { //IIS
            return true;
        }
        if ($_SERVER['SERVER_PORT'] == 443) { //其他
            return true;
        }

        return false;
    }

    public function gaCheck($request, $response, $args)
    {
        $code = $request->getParam('code');

        try {
            if ($code == '') {
                throw new \Exception('请填写验证码');
            }

            $ga = new GA();
            $user = $this->user;
            $rcode = $ga->verifyCode($user->ga_token, $code);

            if (!$rcode) {
                throw new \Exception('验证码错误');
            }
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '验证码正确'
        ]);
    }

    public function gaSet($request, $response, $args)
    {
        $user = $this->user;
        $enable = $request->getParam('enable');
        $user->ga_enable = ($enable == '1') ? '1' : '0';
        $user->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '设置成功'
        ]);
    }

    public function gaReset($request, $response, $args)
    {
        $ga = new GA();
        $user = $this->user;
        $secret = $ga->createSecret();
        $user->ga_token = $secret;
        $user->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '重置成功'
        ]);
    }

    public function announcement($request, $response, $args)
    {
        $anns = Ann::orderBy('date', 'desc')->get();

        return $response->write(
            $this->view()
                ->assign('anns', $anns)
                ->display('user/announcement.tpl')
        );
    }

    public function updatePassword($request, $response, $args)
    {
        $user = $this->user;
        $pwd = $request->getParam('pwd');
        $repwd = $request->getParam('repwd');
        $oldpwd = $request->getParam('oldpwd');

        if (!Hash::checkPassword($user->pass, $oldpwd)) {
            $res['ret'] = 0;
            $res['msg'] = '当前密码不正确';
            return $response->withJson($res);
        }

        if ($pwd != $repwd) {
            $res['ret'] = 0;
            $res['msg'] = '两次输入不符';
            return $response->withJson($res);
        }

        if (strlen($pwd) < 8) {
            $res['ret'] = 0;
            $res['msg'] = '新密码长度不足 8 位';
            return $response->withJson($res);
        }

        $hashPwd = Hash::passwordHash($pwd);
        $user->pass = $hashPwd;
        $user->save();

        if ($_ENV['enable_forced_replacement'] == true) {
            $user->clean_link();
        }

        $res['ret'] = 1;
        $res['msg'] = '修改成功，请重新登录';
        return $response->withJson($res);
    }

    public function updateEmail($request, $response, $args)
    {
        $user = $this->user;
        $oldemail = $user->email;
        $newemail = $request->getParam('newemail');
        $otheruser = User::where('email', $newemail)->first();

        if ($_ENV['enable_change_email'] != true) {
            $res['ret'] = 0;
            $res['msg'] = '此项不允许自行修改，请联系管理员操作';
            return $response->withJson($res);
        }

        if ($newemail == '') {
            $res['ret'] = 0;
            $res['msg'] = '未填写邮箱';
            return $response->withJson($res);
        }

        if (Setting::obtain('reg_email_verify')) {
            $emailcode = $request->getParam('emailcode');
            $mailcount = EmailVerify::where('email', '=', $newemail)
            ->where('code', '=', $emailcode)
            ->where('expire_in', '>', time())
            ->first();

            if ($mailcount == null) {
                $res['ret'] = 0;
                $res['msg'] = '邮箱验证码不正确';
                return $response->withJson($res);
            }
        }

        $check_res = Check::isEmailLegal($newemail);
        if ($check_res['ret'] == 0) {
            return $response->withJson($check_res);
        }

        if ($otheruser != null) {
            $res['ret'] = 0;
            $res['msg'] = '此邮箱已注册';
            return $response->withJson($res);
        }

        if ($newemail == $oldemail) {
            $res['ret'] = 0;
            $res['msg'] = '新邮箱不能和旧邮箱一样';
            return $response->withJson($res);
        }

        $antiXss = new AntiXSS();
        $user->email = $antiXss->xss_clean($newemail);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $response->withJson($res);
    }

    public function updateUsername($request, $response, $args)
    {
        $newusername = $request->getParam('newusername');
        if ($newusername == '') {
            $res['ret'] = 0;
            $res['msg'] = '新用户名不能为空';
            return $response->withJson($res);
        }

        $user = $this->user;
        $antiXss = new AntiXSS();
        $user->user_name = $antiXss->xss_clean($newusername);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $response->withJson($res);
    }

    public function updateWechat($request, $response, $args)
    {
        $user = $this->user;
        $type = $request->getParam('imtype');
        $wechat = trim($request->getParam('wechat'));

        try {
            if ($wechat == '' || $type == '') {
                throw new \Exception('选择社交软件名称并填写联系方式');
            }
            if ($user->telegram_id != 0) {
                throw new \Exception('绑定 Telegram 账户时不能修改此项');
            }
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        $antiXss = new AntiXSS();
        $user->im_type = $antiXss->xss_clean($type);
        $user->im_value = $antiXss->xss_clean($wechat);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $response->withJson($res);
    }

    public function handleKill($request, $response, $args)
    {
        if ($_ENV['enable_kill']) {
            $user = $this->user;
            $passwd = $request->getParam('passwd');

            if (!Hash::checkPassword($user->pass, $passwd)) {
                $res['ret'] = '0';
                $res['msg'] = '当前密码错误，请重试';
                return $response->withJson($res);
            }

            Auth::logout();
            $user->kill_user();

            $res['ret'] = '1';
            $res['msg'] = '已删除你的账户';
        } else {
            $res['ret'] = '0';
            $res['msg'] = '系统不允许主动删除账户，请联系管理员';
        }

        return $response->withJson($res);
    }

    public function detect_index($request, $response, $args)
    {
        $logs = DetectRule::get();
        return $this->view()
            ->assign('rules', $logs)
            ->display('user/detect/index.tpl');
    }

    public function detect_log($request, $response, $args)
    {
        $logs = DetectLog::where('user_id', $this->user->id)
        ->orderBy('id', 'desc')
        ->limit(500)
        ->get();

        return $this->view()
            ->assign('logs', $logs)
            ->display('user/detect/read.tpl');
    }

    public function resetURL($request, $response, $args)
    {
        $user = $this->user;
        $user->clean_link();

        $res['ret'] = 1;
        $res['msg'] = '更换成功';
        return $response->withJson($res);
    }

    public function resetInviteURL($request, $response, $args)
    {
        $user = $this->user;
        $user->clear_inviteCodes();

        $res['ret'] = 1;
        $res['msg'] = '重置成功';
        return $response->withJson($res);
    }

    public function subscribe_log($request, $response, $args)
    {
        if ($_ENV['subscribeLog_show'] == false) {
            return $response->withStatus(302)->withHeader('Location', '/user');
        }

        $logs = UserSubscribeLog::where('user_id', $this->user->id)
        ->orderBy('id', 'desc')
        ->limit(500)
        ->get();

        return $this->view()
            ->assign('logs', $logs)
            ->registerClass('Tools', Tools::class)
            ->fetch('user/subscribe_log.tpl');
    }

    public function updateTheme($request, $response, $args)
    {
        $user = $this->user;
        $theme = $request->getParam('theme');

        if ($theme == '') {
            $res['ret'] = 0;
            $res['msg'] = '请从给出的主题列表中选择一个';
            return $response->withJson($res);
        }

        $user->theme = filter_var($theme, FILTER_SANITIZE_STRING);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '设置成功';
        return $response->withJson($res);
    }

    public function edit($request, $response, $args)
    {
        $config = new Config();
        $themes = Tools::getDir(BASE_PATH . '/resources/views');
        $bind_token = TelegramSessionManager::add_bind_session($this->user);

        return $this->view()
            ->assign('user', $this->user)
            ->assign('themes', $themes)
            ->assign('bind_token', $bind_token)
            ->assign('telegram_bot', $_ENV['telegram_bot'])
            ->assign('config_service', $config)
            ->registerClass('URL', URL::class)
            ->display('user/edit.tpl');
    }

    public function updateMail($request, $response, $args)
    {
        $value = (int) $request->getParam('mail');
        $scope = [0, 1, 2];

        try {
            if (!in_array($value, $scope)) {
                throw new \Exception('请在给出的选项中选择一个');
            }
            if ($value == '2' && $_ENV['enable_telegram'] == false) {
                throw new \Exception('当前无法使用 Telegram 接收每日报告');
            }

            $user = $this->user;
            $user->sendDailyMail = $value;
            $user->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功'
        ]);
    }

    public function disable($request, $response, $args)
    {
        return $this->view()->display('user/disable.tpl');
    }

    public function logout($request, $response, $args)
    {
        Auth::logout();
        return $response->withStatus(302)->withHeader('Location', '/auth/login');
    }

    public function telegram_reset($request, $response, $args)
    {
        $user = $this->user;
        $user->TelegramReset();
        return $response->withStatus(302)->withHeader('Location', '/user/edit');
    }

    public function updateSSR($request, $response, $args)
    {
        $user = $this->user;
        $obfs = $request->getParam('obfs');
        $method = $request->getParam('method');
        $protocol = $request->getParam('protocol');
        $obfs_param = trim($request->getParam('obfs_param'));

        try {
            if ($method == '') {
                throw new \Exception('加密无效');
            }
            if (!Tools::is_param_validate('obfs', $obfs)) {
                throw new \Exception('混淆无效');
            }
            if (!Tools::is_param_validate('obfs', $obfs)) {
                throw new \Exception('混淆无效');
            }
            if (gethostbyname($obfs_param) == $obfs_param) {
                throw new \Exception('混淆参数无效');
            }
            if (!Tools::is_param_validate('protocol', $protocol)) {
                throw new \Exception('协议无效');
            }
            if (!URL::SSCanConnect($user) && !URL::SSRCanConnect($user)) {
                throw new \Exception('组合无效');
            }

            $antiXss = new AntiXSS();
            $user->obfs = $antiXss->xss_clean($obfs);
            $user->method = $antiXss->xss_clean($method);
            $user->protocol = $antiXss->xss_clean($protocol);
            $user->obfs_param = $antiXss->xss_clean($obfs_param);
            $user->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }
        
        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功'
        ]);
    }

    public function doCheckIn($request, $response, $args)
    {
        // 等待重构
    }

    public function updateSsPwd($request, $response, $args)
    {
        $user = $this->user;
        $pwd = Tools::genRandomChar(16);
        $new_uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, $user->email . '|' . time());

        try {
            if (!Tools::is_validate($pwd)) {
                throw new \Exception('密码无效');
            }

            $user->uuid = $new_uuid;
            $user->save();
            $user->updateSsPwd($pwd);
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功'
        ]);
    }

    public function media($request, $response, $args)
    {
        $results = [];
        $db = new DatatablesHelper;
        $nodes = $db->query('SELECT DISTINCT node_id FROM stream_media');

        foreach ($nodes as $node_id)
        {
            $node = Node::where('id', $node_id)->first();

            $unlock = StreamMedia::where('node_id', $node_id)
            ->orderBy('id', 'desc')
            ->where('created_at', '>', time() - 86460) // 只获取最近一天零一分钟内上报的数据
            ->first();

            if ($unlock != null && $node != null) {
                $details = json_decode($unlock->result, true);
                $details = str_replace('Originals Only', '仅限自制', $details);
                $details = str_replace('Oversea Only', '仅限海外', $details);

                foreach ($details as $key => $value)
                {
                    $info = [
                        'node_name' => $node->name,
                        'created_at' => $unlock->created_at,
                        'unlock_item' => $details
                    ];
                }

                array_push($results, $info);
            }
        }

        if ($_ENV['streaming_media_unlock_multiplexing'] != null ) {
            foreach ($_ENV['streaming_media_unlock_multiplexing'] as $key => $value)
            {
                $key_node = Node::where('id', $key)->first();
                $value_node = StreamMedia::where('node_id', $value)
                ->orderBy('id', 'desc')
                ->where('created_at', '>', time() - 86460) // 只获取最近一天零一分钟内上报的数据
                ->first();

                if ($value_node != null) {
                    $details = json_decode($value_node->result, true);
                    $details = str_replace('Originals Only', '仅限自制', $details);
                    $details = str_replace('Oversea Only', '仅限海外', $details);

                    $info = [
                        'node_name' => $key_node->name,
                        'created_at' => $value_node->created_at,
                        'unlock_item' => $details
                    ];

                    array_push($results, $info);
                }
           }
        }

        array_multisort(array_column($results, 'node_name'), SORT_ASC, $results);

        return $this->view()
            ->assign('results', $results)
            ->display('user/media.tpl');
    }

    /*
        上面是整理好的

        下面是等待整理的
    */

    public function index($request, $response, $args)
    {
        $captcha = Captcha::generate();

        if ($_ENV['subscribe_client_url'] != '') {
            $getClient = new Token();
            for ($i = 0; $i < 10; $i++) {
                $token = $this->user->id . Tools::genRandomChar(16);
                $Elink = Token::where('token', '=', $token)->first();
                if ($Elink == null) {
                    $getClient->token = $token;
                    break;
                }
            }
            $getClient->user_id     = $this->user->id;
            $getClient->create_time = time();
            $getClient->expire_time = time() + 10 * 60;
            $getClient->save();
        } else {
            $token = '';
        }

        if (Setting::obtain('enable_checkin_captcha') == true) {
            $geetest_html = $captcha['geetest'];
        } else {
            $geetest_html = null;
        }

        $data = [
            'today_traffic_usage' => ($this->user->transfer_enable == 0) ? 0 : ($this->user->u + $this->user->d - $this->user->last_day_t) / $this->user->transfer_enable * 100,
            'past_traffic_usage' => ($this->user->transfer_enable == 0) ? 0 : $this->user->last_day_t / $this->user->transfer_enable * 100,
            'residual_flow' => ($this->user->transfer_enable==0) ? 0 : ($this->user->transfer_enable - ($this->user->u + $this->user->d)) / $this->user->transfer_enable * 100,
        ];

        return $response->write(
            $this->view()
                ->assign('ssr_sub_token', $this->user->getSublink())
                ->assign('display_ios_class', $_ENV['display_ios_class'])
                ->assign('display_ios_topup', $_ENV['display_ios_topup'])
                ->assign('ios_account', $_ENV['ios_account'])
                ->assign('ios_password', $_ENV['ios_password'])
                ->assign('ann', Ann::orderBy('date', 'desc')->first())
                ->assign('geetest_html', $geetest_html)
                ->assign('mergeSub', $_ENV['mergeSub'])
                ->assign('subUrl', $_ENV['subUrl'])
                ->registerClass('URL', URL::class)
                ->assign('recaptcha_sitekey', $captcha['recaptcha'])
                ->assign('subInfo', LinkController::getSubinfo($this->user, 0))
                ->assign('getUniversalSub', SubController::getUniversalSub($this->user))
                ->assign('getClient', $token)
                ->assign('data', $data)
                ->display('user/index.tpl')
        );
    }

    public function backtoadmin($request, $response, $args)
    {
        $userid = Cookie::get('uid');
        $adminid = Cookie::get('old_uid');
        $user = User::find($userid);
        $admin = User::find($adminid);

        if (!$admin->is_admin || !$user) {
            Cookie::set([
                'uid' => null,
                'email' => null,
                'key' => null,
                'ip' => null,
                'expire_in' => null,
                'old_uid' => null,
                'old_email' => null,
                'old_key' => null,
                'old_ip' => null,
                'old_expire_in' => null,
                'old_local' => null
            ], time() - 1000);
        }
        $expire_in = Cookie::get('old_expire_in');
        $local = Cookie::get('old_local');
        Cookie::set([
            'uid' => Cookie::get('old_uid'),
            'email' => Cookie::get('old_email'),
            'key' => Cookie::get('old_key'),
            'ip' => Cookie::get('old_ip'),
            'expire_in' => $expire_in,
            'old_uid' => null,
            'old_email' => null,
            'old_key' => null,
            'old_ip' => null,
            'old_expire_in' => null,
            'old_local' => null
        ], $expire_in);
        return $response->withStatus(302)->withHeader('Location', $local);
    }

    public function getUserAllURL($request, $response, $args)
    {
        $user = $this->user;
        $type = $request->getQueryParams()["type"];
        $return = '';
        switch ($type) {
            case 'ss':
                $return .= URL::get_NewAllUrl($user, ['type' => 'ss']) . PHP_EOL;
                break;
            case 'ssr':
                $return .= URL::get_NewAllUrl($user, ['type' => 'ssr']) . PHP_EOL;
                break;
            case 'v2ray':
                $return .= URL::get_NewAllUrl($user, ['type' => 'vmess']) . PHP_EOL;
                break;
            default:
                $return .= '悟空别闹！';
                break;
        }
        $response = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->withHeader('Content-Disposition', ' attachment; filename=node.txt');

        return $response->write($return);
    }

    public function getPcClient($request, $response, $args)
    {
        $zipArc = new \ZipArchive();
        $user_token = LinkController::GenerateSSRSubCode($this->user->id);
        $type = trim($request->getQueryParams()['type']);
        // 临时文件存放路径
        $temp_file_path = BASE_PATH . '/storage/';
        // 客户端文件存放路径
        $client_path = BASE_PATH . '/resources/clients/';
        switch ($type) {
            case 'ss-win':
                $user_config_file_name = 'gui-config.json';
                $content = ClientProfiles::getSSPcConf($this->user);
                break;
            case 'ssr-win':
                $user_config_file_name = 'gui-config.json';
                $content = ClientProfiles::getSSRPcConf($this->user);
                break;
            case 'v2rayn-win':
                $user_config_file_name = 'guiNConfig.json';
                $content = ClientProfiles::getV2RayNPcConf($this->user);
                break;
            default:
                return 'gg';
        }
        $temp_file_path .= $type . '_' . $user_token . '.zip';
        $client_path .= $type . '/';
        // 文件存在则先删除
        if (is_file($temp_file_path)) {
            unlink($temp_file_path);
        }
        // 超链接文件内容
        $site_url_content = '[InternetShortcut]' . PHP_EOL . 'URL=' . $_ENV['baseUrl'];
        // 创建 zip 并添加内容
        $zipArc->open($temp_file_path, \ZipArchive::CREATE);
        $zipArc->addFromString($user_config_file_name, $content);
        $zipArc->addFromString('点击访问_' . $_ENV['appName'] . '.url', $site_url_content);
        Tools::folderToZip($client_path, $zipArc, strlen($client_path));
        $zipArc->close();

        $newResponse = $response->withHeader('Content-type', ' application/octet-stream')->withHeader('Content-Disposition', ' attachment; filename=' . $type . '.zip');
        $newResponse->write(file_get_contents($temp_file_path));
        unlink($temp_file_path);

        return $newResponse;
    }

    public function getClientfromToken($request, $response, $args)
    {
        $token = $args['token'];
        $Etoken = Token::where('token', '=', $token)->where('create_time', '>', time() - 60 * 10)->first();
        if ($Etoken == null) {
            return '下载链接已失效，请刷新页面后重新点击.';
        }
        $user = User::find($Etoken->user_id);
        if ($user == null) {
            return null;
        }
        $this->user = $user;
        return $this->getPcClient($request, $response, $args);
    }
}
