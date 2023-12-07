<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ann;
use App\Models\Config;
use App\Models\InviteCode;
use App\Models\LoginIp;
use App\Models\Node;
use App\Models\OnlineLog;
use App\Models\Payback;
use App\Services\Auth;
use App\Services\Captcha;
use App\Services\Subscribe;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function str_replace;
use function strtotime;
use function time;

final class UserController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $captcha = [];
        $class_expire_days = $this->user->class > 0 ?
            round((strtotime($this->user->class_expire) - time()) / 86400) : 0;

        if (Config::obtain('enable_checkin_captcha')) {
            $captcha = Captcha::generate();
        }

        return $response->write(
            $this->view()
                ->assign('ann', (new Ann())->orderBy('date', 'desc')->first())
                ->assign('captcha', $captcha)
                ->assign('class_expire_days', $class_expire_days)
                ->assign('UniversalSub', Subscribe::getUniversalSubLink($this->user))
                ->fetch('user/index.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function profile(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        // 登录IP
        $logins = (new LoginIp())->where('userid', $this->user->id)
            ->where('type', '=', 0)->orderBy('datetime', 'desc')->take(10)->get();
        $ips = (new OnlineLog())->where('user_id', $this->user->id)
            ->where('last_time', '>', time() - 90)->orderByDesc('last_time')->get();

        foreach ($logins as $login) {
            $login->datetime = Tools::toDateTime((int) $login->datetime);

            try {
                $login->location = Tools::getIpLocation($login->ip);
            } catch (Exception) {
                $login->location = '未知';
            }
        }

        foreach ($ips as $ip) {
            $ip->ip = str_replace('::ffff:', '', $ip->ip);
            $ip->location = Tools::getIpLocation($ip->ip);
            $ip->node_name = (new Node())->where('id', $ip->node_id)->first()->name;
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
        $anns = (new Ann())->orderBy('date', 'desc')->get();

        return $response->write(
            $this->view()
                ->assign('anns', $anns)
                ->fetch('user/announcement.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function invite(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $code = (new InviteCode())->where('user_id', $this->user->id)->first()?->code;

        if ($code === null) {
            $code = $this->user->addInviteCode();
        }

        $paybacks = (new Payback())->where('ref_by', $this->user->id)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($paybacks as $payback) {
            $payback->datetime = Tools::toDateTime($payback->datetime);
        }

        $paybacks_sum = (new Payback())->where('ref_by', $this->user->id)->sum('ref_get');

        if (! $paybacks_sum) {
            $paybacks_sum = 0;
        }

        $invite_url = $_ENV['baseUrl'] . '/auth/register?code=' . $code;
        $rebate_ratio_per = Config::obtain('rebate_ratio') * 100;

        return $response->write($this->view()
            ->assign('paybacks', $paybacks)
            ->assign('invite_url', $invite_url)
            ->assign('paybacks_sum', $paybacks_sum)
            ->assign('rebate_ratio_per', $rebate_ratio_per)
            ->fetch('user/invite.tpl'));
    }

    public function checkin(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        if (! $_ENV['enable_checkin']) {
            return ResponseHelper::error($response, '暂时还不能签到');
        }

        if (Config::obtain('enable_checkin_captcha')) {
            $ret = Captcha::verify($request->getParams());

            if (! $ret) {
                return ResponseHelper::error($response, '系统无法接受你的验证结果，请刷新页面后重试');
            }
        }

        $checkin = $this->user->checkin();

        if (! $checkin['ok']) {
            return ResponseHelper::error($response, (string) $checkin['msg']);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => $checkin['msg'],
            'data' => [
                'last-checkin-time' => $this->user->lastCheckInTime(),
            ],
        ]);
    }

    public function switchThemeMode(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $user = $this->user;
        $user->is_dark_mode = $user->is_dark_mode === 1 ? 0 : 1;

        if (! $user->save()) {
            return ResponseHelper::error($response, '切换失败');
        }

        return $response->withHeader('HX-Refresh', 'true')->withJson([
            'ret' => 1,
            'msg' => '切换成功',
        ]);
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

    public function logout(ServerRequest $request, Response $response, array $args): Response
    {
        Auth::logout();

        return $response->withStatus(302)->withHeader('Location', '/');
    }
}
