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
use App\Models\Code;
use App\Models\Payback;
use App\Models\User;
use App\Services\Auth;
use App\Services\Config;
use App\Services\Factory;
use App\Utils\Countdown;
use App\Utils\Helper;
use App\Utils\Telegram;
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
        return $this->echoJson($response, $ret);
    }

    public function UseCode($request, $response, $args)
    {
        $accessToken = Helper::getParam($request, 'access_token');
        $storage = Factory::createTokenStorage();
        $token = $storage->get($accessToken);
        $user = User::find($token->userId);
        $code = Helper::getParam($request, 'code');
        $code = trim($code);

        if ($code == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $this->echoJson($response,$res);
        }

        $codemodel = Code::where('code', '=', $code)->where('isused', '=', 0)->first();
        if ($codemodel == null) {
            $res['ret'] = 0;
            $res['msg'] = '此充值码错误';
            return $this->echoJson($response,$res);
        }

        $codemodel->isused = 1;
        $codemodel->usedatetime = date('Y-m-d H:i:s');
        $codemodel->userid = $user->id;
        $codemodel->save();

        if ($codemodel->type == -1) {
            $user->money += $codemodel->number;
            $user->save();

            if ($user->ref_by != '' && $user->ref_by != 0 && $user->ref_by != null) {
                $gift_user = User::where('id', '=', $user->ref_by)->first();
                $gift_user->money += ($codemodel->number * (Config::get('code_payback') / 100));
                $gift_user->save();

                $Payback = new Payback();
                $Payback->total = $codemodel->number;
                $Payback->userid = $this->user->id;
                $Payback->ref_by = $this->user->ref_by;
                $Payback->ref_get = $codemodel->number * (Config::get('code_payback') / 100);
                $Payback->datetime = time();
                $Payback->save();
            }

            $res['ret'] = 1;
            $res['msg'] = '充值成功，充值的金额为' . $codemodel->number . '元。';

            if (Config::get('enable_donate') == 'true') {
                if ($this->user->is_hide == 1) {
                    Telegram::Send('姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 ' . $codemodel->number . ' 元呢~');
                } else {
                    Telegram::Send('姐姐姐姐，' . $this->user->user_name . ' 大老爷给我们捐了 ' . $codemodel->number . ' 元呢~');
                }
            }

            return $this->echoJson($response,$res);
        }

        if ($codemodel->type == 10001) {
            $user->transfer_enable += $codemodel->number * 1024 * 1024 * 1024;
            $user->save();
        }

        if ($codemodel->type == 10002) {
            if (time() > strtotime($user->expire_in)) {
                $user->expire_in = date('Y-m-d H:i:s', time() + $codemodel->number * 86400);
            } else {
                $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + $codemodel->number * 86400);
            }
            $user->save();
        }

        if ($codemodel->type >= 1 && $codemodel->type <= 10000) {
            if ($user->class == 0 || $user->class != $codemodel->type) {
                $user->class_expire = date('Y-m-d H:i:s', time());
                $user->save();
            }
            $user->class_expire = date('Y-m-d H:i:s', strtotime($user->class_expire) + $codemodel->number * 86400);
            $user->class = $codemodel->type;
            $user->save();
        }
    }
}
