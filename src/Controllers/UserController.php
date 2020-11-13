<?php

namespace App\Controllers;

use App\Services\{
    Auth,
    Mail,
    Config,
    Payment,
    BitPayment,
    Gateway\ChenPay,
};
use App\Models\{
    Ip,
    Ann,
    Code,
    Node,
    Shop,
    User,
    Token,
    Relay,
    Bought,
    Coupon,
    Ticket,
    Payback,
    BlockIp,
    LoginIp,
    UnblockIp,
    Speedtest,
    DetectLog,
    DetectRule,
    TrafficLog,
    InviteCode,
    EmailVerify,
    UserSubscribeLog
};
use App\Utils\{
    GA,
    Pay,
    URL,
    Hash,
    Check,
    QQWry,
    Tools,
    Radius,
    Cookie,
    Geetest,
    Telegram,
    ClientProfiles,
    DatatablesHelper,
    TelegramSessionManager
};
use voku\helper\AntiXSS;
use Exception;

/**
 *  HomeController
 */
class UserController extends BaseController
{
    public function index($request, $response, $args)
    {
        $ssr_sub_token = LinkController::GenerateSSRSubCode($this->user->id);

        $GtSdk = null;
        $recaptcha_sitekey = null;
        if ($_ENV['enable_checkin_captcha'] == true) {
            switch ($_ENV['captcha_provider']) {
                case 'recaptcha':
                    $recaptcha_sitekey = $_ENV['recaptcha_sitekey'];
                    break;
                case 'geetest':
                    $uid = time() . random_int(1, 10000);
                    $GtSdk = Geetest::get($uid);
                    break;
            }
        }

        $Ann = Ann::orderBy('date', 'desc')->first();

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

        return $this->view()
            ->assign('ssr_sub_token', $ssr_sub_token)
            ->assign('display_ios_class', $_ENV['display_ios_class'])
            ->assign('display_ios_topup', $_ENV['display_ios_topup'])
            ->assign('ios_account', $_ENV['ios_account'])
            ->assign('ios_password', $_ENV['ios_password'])
            ->assign('ann', $Ann)
            ->assign('geetest_html', $GtSdk)
            ->assign('mergeSub', $_ENV['mergeSub'])
            ->assign('subUrl', $_ENV['subUrl'])
            ->registerClass('URL', URL::class)
            ->assign('recaptcha_sitekey', $recaptcha_sitekey)
            ->assign('subInfo', LinkController::getSubinfo($this->user, 0))
            ->assign('getClient', $token)
            ->display('user/index.tpl');
    }

    public function lookingglass($request, $response, $args)
    {
        $Speedtest = Speedtest::where('datetime', '>', time() - $_ENV['Speedtest_duration'] * 3600)->orderBy('datetime', 'desc')->get();

        return $this->view()->assign('speedtest', $Speedtest)->assign('hour', $_ENV['Speedtest_duration'])->display('user/lookingglass.tpl');
    }

    public function code($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $codes = Code::where('type', '<>', '-2')->where('userid', '=', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $codes->setPath('/user/code');
        return $this->view()->assign('codes', $codes)->assign('pmw', Payment::purchaseHTML())->assign('bitpay', BitPayment::purchaseHTML())->display('user/code.tpl');
    }

    public function orderDelete($request, $response, $args)
    {
        return (new ChenPay())->orderDelete($request);
    }

    public function donate($request, $response, $args)
    {
        if ($_ENV['enable_donate'] != true) {
            exit(0);
        }

        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $codes = Code::where(
            static function ($query) {
                $query->where('type', '=', -1)
                    ->orWhere('type', '=', -2);
            }
        )->where('isused', 1)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $codes->setPath('/user/donate');
        return $this->view()->assign('codes', $codes)->assign('total_in', Code::where('isused', 1)->where('type', -1)->sum('number'))->assign('total_out', Code::where('isused', 1)->where('type', -2)->sum('number'))->display('user/donate.tpl');
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

    public function code_check($request, $response, $args)
    {
        $time = $request->getQueryParams()['time'];
        $codes = Code::where('userid', '=', $this->user->id)->where('usedatetime', '>', date('Y-m-d H:i:s', $time))->first();
        if ($codes != null && strpos($codes->code, '充值') !== false) {
            $res['ret'] = 1;
            return $response->getBody()->write(json_encode($res));
        }

        $res['ret'] = 0;
        return $response->getBody()->write(json_encode($res));
    }

    public function f2fpayget($request, $response, $args)
    {
        $time = $request->getQueryParams()['time'];
        $res['ret'] = 1;
        return $response->getBody()->write(json_encode($res));
    }

    public function f2fpay($request, $response, $args)
    {
        $amount = $request->getParam('amount');
        if ($amount == '') {
            $res['ret'] = 0;
            $res['msg'] = '订单金额错误：' . $amount;
            return $response->getBody()->write(json_encode($res));
        }
        $user = $this->user;

        //生成二维码
        $qrPayResult = Pay::alipay_get_qrcode($user, $amount, $qrPay);
        //  根据状态值进行业务处理
        switch ($qrPayResult->getTradeStatus()) {
            case 'SUCCESS':
                $aliresponse = $qrPayResult->getResponse();
                $res['ret'] = 1;
                $res['msg'] = '二维码生成成功';
                $res['amount'] = $amount;
                $res['qrcode'] = $qrPay->create_erweima($aliresponse->qr_code);

                break;
            case 'FAILED':
                $res['ret'] = 0;
                $res['msg'] = '支付宝创建订单二维码失败! 请使用其他方式付款。';

                break;
            case 'UNKNOWN':
                $res['ret'] = 0;
                $res['msg'] = '系统异常，状态未知! 请使用其他方式付款。';

                break;
            default:
                $res['ret'] = 0;
                $res['msg'] = '创建订单二维码返回异常! 请使用其他方式付款。';

                break;
        }

        return $response->getBody()->write(json_encode($res));
    }

    public function alipay($request, $response, $args)
    {
        $amount = $request->getQueryParams()['amount'];
        Pay::getGen($this->user, $amount);
    }

    public function codepost($request, $response, $args)
    {
        $code = $request->getParam('code');
        $code = trim($code);
        $user = $this->user;

        if ($code == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        $codeq = Code::where('code', '=', $code)->where('isused', '=', 0)->first();
        if ($codeq == null) {
            $res['ret'] = 0;
            $res['msg'] = '此充值码错误';
            return $response->getBody()->write(json_encode($res));
        }

        $codeq->isused = 1;
        $codeq->usedatetime = date('Y-m-d H:i:s');
        $codeq->userid = $user->id;
        $codeq->save();

        if ($codeq->type == -1) {
            $user->money += $codeq->number;
            $user->save();

            if ($user->ref_by != '' && $user->ref_by != 0 && $user->ref_by != null) {
                $gift_user = User::where('id', '=', $user->ref_by)->first();
                $gift_user->money += ($codeq->number * ($_ENV['code_payback'] / 100));
                $gift_user->save();

                $Payback = new Payback();
                $Payback->total = $codeq->number;
                $Payback->userid = $this->user->id;
                $Payback->ref_by = $this->user->ref_by;
                $Payback->ref_get = $codeq->number * ($_ENV['code_payback'] / 100);
                $Payback->datetime = time();
                $Payback->save();
            }

            $res['ret'] = 1;
            $res['msg'] = '充值成功，充值的金额为' . $codeq->number . '元。';

            if ($_ENV['enable_donate'] == true) {
                if ($this->user->is_hide == 1) {
                    Telegram::Send('姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 ' . $codeq->number . ' 元呢~');
                } else {
                    Telegram::Send('姐姐姐姐，' . $this->user->user_name . ' 大老爷给我们捐了 ' . $codeq->number . ' 元呢~');
                }
            }

            return $response->getBody()->write(json_encode($res));
        }

        if ($codeq->type == 10001) {
            $user->transfer_enable += $codeq->number * 1024 * 1024 * 1024;
            $user->save();
        }

        if ($codeq->type == 10002) {
            if (time() > strtotime($user->expire_in)) {
                $user->expire_in = date('Y-m-d H:i:s', time() + $codeq->number * 86400);
            } else {
                $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + $codeq->number * 86400);
            }
            $user->save();
        }

        if ($codeq->type >= 1 && $codeq->type <= 10000) {
            if ($user->class == 0 || $user->class != $codeq->type) {
                $user->class_expire = date('Y-m-d H:i:s', time());
                $user->save();
            }
            $user->class_expire = date('Y-m-d H:i:s', strtotime($user->class_expire) + $codeq->number * 86400);
            $user->class = $codeq->type;
            $user->save();
        }
    }

    public function GaCheck($request, $response, $args)
    {
        $code = $request->getParam('code');
        $user = $this->user;


        if ($code == '') {
            $res['ret'] = 0;
            $res['msg'] = '二维码不能为空';
            return $response->getBody()->write(json_encode($res));
        }

        $ga = new GA();
        $rcode = $ga->verifyCode($user->ga_token, $code);
        if (!$rcode) {
            $res['ret'] = 0;
            $res['msg'] = '测试错误';
            return $response->getBody()->write(json_encode($res));
        }


        $res['ret'] = 1;
        $res['msg'] = '测试成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function GaSet($request, $response, $args)
    {
        $enable = $request->getParam('enable');
        $user = $this->user;

        if ($enable == '') {
            $res['ret'] = 0;
            $res['msg'] = '选项无效';
            return $response->getBody()->write(json_encode($res));
        }

        $user->ga_enable = $enable;
        $user->save();


        $res['ret'] = 1;
        $res['msg'] = '设置成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function ResetPort($request, $response, $args)
    {
        $user = $this->user;
        $temp = $user->ResetPort();
        $res['msg'] = $temp['msg'];
        $res['ret'] = ($temp['ok'] === true ? 1 : 0);
        return $response->getBody()->write(json_encode($res));
    }

    public function SpecifyPort($request, $response, $args)
    {
        $user = $this->user;
        $port = $request->getParam('port');
        $temp = $user->SpecifyPort($port);
        $res['msg'] = $temp['msg'];
        $res['ret'] = ($temp['ok'] === true ? 1 : 0);
        return $response->getBody()->write(json_encode($res));
    }

    public function GaReset($request, $response, $args)
    {
        $user = $this->user;
        $ga = new GA();
        $secret = $ga->createSecret();

        $user->ga_token = $secret;
        $user->save();
        return $response->withStatus(302)->withHeader('Location', '/user/edit');
    }

    public function profile($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $paybacks = Payback::where('ref_by', $this->user->id)->orderBy('datetime', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $paybacks->setPath('/user/profile');

        $iplocation = new QQWry();

        $userip = array();

        $total = Ip::where('datetime', '>=', time() - 300)->where('userid', '=', $this->user->id)->get();

        $totallogin = LoginIp::where('userid', '=', $this->user->id)->where('type', '=', 0)->orderBy('datetime', 'desc')->take(10)->get();

        $userloginip = array();

        foreach ($totallogin as $single) {
            //if(isset($useripcount[$single->userid]))
            {
                if (!isset($userloginip[$single->ip])) {
                    //$useripcount[$single->userid]=$useripcount[$single->userid]+1;
                    $location = $iplocation->getlocation($single->ip);
                    $userloginip[$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
                }
            }
        }

        foreach ($total as $single) {
            //if(isset($useripcount[$single->userid]))
            {
                $single->ip = Tools::getRealIp($single->ip);
                $is_node = Node::where('node_ip', $single->ip)->first();
                if ($is_node) {
                    continue;
                }


                if (!isset($userip[$single->ip])) {
                    //$useripcount[$single->userid]=$useripcount[$single->userid]+1;
                    $location = $iplocation->getlocation($single->ip);
                    $userip[$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
                }
            }
        }

        $boughts = Bought::where('userid', $this->user->id)->orderBy('id', 'desc')->get();

        if ($request->getParam('json') == 1) {
            $res['userip']      = $userip;
            $res['userloginip'] = $userloginip;
            $res['paybacks']    = $paybacks;
            $res['ret']         = 1;
            return $response->getBody()->write(json_encode($res));
        };

        return $this->view()->assign('boughts', $boughts)->assign('userip', $userip)->assign('userloginip', $userloginip)->assign('paybacks', $paybacks)->display('user/profile.tpl');
    }

    public function announcement($request, $response, $args)
    {
        $Anns = Ann::orderBy('date', 'desc')->get();

        if ($request->getParam('json') == 1) {
            $res['Anns']      = $Anns;
            $res['ret']         = 1;
            return $this->echoJson($response, $res);
        };

        return $this->view()->assign('anns', $Anns)->display('user/announcement.tpl');
    }

    public function tutorial($request, $response, $args)
    {
        return $this->view()->display('user/tutorial.tpl');
    }

    public function edit($request, $response, $args)
    {
        $themes = Tools::getDir(BASE_PATH . '/resources/views');

        $BIP = BlockIp::where('ip', $_SERVER['REMOTE_ADDR'])->first();
        if ($BIP == null) {
            $Block = 'IP: ' . $_SERVER['REMOTE_ADDR'] . ' 没有被封';
            $isBlock = 0;
        } else {
            $Block = 'IP: ' . $_SERVER['REMOTE_ADDR'] . ' 已被封';
            $isBlock = 1;
        }

        $bind_token = TelegramSessionManager::add_bind_session($this->user);

        $config_service = new Config();

        return $this->view()
            ->assign('user', $this->user)
            ->assign('themes', $themes)
            ->assign('isBlock', $isBlock)
            ->assign('Block', $Block)
            ->assign('bind_token', $bind_token)
            ->assign('telegram_bot', $_ENV['telegram_bot'])
            ->assign('config_service', $config_service)
            ->registerClass('URL', URL::class)
            ->display('user/edit.tpl');
    }

    public function invite($request, $response, $args)
    {
        $code = InviteCode::where('user_id', $this->user->id)->first();
        if ($code == null) {
            $this->user->addInviteCode();
            $code = InviteCode::where('user_id', $this->user->id)->first();
        }

        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $paybacks = Payback::where('ref_by', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        if (!$paybacks_sum = Payback::where('ref_by', $this->user->id)->sum('ref_get')) {
            $paybacks_sum = 0;
        }
        $paybacks->setPath('/user/invite');

        return $this->view()->assign('code', $code)->assign('paybacks', $paybacks)->assign('paybacks_sum', $paybacks_sum)->display('user/invite.tpl');
    }

    public function buyInvite($request, $response, $args)
    {
        $price = $_ENV['invite_price'];
        $num = $request->getParam('num');
        $num = trim($num);

        if (!Tools::isInt($num) || $price < 0 || $num <= 0) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        $amount = $price * $num;

        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        if ($user->money < $amount) {
            $res['ret'] = 0;
            $res['msg'] = '余额不足，总价为' . $amount . '元。';
            return $response->getBody()->write(json_encode($res));
        }
        $user->invite_num += $num;
        $user->money -= $amount;
        $user->save();
        $res['invite_num'] = $user->invite_num;
        $res['ret'] = 1;
        $res['msg'] = '邀请次数添加成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function customInvite($request, $response, $args)
    {
        $price = $_ENV['custom_invite_price'];
        $customcode = $request->getParam('customcode');
        $customcode = trim($customcode);

        if (!Tools::is_validate($customcode) || $price < 0 || $customcode == '' || strlen($customcode) > 32) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求,邀请链接后缀不能包含特殊符号且长度不能大于32字符';
            return $response->getBody()->write(json_encode($res));
        }

        if (InviteCode::where('code', $customcode)->count() != 0) {
            $res['ret'] = 0;
            $res['msg'] = '此后缀名被抢注了';
            return $response->getBody()->write(json_encode($res));
        }

        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        if ($user->money < $price) {
            $res['ret'] = 0;
            $res['msg'] = '余额不足，总价为' . $price . '元。';
            return $response->getBody()->write(json_encode($res));
        }
        $code = InviteCode::where('user_id', $user->id)->first();
        $code->code = $customcode;
        $user->money -= $price;
        $user->save();
        $code->save();
        $res['ret'] = 1;
        $res['msg'] = '定制成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function sys()
    {
        return $this->view()->assign('ana', '')->display('user/sys.tpl');
    }

    public function updatePassword($request, $response, $args)
    {
        $oldpwd = $request->getParam('oldpwd');
        $pwd = $request->getParam('pwd');
        $repwd = $request->getParam('repwd');
        $user = $this->user;
        if (!Hash::checkPassword($user->pass, $oldpwd)) {
            $res['ret'] = 0;
            $res['msg'] = '旧密码错误';
            return $response->getBody()->write(json_encode($res));
        }
        if ($pwd != $repwd) {
            $res['ret'] = 0;
            $res['msg'] = '两次输入不符合';
            return $response->getBody()->write(json_encode($res));
        }

        if (strlen($pwd) < 8) {
            $res['ret'] = 0;
            $res['msg'] = '密码太短啦';
            return $response->getBody()->write(json_encode($res));
        }
        $hashPwd = Hash::passwordHash($pwd);
        $user->pass = $hashPwd;
        $user->save();

        $user->clean_link();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function updateEmail($request, $response, $args)
    {
        $user = $this->user;
        $newemail = $request->getParam('newemail');
        $oldemail = $user->email;
        $otheruser = User::where('email', $newemail)->first();
        if ($_ENV['enable_telegram'] !== true) {
            $res['ret'] = 0;
            $res['msg'] = '未啓用用戶自行修改郵箱功能';
            return $response->getBody()->write(json_encode($res));
        }
        if (Config::getconfig('Register.bool.Enable_email_verify')) {
            $emailcode = $request->getParam('emailcode');
            $mailcount = EmailVerify::where('email', '=', $newemail)->where('code', '=', $emailcode)->where('expire_in', '>', time())->first();
            if ($mailcount == null) {
                $res['ret'] = 0;
                $res['msg'] = '您的邮箱验证码不正确';
                return $response->getBody()->write(json_encode($res));
            }
        }
        if ($newemail == '') {
            $res['ret'] = 0;
            $res['msg'] = '未填写邮箱';
            return $response->getBody()->write(json_encode($res));
        }
        if (!Check::isEmailLegal($newemail)) {
            $res['ret'] = 0;
            $res['msg'] = '邮箱无效';
            return $response->getBody()->write(json_encode($res));
        }
        if ($otheruser != null) {
            $res['ret'] = 0;
            $res['msg'] = '邮箱已经被使用了';
            return $response->getBody()->write(json_encode($res));
        }
        if ($newemail == $oldemail) {
            $res['ret'] = 0;
            $res['msg'] = '新邮箱不能和旧邮箱一样';
            return $response->getBody()->write(json_encode($res));
        }
        $antiXss = new AntiXSS();
        $user->email = $antiXss->xss_clean($newemail);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function updateUsername($request, $response, $args)
    {
        $newusername = $request->getParam('newusername');
        $user = $this->user;
        $antiXss = new AntiXSS();
        $user->user_name = $antiXss->xss_clean($newusername);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function updateHide($request, $response, $args)
    {
        $hide = $request->getParam('hide');
        $user = $this->user;
        $user->is_hide = $hide;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function Unblock($request, $response, $args)
    {
        $user = $this->user;
        $BIP = BlockIp::where('ip', $_SERVER['REMOTE_ADDR'])->get();
        foreach ($BIP as $bi) {
            $bi->delete();
        }

        $UIP = new UnblockIp();
        $UIP->userid = $user->id;
        $UIP->ip = $_SERVER['REMOTE_ADDR'];
        $UIP->datetime = time();
        $UIP->save();

        $res['ret'] = 1;
        $res['msg'] = $_SERVER['REMOTE_ADDR'];
        return $this->echoJson($response, $res);
    }

    public function shop($request, $response, $args)
    {
        $shops = Shop::where('status', 1)->orderBy('name')->get();
        return $this->view()->assign('shops', $shops)->display('user/shop.tpl');
    }

    public function CouponCheck($request, $response, $args)
    {
        $coupon = $request->getParam('coupon');
        $coupon = trim($coupon);

        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $shop = $request->getParam('shop');

        $shop = Shop::where('id', $shop)->where('status', 1)->first();

        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        if ($coupon == '') {
            $res['ret'] = 1;
            $res['name'] = $shop->name;
            $res['credit'] = '0 %';
            $res['total'] = $shop->price . '元';
            return $response->getBody()->write(json_encode($res));
        }

        $coupon = Coupon::where('code', $coupon)->first();

        if ($coupon == null) {
            $res['ret'] = 0;
            $res['msg'] = '优惠码无效';
            return $response->getBody()->write(json_encode($res));
        }

        if ($coupon->order($shop->id) == false) {
            $res['ret'] = 0;
            $res['msg'] = '此优惠码不可用于此商品';
            return $response->getBody()->write(json_encode($res));
        }

        $use_limit = $coupon->onetime;
        if ($use_limit > 0) {
            $use_count = Bought::where('userid', $user->id)->where('coupon', $coupon->code)->count();
            if ($use_count >= $use_limit) {
                $res['ret'] = 0;
                $res['msg'] = '优惠码次数已用完';
                return $response->getBody()->write(json_encode($res));
            }
        }

        $res['ret'] = 1;
        $res['name'] = $shop->name;
        $res['credit'] = $coupon->credit . ' %';
        $res['total'] = $shop->price * ((100 - $coupon->credit) / 100) . '元';

        return $response->getBody()->write(json_encode($res));
    }

    public function buy_traffic_package ($request, $response, $args)
    {
        $user = $this->user;
        $shop = $request->getParam('shop');
        $shop = Shop::where('id', $shop)->where('status', 1)->first();
        $price = $shop->price;

        if ($shop == null || $shop->traffic_package() == 0) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        $content = json_decode($shop->content);

        if ($user->class < $content->traffic_package->class->min || $user->class > $content->traffic_package->class->max) {
            $res['ret'] = 0;
            $res['msg'] = '您当前的会员等级无法购买此流量包';
            return $response->getBody()->write(json_encode($res));
        }

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        if (bccomp($user->money, $price, 2) == -1) {
            $res['ret'] = 0;
            $res['msg'] = '喵喵喵~ 当前余额不足，总价为' . $price . '元。</br><a href="/user/code">点击进入充值界面</a>';
            return $response->getBody()->write(json_encode($res));
        }

        $user->money = bcsub($user->money, $price, 2);
        $user->save();

        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        $bought->renew = 0;
        $bought->coupon = 0;
        $bought->price = $price;
        $bought->save();

        $shop->buy($user);

        $res['ret'] = 1;
        $res['msg'] = '购买成功';

        return $response->getBody()->write(json_encode($res));
    }

    public function buy($request, $response, $args)
    {
        $coupon = $request->getParam('coupon');
        $coupon = trim($coupon);
        $code = $coupon;
        $shop = $request->getParam('shop');
        $disableothers = $request->getParam('disableothers');
        $autorenew = $request->getParam('autorenew');

        $shop = Shop::where('id', $shop)->where('status', 1)->first();

        $orders = Bought::where('userid', $this->user->id)->get();
        foreach ($orders as $order)
        {
            $shop_item = Shop::where('id',$order['shopid'])->first();
            $shop_item = json_decode($shop_item['content']);
            $shop_item->datetime = $order['datetime'];
            if (property_exists($shop_item,'reset') || property_exists($shop_item,'reset_value') || property_exists($shop_item,'reset_exp'))
            {
                if (time() < ($shop_item->datetime + $shop_item->reset_exp * 86400) ) {
                    $res['ret'] = 0;
                    $res['msg'] = '您购买的含有自动重置系统的套餐还未过期，无法购买新套餐';
                    return $response->getBody()->write(json_encode($res));
                }
            }
        };

        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        if ($coupon == '') {
            $credit = 0;
        } else {
            $coupon = Coupon::where('code', $coupon)->first();

            if ($coupon == null) {
                $credit = 0;
            } else {
                if ($coupon->onetime == 1) {
                    $onetime = true;
                }

                $credit = $coupon->credit;
            }

            if ($coupon->order($shop->id) == false) {
                $res['ret'] = 0;
                $res['msg'] = '此优惠码不可用于此商品';
                return $response->getBody()->write(json_encode($res));
            }

            if ($coupon->expire < time()) {
                $res['ret'] = 0;
                $res['msg'] = '此优惠码已过期';
                return $response->getBody()->write(json_encode($res));
            }

            $use_limit = $coupon->onetime;
            if ($use_limit > 0) {
                $use_count = Bought::where('userid', $user->id)->where('coupon', $coupon->code)->count();
                if ($use_count >= $use_limit) {
                    $res['ret'] = 0;
                    $res['msg'] = '优惠码次数已用完';
                    return $response->getBody()->write(json_encode($res));
                }
            }
        }

        $price = $shop->price * ((100 - $credit) / 100);
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        if (bccomp($user->money, $price, 2) == -1) {
            $res['ret'] = 0;
            $res['msg'] = '喵喵喵~ 当前余额不足，总价为' . $price . '元。</br><a href="/user/code">点击进入充值界面</a>';
            return $response->getBody()->write(json_encode($res));
        }

        $user->money = bcsub($user->money, $price, 2);
        $user->save();

        if ($disableothers == 1) {
            $boughts = Bought::where('userid', $user->id)->get();
            foreach ($boughts as $disable_bought) {
                $disable_bought->renew = 0;
                $disable_bought->save();
            }
        }

        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        if ($autorenew == 0 || $shop->auto_renew == 0) {
            $bought->renew = 0;
        } else {
            $bought->renew = time() + $shop->auto_renew * 86400;
        }

        $bought->coupon = $code;


        if (isset($onetime)) {
            $price = $shop->price;
        }
        $bought->price = $price;
        $bought->save();

        $shop->buy($user);

        $res['ret'] = 1;
        $res['msg'] = '购买成功';

        return $response->getBody()->write(json_encode($res));
    }

    public function bought($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $shops = Bought::where('userid', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $shops->setPath('/user/bought');
        if ($request->getParam('json') == 1) {
            $res['ret'] = 1;
            foreach ($shops as $shop) {
                $shop->datetime = $shop->datetime();
                $shop->name = $shop->shop()->name;
                $shop->content = $shop->shop()->content();
            };
            $res['shops'] = $shops;
            return $response->getBody()->write(json_encode($res));
        };
        return $this->view()->assign('shops', $shops)->display('user/bought.tpl');
    }

    public function deleteBoughtGet($request, $response, $args)
    {
        $id = $request->getParam('id');
        $shop = Bought::where('id', $id)->where('userid', $this->user->id)->first();

        if ($shop == null) {
            $rs['ret'] = 0;
            $rs['msg'] = '关闭自动续费失败，订单不存在。';
            return $response->getBody()->write(json_encode($rs));
        }

        if ($this->user->id == $shop->userid) {
            $shop->renew = 0;
        }

        if (!$shop->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '关闭自动续费失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '关闭自动续费成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function updateWechat($request, $response, $args)
    {
        $type = $request->getParam('imtype');
        $wechat = $request->getParam('wechat');
        $wechat = trim($wechat);

        $user = $this->user;

        if ($user->telegram_id != 0) {
            $res['ret'] = 0;
            $res['msg'] = '您绑定了 Telegram ，所以此项并不能被修改。';
            return $response->getBody()->write(json_encode($res));
        }

        if ($wechat == '' || $type == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        $user1 = User::where('im_value', $wechat)->where('im_type', $type)->first();
        if ($user1 != null) {
            $res['ret'] = 0;
            $res['msg'] = '此联络方式已经被注册';
            return $response->getBody()->write(json_encode($res));
        }

        $user->im_type = $type;
        $antiXss = new AntiXSS();
        $user->im_value = $antiXss->xss_clean($wechat);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function updateSSR($request, $response, $args)
    {
        $protocol = $request->getParam('protocol');
        $obfs = $request->getParam('obfs');
        $obfs_param = $request->getParam('obfs_param');
        $obfs_param = trim($obfs_param);

        $user = $this->user;

        if ($obfs == '' || $protocol == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('obfs', $obfs)) {
            $res['ret'] = 0;
            $res['msg'] = '混淆无效';
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('protocol', $protocol)) {
            $res['ret'] = 0;
            $res['msg'] = '协议无效';
            return $response->getBody()->write(json_encode($res));
        }

        $antiXss = new AntiXSS();

        $user->protocol = $antiXss->xss_clean($protocol);
        $user->obfs = $antiXss->xss_clean($obfs);
        $user->obfs_param = $antiXss->xss_clean($obfs_param);

        if (!Tools::checkNoneProtocol($user)) {
            $res['ret'] = 0;
            $res['msg'] = '系统检测到您目前的加密方式为 none ，但您将要设置为的协议并不在以下协议<br>' . implode(',', Config::getSupportParam('allow_none_protocol')) . '<br>之内，请您先修改您的加密方式，再来修改此处设置。';
            return $this->echoJson($response, $res);
        }

        if (!URL::SSCanConnect($user) && !URL::SSRCanConnect($user)) {
            $res['ret'] = 0;
            $res['msg'] = '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。';
            return $this->echoJson($response, $res);
        }

        $user->save();

        if (!URL::SSCanConnect($user)) {
            $res['ret'] = 1;
            $res['msg'] = '设置成功，但您目前的协议，混淆，加密方式设置会导致 Shadowsocks原版客户端无法连接，请您自行更换到 ShadowsocksR 客户端。';
            return $this->echoJson($response, $res);
        }

        if (!URL::SSRCanConnect($user)) {
            $res['ret'] = 1;
            $res['msg'] = '设置成功，但您目前的协议，混淆，加密方式设置会导致 ShadowsocksR 客户端无法连接，请您自行更换到 Shadowsocks 客户端。';
            return $this->echoJson($response, $res);
        }

        $res['ret'] = 1;
        $res['msg'] = '设置成功，您可自由选用客户端来连接。';
        return $this->echoJson($response, $res);
    }

    public function updateTheme($request, $response, $args)
    {
        $theme = $request->getParam('theme');

        $user = $this->user;

        if ($theme == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        $user->theme = filter_var($theme, FILTER_SANITIZE_STRING);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '设置成功';
        return $this->echoJson($response, $res);
    }

    public function updateMail($request, $response, $args)
    {
        $value = (int) $request->getParam('mail');
        if (in_array($value, [0, 1, 2])) {
            $user = $this->user;
            if ($value == 2 && $_ENV['enable_telegram'] === false) {
                $res['ret'] = 0;
                $res['msg'] = '修改失败，当前无法使用 Telegram 接收每日报告';
                return $this->echoJson($response, $res);
            }
            $user->sendDailyMail = $value;
            $user->save();
            $res['ret'] = 1;
            $res['msg'] = '修改成功';
        } else {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
        }
        return $this->echoJson($response, $res);
    }

    public function PacSet($request, $response, $args)
    {
        $pac = $request->getParam('pac');

        $user = $this->user;

        if ($pac == '') {
            $res['ret'] = 0;
            $res['msg'] = '输入不能为空';
            return $response->getBody()->write(json_encode($res));
        }

        $user->pac = $pac;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function updateSsPwd($request, $response, $args)
    {
        $user = Auth::getUser();
        $pwd = $request->getParam('sspwd');
        $pwd = trim($pwd);

        if ($pwd == '') {
            $res['ret'] = 0;
            $res['msg'] = '密码不能为空';
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_validate($pwd)) {
            $res['ret'] = 0;
            $res['msg'] = '密码无效';
            return $response->getBody()->write(json_encode($res));
        }

        $user->updateSsPwd($pwd);
        $res['ret'] = 1;

        Radius::Add($user, $pwd);

        return $this->echoJson($response, $res);
    }

    public function updateMethod($request, $response, $args)
    {
        $user = Auth::getUser();
        $method = $request->getParam('method');
        $method = strtolower($method);

        if ($method == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('method', $method)) {
            $res['ret'] = 0;
            $res['msg'] = '加密无效';
            return $response->getBody()->write(json_encode($res));
        }

        $user->method = $method;

        if (!Tools::checkNoneProtocol($user)) {
            $res['ret'] = 0;
            $res['msg'] = '系统检测到您将要设置的加密方式为 none ，但您的协议并不在以下协议<br>' . implode(',', Config::getSupportParam('allow_none_protocol')) . '<br>之内，请您先修改您的协议，再来修改此处设置。';
            return $this->echoJson($response, $res);
        }

        if (!URL::SSCanConnect($user) && !URL::SSRCanConnect($user)) {
            $res['ret'] = 0;
            $res['msg'] = '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。';
            return $this->echoJson($response, $res);
        }

        $user->updateMethod($method);

        if (!URL::SSCanConnect($user)) {
            $res['ret'] = 1;
            $res['msg'] = '设置成功，但您目前的协议，混淆，加密方式设置会导致 Shadowsocks原版客户端无法连接，请您自行更换到 ShadowsocksR 客户端。';
            return $this->echoJson($response, $res);
        }

        if (!URL::SSRCanConnect($user)) {
            $res['ret'] = 1;
            $res['msg'] = '设置成功，但您目前的协议，混淆，加密方式设置会导致 ShadowsocksR 客户端无法连接，请您自行更换到 Shadowsocks 客户端。';
            return $this->echoJson($response, $res);
        }

        $res['ret'] = 1;
        $res['msg'] = '设置成功，您可自由选用两种客户端来进行连接。';
        return $this->echoJson($response, $res);
    }

    public function logout($request, $response, $args)
    {
        Auth::logout();
        return $response->withStatus(302)->withHeader('Location', '/');
    }

    public function doCheckIn($request, $response, $args)
    {
        if ($_ENV['enable_checkin_captcha'] == true) {
            switch ($_ENV['captcha_provider']) {
                case 'recaptcha':
                    $recaptcha = $request->getParam('recaptcha');
                    if ($recaptcha == '') {
                        $ret = false;
                    } else {
                        $json = file_get_contents('https://recaptcha.net/recaptcha/api/siteverify?secret=' . $_ENV['recaptcha_secret'] . '&response=' . $recaptcha);
                        $ret = json_decode($json)->success;
                    }
                    break;
                case 'geetest':
                    $ret = Geetest::verify($request->getParam('geetest_challenge'), $request->getParam('geetest_validate'), $request->getParam('geetest_seccode'));
                    break;
            }
            if (!$ret) {
                $res['ret'] = 0;
                $res['msg'] = '系统无法接受您的验证结果，请刷新页面后重试。';
                return $response->getBody()->write(json_encode($res));
            }
        }

        if (strtotime($this->user->expire_in) < time()) {
            $res['ret'] = 0;
            $res['msg'] = '您的账户已过期，无法签到。';
            return $response->getBody()->write(json_encode($res));
        }

        $checkin = $this->user->checkin();
        if ($checkin['ok'] === false) {
            $res['ret'] = 0;
            $res['msg'] = $checkin['msg'];
            return $this->echoJson($response, $res);
        }

        $res['msg'] = $checkin['msg'];
        $res['unflowtraffic'] = $this->user->transfer_enable;
        $res['traffic'] = Tools::flowAutoShow($this->user->transfer_enable);
        $res['trafficInfo'] = array(
            'todayUsedTraffic' => $this->user->TodayusedTraffic(),
            'lastUsedTraffic' => $this->user->LastusedTraffic(),
            'unUsedTraffic' => $this->user->unusedTraffic(),
        );
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }

    public function kill($request, $response, $args)
    {
        return $this->view()->display('user/kill.tpl');
    }

    public function handleKill($request, $response, $args)
    {
        $user = Auth::getUser();

        $email = $user->email;

        $passwd = $request->getParam('passwd');
        // check passwd
        $res = array();
        if (!Hash::checkPassword($user->pass, $passwd)) {
            $res['ret'] = 0;
            $res['msg'] = ' 密码错误';
            return $this->echoJson($response, $res);
        }

        if ($_ENV['enable_kill'] == true) {
            Auth::logout();
            $user->kill_user();
            $res['ret'] = 1;
            $res['msg'] = '您的帐号已经从我们的系统中删除。欢迎下次光临!';
        } else {
            $res['ret'] = 0;
            $res['msg'] = '管理员不允许删除，如需删除请联系管理员。';
        }
        return $this->echoJson($response, $res);
    }

    public function trafficLog($request, $response, $args)
    {
        $traffic = TrafficLog::where('user_id', $this->user->id)->where('log_time', '>', time() - 3 * 86400)->orderBy('id', 'desc')->get();

        if ($request->getParam('json') == 1) {
            $res['ret'] = 1;
            foreach ($traffic as $trafficdata) {
                $trafficdata->total_used = $trafficdata->totalUsedRaw();
                $trafficdata->name = $trafficdata->node()->name;
            }
            $res['traffic'] = $traffic;

            return $this->echoJson($response, $res);
        }

        return $this->view()->assign('logs', $traffic)->display('user/trafficlog.tpl');
    }

    public function detect_index($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $logs = DetectRule::paginate(15, ['*'], 'page', $pageNum);

        if ($request->getParam('json') == 1) {
            $res['ret'] = 1;
            $res['logs'] = $logs;
            return $this->echoJson($response, $res);
        }

        $logs->setPath('/user/detect');
        return $this->view()->assign('rules', $logs)->display('user/detect_index.tpl');
    }

    public function detect_log($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $logs = DetectLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->paginate(15, ['*'], 'page', $pageNum);

        if ($request->getParam('json') == 1) {
            $res['ret'] = 1;
            foreach ($logs as $log) {
                $log->node_name = $log->Node()->name;
                $log->detect_rule_name = $log->DetectRule()->name;
                $log->detect_rule_text = $log->DetectRule()->text;
                $log->detect_rule_regex = $log->DetectRule()->regex;
                $log->detect_rule_type = $log->DetectRule()->type;
                $log->detect_rule_date = date('Y-m-d H:i:s',$log->datetime);
            }
            $res['logs'] = $logs;
            return $this->echoJson($response, $res);
        }

        $logs->setPath('/user/detect/log');
        return $this->view()->assign('logs', $logs)->display('user/detect_log.tpl');
    }

    public function disable($request, $response, $args)
    {
        return $this->view()->display('user/disable.tpl');
    }

    public function telegram_reset($request, $response, $args)
    {
        $user = $this->user;
        $user->TelegramReset();
        return $response->withStatus(302)->withHeader('Location', '/user/edit');
    }

    public function resetURL($request, $response, $args)
    {
        $user = $this->user;
        $user->clean_link();
        return $response->withStatus(302)->withHeader('Location', '/user');
    }

    public function resetInviteURL($request, $response, $args)
    {
        $user = $this->user;
        $user->clear_inviteCodes();
        return $response->withStatus(302)->withHeader('Location', '/user/invite');
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

    /**
     * 订阅记录
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     */
    public function subscribe_log($request, $response, $args)
    {
        if ($_ENV['subscribeLog_show'] === false) {
            return $response->withStatus(302)->withHeader('Location', '/user');
        }
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $logs = UserSubscribeLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->paginate(15, ['*'], 'page', $pageNum);
        $iplocation = new QQWry();
        $logs->setPath('/user/subscribe_log');

        if (($request->getParam('json') == 1)) {
            $res['ret'] = 1;
            $res['logs'] = $logs;
            foreach ($logs as $log) {
                $location = $iplocation->getlocation($log->request_ip);
                $log->country = iconv("gbk", "utf-8//IGNORE", $location['country']);
                $log->area = iconv("gbk", "utf-8//IGNORE", $location['area']);
            }
            $res['subscribeLog_keep_days'] = $_ENV['subscribeLog_keep_days'];
            return $this->echoJson($response, $res);
        }

        return $this->view()->assign('logs', $logs)->assign('iplocation', $iplocation)->fetch('user/subscribe_log.tpl');
    }

    /**
     * 获取包含订阅信息的客户端压缩档
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     */
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

    /**
     * 从使用同数据库的其他面板下载客户端[内置节点]
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     */
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
