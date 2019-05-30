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
use App\Utils\Helper;

class ClientApiController extends BaseController
{
    public function GetAnnouncement($request, $response, $args)
    {
        $accessToken = Helper::getTokenFromReq($request);
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
            $accessToken = Helper::getTokenFromReq($request);
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
        $accessToken = Helper::getTokenFromReq($request);
        $storage = Factory::createTokenStorage();
        $token = $storage->get($accessToken);
        $user = User::find($token->userId);
        $ssr_sub_token = LinkController::GenerateSSRSubCode($user->id, 0);
        $mu = 0;
        if ($request->getQueryParams()['mu'] != '') {
            $mu = $request->getQueryParams()['mu'];
        }
        $res['ret'] = 1;
        $res['msg'] = 'ok';
        $res['data'] = Config::get('subUrl') . $ssr_sub_token . '?mu=' . $mu;
        return $this->echoJson($response, $res);
    }
}
