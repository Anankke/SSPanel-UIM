<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ann;
use App\Models\Config;
use App\Models\LoginIp;
use App\Models\Node;
use App\Models\OnlineLog;
use App\Services\Auth;
use App\Services\Captcha;
use App\Services\Reward;
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
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
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
    public function profile(ServerRequest $request, Response $response, array $args): ResponseInterface
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
    public function announcement(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $anns = (new Ann())->orderBy('date', 'desc')->get();

        return $response->write(
            $this->view()
                ->assign('anns', $anns)
                ->fetch('user/announcement.tpl')
        );
    }

    public function checkin(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        if (! Config::obtain('enable_checkin') || ! $this->user->isAbleToCheckin()) {
            return ResponseHelper::error($response, '暂时还不能签到');
        }

        if (Config::obtain('enable_checkin_captcha')) {
            $ret = Captcha::verify($request->getParams());

            if (! $ret) {
                return ResponseHelper::error($response, '系统无法接受你的验证结果，请刷新页面后重试');
            }
        }

        $traffic = Reward::issueCheckinReward($this->user->id);

        if (! $traffic) {
            return ResponseHelper::error($response, '签到失败');
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '获得了 ' . $traffic . 'MB 流量',
            'data' => [
                'last-checkin-time' => Tools::toDateTime(time()),
            ],
        ]);
    }

    public function switchThemeMode(ServerRequest $request, Response $response, array $args): ResponseInterface
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
    public function banned(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;

        return $response->write(
            $this->view()
                ->assign('banned_reason', $user->banned_reason)
                ->fetch('user/banned.tpl')
        );
    }

    public function logout(ServerRequest $request, Response $response, array $args): Response
    {
        Auth::logout();

        return $response->withStatus(302)->withHeader('Location', '/');
    }
}
