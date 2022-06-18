<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ann;
use App\Models\BlockIp;
use App\Models\Bought;
use App\Models\Code;
use App\Models\EmailVerify;
use App\Models\InviteCode;
use App\Models\Ip;
use App\Models\LoginIp;
use App\Models\Node;
use App\Models\Payback;
use App\Models\Setting;
use App\Models\StreamMedia;
use App\Models\Token;
use App\Models\UnblockIp;
use App\Models\User;
use App\Models\UserSubscribeLog;
use App\Services\Auth;
use App\Services\Captcha;
use App\Services\Config;
use App\Services\Payment;
use App\Utils\Check;
use App\Utils\ClientProfiles;
use App\Utils\Cookie;
use App\Utils\DatatablesHelper;
use App\Utils\GA;
use App\Utils\Hash;
use App\Utils\QQWry;
use App\Utils\ResponseHelper;
use App\Utils\Telegram;
use App\Utils\TelegramSessionManager;
use App\Utils\Tools;
use App\Utils\URL;
use Ramsey\Uuid\Uuid;
use Slim\Http\Request;
use Slim\Http\Response;
use voku\helper\AntiXSS;

/**
 *  HomeController
 */
final class UserController extends BaseController
{
    /**
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args)
    {
        $captcha = Captcha::generate();

        if ($_ENV['subscribe_client_url'] !== '') {
            $getClient = new Token();
            for ($i = 0; $i < 10; $i++) {
                $token = $this->user->id . Tools::genRandomChar(16);
                $Elink = Token::where('token', '=', $token)->first();
                if ($Elink === null) {
                    $getClient->token = $token;
                    break;
                }
            }
            $getClient->user_id = $this->user->id;
            $getClient->create_time = time();
            $getClient->expire_time = time() + 10 * 60;
            $getClient->save();
        } else {
            $token = '';
        }

        if (Setting::obtain('enable_checkin_captcha') === true) {
            $geetest_html = $captcha['geetest'];
        } else {
            $geetest_html = null;
        }

        $data = [
            'today_traffic_usage' => (int) $this->user->transfer_enable === 0 ? 0 : ($this->user->u + $this->user->d - $this->user->last_day_t) / $this->user->transfer_enable * 100,
            'past_traffic_usage' => (int) $this->user->transfer_enable === 0 ? 0 : $this->user->last_day_t / $this->user->transfer_enable * 100,
            'residual_flow' => (int) $this->user->transfer_enable === 0 ? 0 : ($this->user->transfer_enable - ($this->user->u + $this->user->d)) / $this->user->transfer_enable * 100,
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

    /**
     * @param array     $args
     */
    public function code(Request $request, Response $response, array $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $codes = Code::where('type', '<>', '-2')
            ->where('userid', '=', $this->user->id)
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page', $pageNum);

        $render = Tools::paginateRender($codes);

        return $response->write(
            $this->view()
                ->assign('codes', $codes)
                ->assign('payments', Payment::getPaymentsEnabled())
                // ->assign('pmw', Payment::purchaseHTML())
                ->assign('render', $render)
                ->display('user/code.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function donate(Request $request, Response $response, array $args)
    {
        if ($_ENV['enable_donate'] !== true) {
            exit(0);
        }

        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $codes = Code::where(
            static function ($query): void {
                $query->where('type', '=', -1)
                    ->orWhere('type', '=', -2);
            }
        )->where('isused', 1)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $render = Tools::paginateRender($codes);
        return $response->write(
            $this->view()
                ->assign('codes', $codes)
                ->assign('total_in', Code::where('isused', 1)->where('type', -1)->sum('number'))
                ->assign('total_out', Code::where('isused', 1)->where('type', -2)->sum('number'))
                ->assign('render', $render)
                ->display('user/donate.tpl')
        );
    }

    public function isHTTPS()
    {
        define('HTTPS', false);
        if (defined('HTTPS') && HTTPS) {
            return true;
        }
        if (! isset($_SERVER)) {
            return false;
        }
        if (! isset($_SERVER['HTTPS'])) {
            return false;
        }
        if ($_SERVER['HTTPS'] === 1) {  //Apache
            return true;
        }

        if ($_SERVER['HTTPS'] === 'on') { //IIS
            return true;
        }

        if ($_SERVER['SERVER_PORT'] === 443) { //其他
            return true;
        }
        return false;
    }

    /**
     * @param array     $args
     */
    public function codeCheck(Request $request, Response $response, array $args)
    {
        $time = $request->getQueryParams()['time'];
        $codes = Code::where('userid', '=', $this->user->id)
            ->where('usedatetime', '>', date('Y-m-d H:i:s', $time))
            ->first();

        if ($codes !== null && strpos($codes->code, '充值') !== false) {
            return $response->withJson([
                'ret' => 1,
            ]);
        }
        return $response->withJson([
            'ret' => 0,
        ]);
    }

    /**
     * @param array     $args
     */
    public function codePost(Request $request, Response $response, array $args)
    {
        $code = trim($request->getParam('code'));
        if ($code === '') {
            return ResponseHelper::error($response, '请填写充值码');
        }

        $codeq = Code::where('code', $code)->where('isused', 0)->first();
        if ($codeq === null) {
            return ResponseHelper::error($response, '没有这个充值码');
        }

        $user = $this->user;
        $codeq->isused = 1;
        $codeq->usedatetime = date('Y-m-d H:i:s');
        $codeq->userid = $user->id;
        $codeq->save();

        if ($codeq->type === -1) {
            $user->money += $codeq->number;
            $user->save();

            // 返利
            if ($user->ref_by > 0 && Setting::obtain('invitation_mode') === 'after_recharge') {
                Payback::rebate($user->id, $codeq->number);
            }

            if ($_ENV['enable_donate']) {
                if ($this->user->is_hide === 1) {
                    Telegram::send('姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 ' . $codeq->number . ' 元呢~');
                } else {
                    Telegram::send('姐姐姐姐，' . $this->user->user_name . ' 大老爷给我们捐了 ' . $codeq->number . ' 元呢~');
                }
            }

            return $response->withJson([
                'ret' => 1,
                'msg' => '兑换成功，金额为 ' . $codeq->number . ' 元',
            ]);
        }

        if ($codeq->type === 10001) {
            $user->transfer_enable += $codeq->number * 1024 * 1024 * 1024;
            $user->save();
        }

        if ($codeq->type === 10002) {
            if (time() > strtotime($user->expire_in)) {
                $user->expire_in = date('Y-m-d H:i:s', time() + $codeq->number * 86400);
            } else {
                $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + $codeq->number * 86400);
            }
            $user->save();
        }

        if ($codeq->type >= 1 && $codeq->type <= 10000) {
            if ($user->class === 0 || $user->class !== $codeq->type) {
                $user->class_expire = date('Y-m-d H:i:s', time());
                $user->save();
            }
            $user->class_expire = date('Y-m-d H:i:s', strtotime($user->class_expire) + $codeq->number * 86400);
            $user->class = $codeq->type;
            $user->save();
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '',
        ]);
    }

    /**
     * @param array     $args
     */
    public function gaCheck(Request $request, Response $response, array $args)
    {
        $code = $request->getParam('code');
        if ($code === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '二维码不能为空',
            ]);
        }
        $user = $this->user;
        $ga = new GA();
        $rcode = $ga->verifyCode($user->ga_token, $code);
        if (! $rcode) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '测试错误',
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '测试成功',
        ]);
    }

    /**
     * @param array     $args
     */
    public function gaSet(Request $request, Response $response, array $args)
    {
        $enable = $request->getParam('enable');
        if ($enable === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '选项无效',
            ]);
        }
        $user = $this->user;
        $user->ga_enable = $enable;
        $user->save();
        return $response->withJson([
            'ret' => 1,
            'msg' => '设置成功',
        ]);
    }

    /**
     * @param array     $args
     */
    public function resetPort(Request $request, Response $response, array $args)
    {
        $temp = $this->user->resetPort();
        return $response->withJson([
            'ret' => ($temp['ok'] === true ? 1 : 0),
            'msg' => $temp['msg'],
        ]);
    }

    /**
     * @param array     $args
     */
    public function specifyPort(Request $request, Response $response, array $args)
    {
        $temp = $this->user->specifyPort((int) $request->getParam('port'));
        return $response->withJson([
            'ret' => ($temp['ok'] === true ? 1 : 0),
            'msg' => $temp['msg'],
        ]);
    }

    /**
     * @param array     $args
     */
    public function gaReset(Request $request, Response $response, array $args)
    {
        $ga = new GA();
        $secret = $ga->createSecret();
        $user = $this->user;
        $user->ga_token = $secret;
        $user->save();
        return $response->withStatus(302)->withHeader('Location', '/user/edit');
    }

    /**
     * @param array     $args
     */
    public function profile(Request $request, Response $response, array $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $paybacks = Payback::where('ref_by', $this->user->id)
            ->orderBy('datetime', 'desc')
            ->paginate(15, ['*'], 'page', $pageNum);

        // 登录IP
        $totallogin = LoginIp::where('userid', '=', $this->user->id)->where('type', '=', 0)->orderBy('datetime', 'desc')->take(10)->get();

        // 使用IP
        $userip = [];
        $iplocation = new QQWry();
        $total = Ip::where('datetime', '>=', time() - 300)->where('userid', '=', $this->user->id)->get();
        foreach ($total as $single) {
            $single->ip = Tools::getRealIp($single->ip);
            $is_node = Node::where('node_ip', $single->ip)->first();
            if ($is_node) {
                continue;
            }
            if (! isset($userip[$single->ip])) {
                $location = $iplocation->getlocation($single->ip);
                $userip[$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            }
        }

        if ($request->getParam('json') === 1) {
            return $response->withJson([
                'ret' => 1,
                'paybacks' => $paybacks,
                'userloginip' => $totallogin,
                'userip' => $userip,
            ]);
        }

        $boughts = Bought::where('userid', $this->user->id)->orderBy('id', 'desc')->get();

        return $response->write(
            $this->view()
                ->assign('boughts', $boughts)
                ->assign('userip', $userip)
                ->assign('userloginip', $totallogin)
                ->assign('paybacks', $paybacks)
                ->registerClass('Tools', Tools::class)
                ->display('user/profile.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function announcement(Request $request, Response $response, array $args)
    {
        $Anns = Ann::orderBy('date', 'desc')->get();

        if ($request->getParam('json') === 1) {
            return $response->withJson([
                'Anns' => $Anns,
                'ret' => 1,
            ]);
        }

        return $response->write(
            $this->view()
                ->assign('anns', $Anns)
                ->display('user/announcement.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function media(Request $request, Response $response, array $args)
    {
        $results = [];
        $db = new DatatablesHelper();
        $nodes = $db->query('SELECT DISTINCT node_id FROM stream_media');

        foreach ($nodes as $node_id) {
            $node = Node::where('id', $node_id)->first();

            $unlock = StreamMedia::where('node_id', $node_id)
                ->orderBy('id', 'desc')
                ->where('created_at', '>', time() - 86460) // 只获取最近一天零一分钟内上报的数据
                ->first();

            if ($unlock !== null && $node !== null) {
                $details = json_decode($unlock->result, true);
                $details = str_replace('Originals Only', '仅限自制', $details);
                $details = str_replace('Oversea Only', '仅限海外', $details);

                foreach ($details as $key => $value) {
                    $info = [
                        'node_name' => $node->name,
                        'created_at' => $unlock->created_at,
                        'unlock_item' => $details,
                    ];
                }

                array_push($results, $info);
            }
        }

        if ($_ENV['streaming_media_unlock_multiplexing'] !== null) {
            foreach ($_ENV['streaming_media_unlock_multiplexing'] as $key => $value) {
                $key_node = Node::where('id', $key)->first();
                $value_node = StreamMedia::where('node_id', $value)
                    ->orderBy('id', 'desc')
                    ->where('created_at', '>', time() - 86460) // 只获取最近一天零一分钟内上报的数据
                    ->first();

                if ($value_node !== null) {
                    $details = json_decode($value_node->result, true);
                    $details = str_replace('Originals Only', '仅限自制', $details);
                    $details = str_replace('Oversea Only', '仅限海外', $details);

                    $info = [
                        'node_name' => $key_node->name,
                        'created_at' => $value_node->created_at,
                        'unlock_item' => $details,
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

    /**
     * @param array     $args
     */
    public function edit(Request $request, Response $response, array $args)
    {
        $themes = Tools::getDir(BASE_PATH . '/resources/views');

        $BIP = BlockIp::where('ip', $_SERVER['REMOTE_ADDR'])->first();
        if ($BIP === null) {
            $Block = 'IP: ' . $_SERVER['REMOTE_ADDR'] . ' 没有被封';
            $isBlock = 0;
        } else {
            $Block = 'IP: ' . $_SERVER['REMOTE_ADDR'] . ' 已被封';
            $isBlock = 1;
        }

        $bind_token = TelegramSessionManager::addBindSession($this->user);

        return $this->view()
            ->assign('user', $this->user)
            ->assign('themes', $themes)
            ->assign('isBlock', $isBlock)
            ->assign('Block', $Block)
            ->assign('bind_token', $bind_token)
            ->assign('telegram_bot', $_ENV['telegram_bot'])
            ->registerClass('Config', Config::class)
            ->registerClass('URL', URL::class)
            ->display('user/edit.tpl');
    }

    /**
     * @param array     $args
     */
    public function invite(Request $request, Response $response, array $args)
    {
        $code = InviteCode::where('user_id', $this->user->id)->first();
        if ($code === null) {
            $this->user->addInviteCode();
            $code = InviteCode::where('user_id', $this->user->id)->first();
        }

        $pageNum = $request->getQueryParams()['page'] ?? 1;

        $paybacks = Payback::where('ref_by', $this->user->id)
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page', $pageNum);

        $paybacks_sum = Payback::where('ref_by', $this->user->id)->sum('ref_get');
        if (! $paybacks_sum) {
            $paybacks_sum = 0;
        }

        $render = Tools::paginateRender($paybacks);

        $invite_url = $_ENV['baseUrl'] . '/auth/register?code=' . $code->code;

        return $this->view()
            ->assign('code', $code)
            ->assign('render', $render)
            ->assign('paybacks', $paybacks)
            ->assign('invite_url', $invite_url)
            ->assign('paybacks_sum', $paybacks_sum)
            ->display('user/invite.tpl');
    }

    /**
     * @param array     $args
     */
    public function buyInvite(Request $request, Response $response, array $args)
    {
        $price = $_ENV['invite_price'];
        $num = $request->getParam('num');
        $num = trim($num);

        if (! Tools::isInt($num) || $price < 0 || $num <= 0) {
            return ResponseHelper::error($response, '非法请求');
        }

        $amount = $price * $num;

        $user = $this->user;

        if (! $user->isLogin) {
            return $response->withJson([ 'ret' => -1 ]);
        }

        if ($user->money < $amount) {
            return ResponseHelper::error($response, '余额不足，总价为' . $amount . '元。');
        }
        $user->invite_num += $num;
        $user->money -= $amount;
        $user->save();

        return ResponseHelper::successfully($response, '邀请次数添加成功');
    }

    /**
     * @param array     $args
     */
    public function customInvite(Request $request, Response $response, array $args)
    {
        $price = $_ENV['custom_invite_price'];
        $customcode = $request->getParam('customcode');
        $customcode = trim($customcode);

        if (! Tools::isValidate($customcode) || $price < 0 || $customcode === '' || strlen($customcode) > 32) {
            return ResponseHelper::error(
                $response,
                '非法请求,邀请链接后缀不能包含特殊符号且长度不能大于32字符'
            );
        }

        if (InviteCode::where('code', $customcode)->count() !== 0) {
            return ResponseHelper::error($response, '此后缀名被抢注了');
        }

        $user = $this->user;

        if (! $user->isLogin) {
            return $response->withJson([ 'ret' => -1 ]);
        }

        if ($user->money < $price) {
            return ResponseHelper::error(
                $response,
                '余额不足，总价为' . $price . '元。'
            );
        }
        $code = InviteCode::where('user_id', $user->id)->first();
        $code->code = $customcode;
        $user->money -= $price;
        $user->save();
        $code->save();
        return ResponseHelper::successfully($response, '定制成功');
    }

    /**
     * @param array     $args
     */
    public function sys(Request $request, Response $response, array $args)
    {
        return $this->view()->assign('ana', '')->display('user/sys.tpl');
    }

    /**
     * @param array     $args
     */
    public function updatePassword(Request $request, Response $response, array $args)
    {
        $oldpwd = $request->getParam('oldpwd');
        $pwd = $request->getParam('pwd');
        $repwd = $request->getParam('repwd');
        $user = $this->user;
        if (! Hash::checkPassword($user->pass, $oldpwd)) {
            return ResponseHelper::error($response, '旧密码错误');
        }
        if ($pwd !== $repwd) {
            return ResponseHelper::error($response, '两次输入不符合');
        }

        if (strlen($pwd) < 8) {
            return ResponseHelper::error($response, '密码太短啦');
        }
        $hashPwd = Hash::passwordHash($pwd);
        $user->pass = $hashPwd;
        $user->save();

        if ($_ENV['enable_forced_replacement'] === true) {
            $user->cleanLink();
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    /**
     * @param array     $args
     */
    public function updateEmail(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $newemail = $request->getParam('newemail');
        $oldemail = $user->email;
        $otheruser = User::where('email', $newemail)->first();

        if ($_ENV['enable_change_email'] !== true) {
            return ResponseHelper::error($response, '此项不允许自行修改，请联系管理员操作');
        }

        if (Setting::obtain('reg_email_verify')) {
            $emailcode = $request->getParam('emailcode');
            $mailcount = EmailVerify::where('email', '=', $newemail)->where('code', '=', $emailcode)->where('expire_in', '>', time())->first();
            if ($mailcount === null) {
                return ResponseHelper::error($response, '您的邮箱验证码不正确');
            }
        }

        if ($newemail === '') {
            return ResponseHelper::error($response, '未填写邮箱');
        }

        $check_res = Check::isEmailLegal($newemail);
        if ($check_res['ret'] === 0) {
            return $response->withJson($check_res);
        }

        if ($otheruser !== null) {
            return ResponseHelper::error($response, '邮箱已经被使用了');
        }

        if ($newemail === $oldemail) {
            return ResponseHelper::error($response, '新邮箱不能和旧邮箱一样');
        }

        $antiXss = new AntiXSS();
        $user->email = $antiXss->xss_clean($newemail);
        $user->save();

        return ResponseHelper::successfully($response, '修改成功');
    }

    /**
     * @param array     $args
     */
    public function updateUsername(Request $request, Response $response, array $args)
    {
        $newusername = $request->getParam('newusername');
        $user = $this->user;
        $antiXss = new AntiXSS();
        $user->user_name = $antiXss->xss_clean($newusername);
        $user->save();

        return ResponseHelper::successfully($response, '修改成功');
    }

    /**
     * @param array     $args
     */
    public function updateHide(Request $request, Response $response, array $args)
    {
        $hide = $request->getParam('hide');
        $user = $this->user;
        $user->is_hide = $hide;
        $user->save();

        return ResponseHelper::successfully($response, '修改成功');
    }

    /**
     * @param array     $args
     */
    public function unblock(Request $request, Response $response, array $args)
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

        return ResponseHelper::successfully($response, $_SERVER['REMOTE_ADDR']);
    }

    /**
     * @param array     $args
     */
    public function bought(Request $request, Response $response, array $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $shops = Bought::where('userid', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        if ($request->getParam('json') === 1) {
            foreach ($shops as $shop) {
                $shop->datetime = $shop->datetime();
                $shop->name = $shop->shop()->name;
                $shop->content = $shop->shop()->content();
            }
            return $response->withJson([
                'ret' => 1,
                'shops' => $shops,
            ]);
        }
        $render = Tools::paginateRender($shops);
        return $this->view()
            ->assign('shops', $shops)
            ->assign('render', $render)
            ->display('user/bought.tpl');
    }

    /**
     * @param array     $args
     */
    public function deleteBoughtGet(Request $request, Response $response, array $args)
    {
        $id = $request->getParam('id');
        $shop = Bought::where('id', $id)->where('userid', $this->user->id)->first();

        if ($shop === null) {
            return ResponseHelper::error($response, '关闭自动续费失败，订单不存在。');
        }

        if ($this->user->id === $shop->userid) {
            $shop->renew = 0;
        }

        if (! $shop->save()) {
            return ResponseHelper::error($response, '关闭自动续费失败');
        }
        return ResponseHelper::successfully($response, '关闭自动续费成功');
    }

    /**
     * @param array     $args
     */
    public function updateWechat(Request $request, Response $response, array $args)
    {
        $type = $request->getParam('imtype');
        $wechat = $request->getParam('wechat');
        $wechat = trim($wechat);

        $user = $this->user;

        if ($user->telegram_id !== 0) {
            return ResponseHelper::error(
                $response,
                '您绑定了 Telegram ，所以此项并不能被修改。'
            );
        }

        if ($wechat === '' || $type === '') {
            return ResponseHelper::error($response, '非法输入');
        }

        $user1 = User::where('im_value', $wechat)->where('im_type', $type)->first();
        if ($user1 !== null) {
            return ResponseHelper::error($response, '此联络方式已经被注册');
        }

        $user->im_type = $type;
        $antiXss = new AntiXSS();
        $user->im_value = $antiXss->xss_clean($wechat);
        $user->save();

        return ResponseHelper::successfully($response, '修改成功');
    }

    /**
     * @param array     $args
     */
    public function updateSSR(Request $request, Response $response, array $args)
    {
        $protocol = $request->getParam('protocol');
        $obfs = $request->getParam('obfs');
        $obfs_param = $request->getParam('obfs_param');
        $obfs_param = trim($obfs_param);

        $user = $this->user;

        if ($obfs === '' || $protocol === '') {
            return ResponseHelper::error($response, '非法输入');
        }

        if (! Tools::isParamValidate('obfs', $obfs)) {
            return ResponseHelper::error($response, '协议无效');
        }

        if (! Tools::isParamValidate('protocol', $protocol)) {
            return ResponseHelper::error($response, '协议无效');
        }

        $antiXss = new AntiXSS();

        $user->protocol = $antiXss->xss_clean($protocol);
        $user->obfs = $antiXss->xss_clean($obfs);
        $user->obfs_param = $antiXss->xss_clean($obfs_param);

        if (! Tools::checkNoneProtocol($user)) {
            return ResponseHelper::error(
                $response,
                '系统检测到您目前的加密方式为 none ，但您将要设置为的协议并不在以下协议<br>'
                . implode(',', Config::getSupportParam('allow_none_protocol'))
                . '<br>之内，请您先修改您的加密方式，再来修改此处设置。'
            );
        }

        if (! URL::SSCanConnect($user) && ! URL::SSRCanConnect($user)) {
            return ResponseHelper::error(
                $response,
                '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。'
            );
        }

        $user->save();

        if (! URL::SSCanConnect($user)) {
            return ResponseHelper::error(
                $response,
                '设置成功，但您目前的协议，混淆，加密方式设置会导致 Shadowsocks原版客户端无法连接，请您自行更换到 ShadowsocksR 客户端。'
            );
        }

        if (! URL::SSRCanConnect($user)) {
            return ResponseHelper::error(
                $response,
                '设置成功，但您目前的协议，混淆，加密方式设置会导致 ShadowsocksR 客户端无法连接，请您自行更换到 Shadowsocks 客户端。'
            );
        }

        return ResponseHelper::successfully($response, '设置成功，您可自由选用客户端来连接。');
    }

    /**
     * @param array     $args
     */
    public function updateTheme(Request $request, Response $response, array $args)
    {
        $theme = $request->getParam('theme');

        $user = $this->user;

        if ($theme === '') {
            return ResponseHelper::error($response, '非法输入');
        }

        $user->theme = filter_var($theme, FILTER_SANITIZE_STRING);
        $user->save();

        return ResponseHelper::successfully($response, '设置成功');
    }

    /**
     * @param array     $args
     */
    public function updateMail(Request $request, Response $response, array $args)
    {
        $value = (int) $request->getParam('mail');
        if (in_array($value, [0, 1, 2])) {
            $user = $this->user;
            if ($value === 2 && $_ENV['enable_telegram'] === false) {
                return ResponseHelper::error(
                    $response,
                    '修改失败，当前无法使用 Telegram 接收每日报告'
                );
            }
            $user->sendDailyMail = $value;
            $user->save();
            return ResponseHelper::successfully($response, '修改成功');
        }
        return ResponseHelper::error($response, '非法输入');
    }

    /**
     * @param array     $args
     */
    public function updateSsPwd(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $pwd = Tools::genRandomChar(16);
        $current_timestamp = time();
        $new_uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, $user->email . '|' . $current_timestamp);
        $otheruuid = User::where('uuid', $new_uuid)->first();

        if ($pwd === '') {
            return ResponseHelper::error($response, '密码不能为空');
        }
        if (! Tools::isValidate($pwd)) {
            return ResponseHelper::error($response, '密码无效');
        }
        if ($otheruuid !== null) {
            return ResponseHelper::error($response, '目前出现一些问题，请稍后再试');
        }

        $user->uuid = $new_uuid;
        $user->save();
        $user->updateSsPwd($pwd);
        return ResponseHelper::successfully($response, '修改成功');
    }

    /**
     * @param array     $args
     */
    public function updateMethod(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $method = strtolower($request->getParam('method'));
        $result = $user->updateMethod($method);
        $result['ret'] = $result['ok'] ? 1 : 0;
        return $response->withJson($result);
    }

    /**
     * @param array     $args
     */
    public function logout(Request $request, Response $response, array $args)
    {
        Auth::logout();
        return $response->withStatus(302)->withHeader('Location', '/');
    }

    /**
     * @param array     $args
     */
    public function doCheckIn(Request $request, Response $response, array $args)
    {
        if ($_ENV['enable_checkin'] === false) {
            return ResponseHelper::error($response, '暂时还不能签到');
        }

        if (Setting::obtain('enable_checkin_captcha') === true) {
            $ret = Captcha::verify($request->getParams());
            if (! $ret) {
                return ResponseHelper::error($response, '系统无法接受您的验证结果，请刷新页面后重试');
            }
        }

        if (strtotime($this->user->expire_in) < time()) {
            return ResponseHelper::error($response, '没有过期的账户才可以签到');
        }

        $checkin = $this->user->checkin();
        if ($checkin['ok'] === false) {
            return ResponseHelper::error($response, $checkin['msg']);
        }

        return $response->withJson([
            'ret' => 1,
            'trafficInfo' => [
                'todayUsedTraffic' => $this->user->todayUsedTraffic(),
                'lastUsedTraffic' => $this->user->lastUsedTraffic(),
                'unUsedTraffic' => $this->user->unusedTraffic(),
            ],
            'traffic' => Tools::flowAutoShow($this->user->transfer_enable),
            'unflowtraffic' => $this->user->transfer_enable,
            'msg' => $checkin['msg'],
        ]);
    }

    /**
     * @param array     $args
     */
    public function kill(Request $request, Response $response, array $args)
    {
        return $this->view()->display('user/kill.tpl');
    }

    /**
     * @param array     $args
     */
    public function handleKill(Request $request, Response $response, array $args)
    {
        $user = $this->user;

        $passwd = $request->getParam('passwd');
        if (! Hash::checkPassword($user->pass, $passwd)) {
            return ResponseHelper::error($response, '密码错误');
        }

        if ($_ENV['enable_kill'] === true) {
            Auth::logout();
            $user->killUser();
            return ResponseHelper::successfully($response, '您的帐号已经从我们的系统中删除。欢迎下次光临');
        }
        return ResponseHelper::error($response, '管理员不允许删除，如需删除请联系管理员。');
    }

    /**
     * @param array     $args
     */
    public function disable(Request $request, Response $response, array $args)
    {
        return $this->view()->display('user/disable.tpl');
    }

    /**
     * @param array     $args
     */
    public function telegramReset(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $user->telegramReset();
        return $response->withStatus(302)->withHeader('Location', '/user/edit');
    }

    /**
     * @param array     $args
     */
    public function resetURL(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $user->cleanLink();
        return $response->withStatus(302)->withHeader('Location', '/user');
    }

    /**
     * @param array     $args
     */
    public function resetInviteURL(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $user->clearInviteCodes();

        return ResponseHelper::successfully($response, '重置成功');
    }

    /**
     * @param array     $args
     */
    public function backtoadmin(Request $request, Response $response, array $args)
    {
        $userid = Cookie::get('uid');
        $adminid = Cookie::get('old_uid');
        $user = User::find($userid);
        $admin = User::find($adminid);

        if (! $admin->is_admin || ! $user) {
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
                'old_local' => null,
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
            'old_local' => null,
        ], $expire_in);
        return $response->withStatus(302)->withHeader('Location', $local);
    }

    /**
     * @param array     $args
     */
    public function getUserAllURL(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $type = $request->getQueryParams()['type'];
        $return = '';
        switch ($type) {
            case 'ss':
                $return .= URL::getNewAllUrl($user, ['type' => 'ss']) . PHP_EOL;
                break;
            case 'ssr':
                $return .= URL::getNewAllUrl($user, ['type' => 'ssr']) . PHP_EOL;
                break;
            case 'v2ray':
                $return .= URL::getNewAllUrl($user, ['type' => 'vmess']) . PHP_EOL;
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
     * @param array    $args
     */
    public function subscribeLog(Request $request, Response $response, array $args)
    {
        if ($_ENV['subscribeLog_show'] === false) {
            return $response->withStatus(302)->withHeader('Location', '/user');
        }

        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $logs = UserSubscribeLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->paginate(15, ['*'], 'page', $pageNum);

        $render = Tools::paginateRender($logs);
        return $this->view()
            ->assign('logs', $logs)
            ->assign('render', $render)
            ->registerClass('Tools', Tools::class)
            ->fetch('user/subscribe_log.tpl');
    }

    /**
     * 获取包含订阅信息的客户端压缩档，PHP 需安装 zip 扩展
     *
     * @param array    $args
     */
    public function getPcClient(Request $request, Response $response, array $args)
    {
        $zipArc = new \ZipArchive();
        $user_token = LinkController::generateSSRSubCode($this->user->id);
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
     * @param array    $args
     */
    public function getClientfromToken(Request $request, Response $response, array $args)
    {
        $token = $args['token'];
        $Etoken = Token::where('token', '=', $token)->where('create_time', '>', time() - 60 * 10)->first();
        if ($Etoken === null) {
            return '下载链接已失效，请刷新页面后重新点击.';
        }
        $user = User::find($Etoken->user_id);
        if ($user === null) {
            return null;
        }
        $this->user = $user;
        return $this->getPcClient($request, $response, $args);
    }
}
