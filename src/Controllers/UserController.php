<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ann;
use App\Models\Config;
use App\Services\Analytics;
use App\Services\Auth;
use App\Services\Captcha;
use App\Services\Config\ClientConfig;
use App\Services\Reward;
use App\Services\Subscribe;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_encode;
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
        $traffic_logs = [];
        $class_expire_days = $this->user->class > 0 ?
            round((strtotime($this->user->class_expire) - time()) / 86400) : 0;
        $ann = (new Ann())->where('status', '>', 0)
            ->orderBy('status', 'desc')
            ->orderBy('sort')
            ->orderBy('date', 'desc')->first();

        if (Config::obtain('enable_checkin') &&
            Config::obtain('enable_checkin_captcha') &&
            $this->user->isAbleToCheckin()) {
            $captcha = Captcha::generate();
        }

        if (Config::obtain('traffic_log')) {
            $hourly_usage = Analytics::getUserTodayHourlyUsage($this->user->id);

            foreach ($hourly_usage as $hour => $usage) {
                $traffic_logs[] = Tools::bToMB((int) $usage);
            }
        }

        $universalSub = Subscribe::getUniversalSubLink($this->user);
        $r2Enabled = filter_var($_ENV['enable_r2_client_download'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
        $clientData = ClientConfig::getClients(
            $universalSub,
            $_ENV['appName'] ?? 'SSPanel',
            $r2Enabled
        );

        return $response->write(
            $this->view()
                ->assign('ann', $ann)
                ->assign('captcha', $captcha)
                ->assign('traffic_logs', json_encode($traffic_logs))
                ->assign('class_expire_days', $class_expire_days)
                ->assign('UniversalSub', $universalSub)
                ->assign('clientData', json_encode($clientData['clients']))
                ->assign('platformIcons', json_encode($clientData['icons']))
                ->assign('user_class', $this->user->class)
                ->assign('user_money', $this->user->money)
                ->assign('ip_limit', $this->user->node_iplimit)
                ->assign('speed_limit', $this->user->node_speedlimit)
                ->fetch('user/index.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function announcement(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $anns = (new Ann())->where('status', '>', 0)
            ->orderBy('status', 'desc')
            ->orderBy('sort')
            ->orderBy('date', 'desc')->get();

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

    /**
     * @throws Exception
     */
    public function banned(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('banned_reason', $this->user->banned_reason)
                ->fetch('user/banned.tpl')
        );
    }

    public function logout(ServerRequest $request, Response $response, array $args): Response
    {
        Auth::logout();

        return $response->withStatus(302)->withHeader('Location', '/');
    }
}
