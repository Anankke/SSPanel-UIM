<?php
/**
 * Created by PhpStorm.
 * User: kaguya
 * Date: 2018/06/30
 * Time: 21:40
 */

namespace App\Controllers\Client;


use App\Controllers\BaseController;
use App\Controllers\LinkController;
use App\Models\Ann;
use App\Models\User;
use App\Services\Auth;
use App\Services\Config;
use App\Services\Factory;
use App\Utils\Countdown;
use App\Utils\Helper;
use Whoops\Exception\ErrorException;

class ClientApiController extends BaseController
{
    public function GetAnnouncement($request, $response, $args)
    {
        $accessToken = Helper::getParam($request, 'access_token');
        $storage = Factory::createTokenStorage();
        $token = $storage->get($accessToken);
        $user = User::find($token->userId);
        $Anns = Ann::orderBy('date', 'desc')->first();
        $res['ret'] = 1;
        $res['msg'] = 'ok';
        $res['data'] = $Anns;
        return $this->echoJson($response, $res);
    }

    public function Redirect($request, $response, $args)
    {
        $user = Auth::getUser();
        $url = $request->getQueryParams()['target'];
        if (!$user->isLogin) {
            $accessToken = Helper::getParam($request, 'access_token');
            $storage = Factory::createTokenStorage();
            $token = $storage->get($accessToken);
            if ($token == null) {
                $res['ret'] = 0;
                $res['msg'] = 'token is null';
                return $this->echoJson($response, $res);
            }
            $user = User::find($token->userId);
            $time = 3600 * 24;
            Auth::login($user->id, $time);
        }
        return $response->withRedirect($url);
    }

    public function GetSubLink($request, $response, $args)
    {
        $accessToken = Helper::getParam($request, 'access_token');
        $storage = Factory::createTokenStorage();
        $token = $storage->get($accessToken);
        $user = User::find($token->userId);
        $ssr_sub_token = LinkController::GenerateSSRSubCode($user->id, 0);
        $mu = 0;
        $res['ret'] = 1;
        $res['msg'] = 'ok';
        $res['data'] = Config::get('subUrl') . $ssr_sub_token . '?mu=' . $mu;
        try {
            if ($request->getQueryParams()['mu'] != '') {
                $mu = $request->getQueryParams()['mu'];
            }
        } catch (ErrorException $e) {
            if (Config::get('mergeSub') == true) {
                $res['data'] = Config::get('subUrl') . $ssr_sub_token;
            }
        }
        return $this->echoJson($response, $res);
    }

    public function GetUserInfo($request, $response, $args)
    {
        $accessToken = Helper::getParam($request, 'access_token');
        $storage = Factory::createTokenStorage();
        $token = $storage->get($accessToken);
        $user = User::find($token->userId);
        $res['ret'] = 1;
        $res['msg'] = 'ok';
        if ($user->class == 0) {
            $ret['data']['level'] = 'VIP' . $user->class;
        } else {
            $ret['data']['level'] = '普通用户';
        }
        $ret['data']['money'] = $user->money;
        if ($user->node_connector != 0) {
            $ret['data']['online_count'] = $user->online_ip_count();
            $ret['data']['node_connector'] = $user->node_connector;
        } else {
            $ret['data']['online_count'] = $user->online_ip_count();
            $ret['data']['node_connector'] = '不限制';
        }
        if ($user->node_speedlimit != 0) {
            $ret['data']['node_speedlimit'] = $user->node_speedlimit . 'Mbps';
        } else {
            $ret['data']['node_speedlimit'] = '无限制';
        }
        if ($user->class_expire != "1989-06-04 00:05:00") {
            $ret['data']['level_expire'] = $user->class_expire;
        } else {
            $ret['data']['level_expire'] = '不过期';
        }
        $levelExpireCountdown = new Countdown($user->class_expire);
        $ret['data']['days_level_expire'] = $levelExpireCountdown->countdown();
        $accountExpireCountdown = new Countdown($user->expire_in);
        $ret['data']['days_level_expire'] = $accountExpireCountdown->countdown();
        if ($user->lastSsTime() != "从未使用喵") {
            $ret['data']['lastSsTime'] = $user->lastSsTime();
        } else {
            $ret['data']['lastSsTime'] = '从未使用';
        }
        $ret['data']['lastCheckInTime'] = $user->lastCheckInTime();
        $ret['data']['todayUsedTraffic'] = $user->TodayusedTraffic();
        $ret['data']['lastUsedTraffic'] = $user->LastusedTraffic();
        $ret['data']['unUsedTraffic'] = $user->unusedTraffic();
        return $this->echoJson($response,$ret);
    }
}
