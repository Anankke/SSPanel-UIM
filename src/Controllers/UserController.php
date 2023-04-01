<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ann;
use App\Models\Docs;
use App\Models\EmailVerify;
use App\Models\InviteCode;
use App\Models\LoginIp;
use App\Models\Node;
use App\Models\Payback;
use App\Models\Setting;
use App\Models\StreamMedia;
use App\Models\User;
use App\Services\Auth;
use App\Services\Captcha;
use App\Services\Config;
use App\Services\DB;
use App\Services\MFA;
use App\Utils\Cookie;
use App\Utils\Hash;
use App\Utils\ResponseHelper;
use App\Utils\Telegram;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function in_array;
use function json_decode;
use function strlen;
use function time;

/**
 *  HomeController
 */
final class UserController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $captcha = [];

        if (Setting::obtain('enable_checkin_captcha') === true) {
            $captcha = Captcha::generate();
        }

        $data = [
            'today_traffic_usage' => (int) $this->user->transfer_enable === 0 ? 0 : ($this->user->u + $this->user->d - $this->user->last_day_t) / $this->user->transfer_enable * 100,
            'past_traffic_usage' => (int) $this->user->transfer_enable === 0 ? 0 : $this->user->last_day_t / $this->user->transfer_enable * 100,
            'residual_flow' => (int) $this->user->transfer_enable === 0 ? 0 : ($this->user->transfer_enable - ($this->user->u + $this->user->d)) / $this->user->transfer_enable * 100,
        ];

        return $response->write(
            $this->view()
                ->assign('ann', Ann::orderBy('date', 'desc')->first())
                ->assign('UniversalSub', SubController::getUniversalSub($this->user))
                ->assign('TraditionalSub', LinkController::getTraditionalSub($this->user))
                ->assign('data', $data)
                ->assign('captcha', $captcha)
                ->fetch('user/index.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function profile(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        // 登录IP
        $logins = LoginIp::where('userid', '=', $this->user->id)->where('type', '=', 0)->orderBy('datetime', 'desc')->take(10)->get();

        foreach ($logins as $login) {
            $login->datetime = Tools::toDateTime((int) $login->datetime);
            $login->location = Tools::getIpLocation($login->ip);
        }

        return $response->write(
            $this->view()
                ->assign('logins', $logins)
                ->fetch('user/profile.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function announcement(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $anns = Ann::orderBy('date', 'desc')->get();

        return $response->write(
            $this->view()
                ->assign('anns', $anns)
                ->fetch('user/announcement.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function docs(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $docs = Docs::orderBy('id', 'desc')->get();

        return $response->write(
            $this->view()
                ->assign('docs', $docs)
                ->fetch('user/docs.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function media(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $results = [];
        $pdo = DB::getPdo();
        $nodes = $pdo->query('SELECT DISTINCT node_id FROM stream_media');

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
                $info = [];

                foreach ($details as $key => $value) {
                    $info = [
                        'node_name' => $node->name,
                        'created_at' => $unlock->created_at,
                        'unlock_item' => $details,
                    ];
                }

                $results[] = $info;
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

                    $results[] = $info;
                }
            }
        }

        $node_names = array_column($results, 'node_name');
        array_multisort($node_names, SORT_ASC, $results);

        return $response->write($this->view()
            ->assign('results', $results)
            ->fetch('user/media.tpl'));
    }

    /**
     * @throws Exception
     */
    public function edit(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $themes = Tools::getDir(BASE_PATH . '/resources/views');
        $bind_token = Telegram::addBindSession($this->user);
        $methods = Config::getSupportParam('method');
        $gaurl = MFA::getGAurl($this->user);

        return $response->write($this->view()
            ->assign('user', $this->user)
            ->assign('themes', $themes)
            ->assign('bind_token', $bind_token)
            ->assign('methods', $methods)
            ->assign('gaurl', $gaurl)
            ->assign('telegram_bot', $_ENV['telegram_bot'])
            ->registerClass('Config', Config::class)
            ->fetch('user/edit.tpl'));
    }

    /**
     * @throws Exception
     */
    public function invite(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $code = InviteCode::where('user_id', $this->user->id)->first();
        if ($code === null) {
            $this->user->addInviteCode();
            $code = InviteCode::where('user_id', $this->user->id)->first();
        }

        $paybacks = Payback::where('ref_by', $this->user->id)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($paybacks as $payback) {
            $payback->datetime = Tools::toDateTime($payback->datetime);
        }

        $paybacks_sum = Payback::where('ref_by', $this->user->id)->sum('ref_get');
        if (! $paybacks_sum) {
            $paybacks_sum = 0;
        }

        $invite_url = $_ENV['baseUrl'] . '/auth/register?code=' . $code->code;

        return $response->write($this->view()
            ->assign('code', $code)
            ->assign('paybacks', $paybacks)
            ->assign('invite_url', $invite_url)
            ->assign('paybacks_sum', $paybacks_sum)
            ->fetch('user/invite.tpl'));
    }

    public function updatePassword(ServerRequest $request, Response $response, array $args): ResponseInterface
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

        if (Setting::obtain('enable_forced_replacement')) {
            $user->cleanLink();
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function updateEmail(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();

        $user = $this->user;
        $newemail = $antiXss->xss_clean($request->getParam('newemail'));
        $oldemail = $user->email;
        $otheruser = User::where('email', $newemail)->first();

        if (! $_ENV['enable_change_email']) {
            return ResponseHelper::error($response, '此项不允许自行修改，请联系管理员操作');
        }

        if (Setting::obtain('reg_email_verify')) {
            $emailcode = $request->getParam('emailcode');
            $mailcount = EmailVerify::where('email', '=', $newemail)
                ->where('code', '=', $emailcode)->where('expire_in', '>', time())->first();
            if ($mailcount === null) {
                return ResponseHelper::error($response, '您的邮箱验证码不正确');
            }
        }

        if ($newemail === '') {
            return ResponseHelper::error($response, '未填写邮箱');
        }

        $check_res = Tools::isEmailLegal($newemail);
        if ($check_res['ret'] === 0) {
            return $response->withJson($check_res);
        }

        if ($otheruser !== null) {
            return ResponseHelper::error($response, '邮箱已经被使用了');
        }

        if ($newemail === $oldemail) {
            return ResponseHelper::error($response, '新邮箱不能和旧邮箱一样');
        }

        $user->email = $newemail;
        $user->save();

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function updateUsername(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();

        $newusername = $antiXss->xss_clean($request->getParam('newusername'));
        $user = $this->user;

        $user->user_name = $newusername;
        $user->save();

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function updateContact(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();

        $type = $antiXss->xss_clean($request->getParam('imtype'));
        $value = $antiXss->xss_clean($request->getParam('imvalue'));

        $user = $this->user;

        if ($user->telegram_id !== null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '你的账户绑定了 Telegram ，所以此项并不能被修改',
            ]);
        }

        if ($value === '' || $type === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '联络方式不能为空',
            ]);
        }

        $user_exist = User::where('im_value', $value)->where('im_type', $type)->first();
        if ($user_exist !== null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '此联络方式已经被注册',
            ]);
        }

        $user->im_type = $type;
        $user->im_value = $value;
        $user->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    public function updateTheme(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();
        $theme = $antiXss->xss_clean($request->getParam('theme'));

        $user = $this->user;

        if ($theme === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '主题不能为空',
            ]);
        }

        $user->theme = $theme;
        $user->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    public function updateMail(ServerRequest $request, Response $response, array $args): ResponseInterface
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

    public function resetPasswd(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $pwd = Tools::genRandomChar(16);
        $current_timestamp = time();
        $new_uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, $user->email . '|' . $current_timestamp);
        $existing_uuid = User::where('uuid', $new_uuid)->first();

        if ($existing_uuid !== null) {
            return ResponseHelper::error($response, '目前出现一些问题，请稍后再试');
        }

        $user->uuid = $new_uuid;
        $user->passwd = $pwd;
        $user->save();

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function updateMethod(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();

        $user = $this->user;

        $method = strtolower($antiXss->xss_clean($request->getParam('method')));

        if ($method === '') {
            ResponseHelper::error($response, '非法输入');
        }
        if (! Tools::isParamValidate('method', $method)) {
            ResponseHelper::error($response, '加密无效');
        }

        $user->method = $method;
        $user->save();

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function logout(ServerRequest $request, Response $response, array $args): Response
    {
        Auth::logout();
        return $response->withStatus(302)->withHeader('Location', '/');
    }

    public function doCheckIn(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
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

        if (! $checkin['ok']) {
            return ResponseHelper::error($response, (string) $checkin['msg']);
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
     * @throws Exception
     */
    public function kill(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write($this->view()->fetch('user/kill.tpl'));
    }

    public function handleKill(ServerRequest $request, Response $response, array $args): ResponseInterface
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
     * @throws Exception
     */
    public function banned(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $user = $this->user;

        return $response->write($this->view()
            ->assign('banned_reason', $user->banned_reason)
            ->fetch('user/banned.tpl'));
    }

    public function resetTelegram(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->telegramReset();

        return ResponseHelper::successfully($response, '重置成功');
    }

    public function resetURL(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->cleanLink();

        return ResponseHelper::successfully($response, '重置成功');
    }

    public function resetInviteURL(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->clearInviteCodes();

        return ResponseHelper::successfully($response, '重置成功');
    }

    public function backtoadmin(ServerRequest $request, Response $response, array $args): Response
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

    public function switchThemeMode(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $user = $this->user;
        if ($user->is_dark_mode === 1) {
            $user->is_dark_mode = 0;
        } else {
            $user->is_dark_mode = 1;
        }
        $user->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '切换成功',
        ]);
    }
}
