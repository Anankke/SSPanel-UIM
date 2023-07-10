<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ann;
use App\Models\Docs;
use App\Models\InviteCode;
use App\Models\LoginIp;
use App\Models\Node;
use App\Models\OnlineLog;
use App\Models\Payback;
use App\Models\Setting;
use App\Models\StreamMedia;
use App\Models\User;
use App\Services\Auth;
use App\Services\Cache;
use App\Services\Captcha;
use App\Services\Config;
use App\Services\DB;
use App\Services\MFA;
use App\Utils\Hash;
use App\Utils\ResponseHelper;
use App\Utils\Telegram;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use RedisException;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function array_column;
use function array_multisort;
use function in_array;
use function json_decode;
use function str_replace;
use function strlen;
use function strtolower;
use function strtotime;
use function time;
use const BASE_PATH;
use const SORT_ASC;

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

        if (Setting::obtain('enable_checkin_captcha')) {
            $captcha = Captcha::generate();
        }

        return $response->write(
            $this->view()
                ->assign('ann', Ann::orderBy('date', 'desc')->first())
                ->assign('UniversalSub', SubController::getUniversalSub($this->user))
                ->assign('TraditionalSub', LinkController::getTraditionalSub($this->user))
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
        $logins = LoginIp::where('userid', '=', $this->user->id)
            ->where('type', '=', 0)->orderBy('datetime', 'desc')->take(10)->get();
        $ips = OnlineLog::where('user_id', '=', $this->user->id)
            ->where('last_time', '>', time() - 90)->orderByDesc('last_time')->get();

        foreach ($logins as $login) {
            $login->datetime = Tools::toDateTime((int) $login->datetime);
            $login->location = Tools::getIpLocation($login->ip);
        }

        foreach ($ips as $ip) {
            $ip->ip = str_replace('::ffff:', '', $ip->ip);
            $ip->location = Tools::getIpLocation($ip->ip);
            $ip->node_name = Node::where('id', $ip->node_id)->first()->name;
            $ip->last_time = Tools::toDateTime((int) $ip->last_time);
        }

        return $response->write(
            $this->view()
                ->assign('logins', $logins)
                ->assign('ips', $ips)
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
                ->where('created_at', '>', time() - 86400) // 只获取最近一天内上报的数据
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

        if ($oldpwd === '' || $pwd === '' || $repwd === '') {
            return ResponseHelper::error($response, '密码不能为空');
        }

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

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        if (Setting::obtain('enable_forced_replacement')) {
            $user->cleanLink();
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    /**
     * @throws RedisException
     */
    public function updateEmail(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();
        $user = $this->user;
        $new_email = $antiXss->xss_clean($request->getParam('newemail'));
        $old_email = $user->email;

        if (! $_ENV['enable_change_email']) {
            return ResponseHelper::error($response, '此项不允许自行修改，请联系管理员操作');
        }

        if ($new_email === '') {
            return ResponseHelper::error($response, '未填写邮箱');
        }

        if (! Tools::isEmailLegal($new_email)) {
            return $response->withJson($check_res);
        }

        $exist_user = User::where('email', $new_email)->first();

        if ($exist_user !== null) {
            return ResponseHelper::error($response, '邮箱已经被使用了');
        }

        if ($new_email === $old_email) {
            return ResponseHelper::error($response, '新邮箱不能和旧邮箱一样');
        }

        if (Setting::obtain('reg_email_verify')) {
            $redis = Cache::initRedis();
            $email_verify_code = $request->getParam('emailcode');
            $email_verify = $redis->get($email_verify_code);

            if (! $email_verify) {
                return ResponseHelper::error($response, '你的邮箱验证码不正确');
            }

            $redis->del($email_verify_code);
        }

        $user->email = $new_email;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function updateUsername(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();
        $newusername = $antiXss->xss_clean($request->getParam('newusername'));
        $user = $this->user;
        $user->user_name = $newusername;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function updateContact(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();
        $type = $antiXss->xss_clean($request->getParam('imtype'));
        $value = $antiXss->xss_clean($request->getParam('imvalue'));
        $user = $this->user;

        if ($user->telegram_id !== null) {
            return ResponseHelper::error($response, '你的账户绑定了 Telegram ，所以此项并不能被修改');
        }

        if ($value === '' || $type === '') {
            return ResponseHelper::error($response, '联络方式不能为空');
        }

        if (User::where('im_value', $value)->where('im_type', $type)->first() !== null) {
            return ResponseHelper::error($response, '此联络方式已经被注册');
        }

        $user->im_type = $type;
        $user->im_value = $value;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function updateTheme(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();
        $theme = $antiXss->xss_clean($request->getParam('theme'));
        $user = $this->user;

        if ($theme === '') {
            return ResponseHelper::error($response, '主题不能为空');
        }

        $user->theme = $theme;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function updateMail(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $value = (int) $request->getParam('mail');

        if (! in_array($value, [0, 1, 2])) {
            return ResponseHelper::error($response, '参数错误');
        }

        $user = $this->user;

        if ($value === 2 && ! $_ENV['enable_telegram']) {
            return ResponseHelper::error(
                $response,
                '修改失败，当前无法使用 Telegram 接收每日报告'
            );
        }

        $user->daily_mail_enable = $value;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function resetPasswd(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->uuid = Uuid::uuid4();
        $user->passwd = Tools::genRandomChar(16);

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function resetApiToken(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->api_token = Uuid::uuid4();

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

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

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::successfully($response, '修改成功');
    }

    public function logout(ServerRequest $request, Response $response, array $args): Response
    {
        Auth::logout();
        return $response->withStatus(302)->withHeader('Location', '/');
    }

    public function doCheckIn(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        if (! $_ENV['enable_checkin']) {
            return ResponseHelper::error($response, '暂时还不能签到');
        }

        if (Setting::obtain('enable_checkin_captcha')) {
            $ret = Captcha::verify($request->getParams());
            if (! $ret) {
                return ResponseHelper::error($response, '系统无法接受你的验证结果，请刷新页面后重试');
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
            'traffic' => Tools::autoBytes($this->user->transfer_enable),
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

        if ($passwd === '') {
            return ResponseHelper::error($response, '密码不能为空');
        }

        if (! Hash::checkPassword($user->pass, $passwd)) {
            return ResponseHelper::error($response, '密码错误');
        }

        if ($_ENV['enable_kill']) {
            Auth::logout();
            $user->killUser();
            return ResponseHelper::successfully($response, '你的帐号已经从我们的系统中删除。欢迎下次光临');
        }

        return ResponseHelper::error($response, '自助账号删除未启用，如需删除账户请联系管理员。');
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

    public function switchThemeMode(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $user = $this->user;

        $user->is_dark_mode = $user->is_dark_mode === 1 ? 0 : 1;

        if (! $user->save()) {
            return ResponseHelper::error($response, '切换失败');
        }

        return ResponseHelper::successfully($response, '切换成功');
    }
}
