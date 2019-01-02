<?php

//Thanks to http://blog.csdn.net/jollyjumper/article/details/9823047

namespace App\Controllers;

use App\Models\Link;
use App\Models\User;
use App\Models\Node;
use App\Models\Relay;
use App\Models\Smartline;
use App\Utils\ConfRender;
use App\Utils\Tools;
use App\Utils\URL;
use App\Services\Config;

/**
 *  HomeController
 */
class LinkController extends BaseController
{
    public function __construct()
    {
    }

    public static function GenerateRandomLink()
    {
        $i =0;
        for ($i = 0; $i < 10; $i++) {
            $token = Tools::genRandomChar(16);
            $Elink = Link::where("token", "=", $token)->first();
            if ($Elink == null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }

    public static function GenerateSSRSubCode($userid, $without_mu)
    {
        $Elink = Link::where("type", "=", 11)->where("userid", "=", $userid)->where("geo", $without_mu)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 11;
        $NLink->address = "";
        $NLink->port = 0;
        $NLink->ios = 0;
        $NLink->geo = $without_mu;
        $NLink->method = "";
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GetContent($request, $response, $args)
    {
        $token = $args['token'];

        //$builder->getPhrase();
        $Elink = Link::where("token", "=", $token)->first();
        if ($Elink == null) {
            return null;
        }

		if($Elink->type!=11){
			return null;
		}
		
		$user=User::where("id", $Elink->userid)->first();
        if ($user == null) {
            return null;
        }

        $mu = 0;
		if (isset($request->getQueryParams()["mu"])) {
			$mu = (int)$request->getQueryParams()["mu"];
        }

        $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.txt');
        $newResponse->getBody()->write(LinkController::GetSSRSub(User::where("id", "=", $Elink->userid)->first(), $mu));
        return $newResponse;
    }

    const V2RYA_MU = 2;
    const SSD_MU = 3;
    const CLASH_MU = 4;

    public static function GetSSRSub($user, $mu = 0)
    {
        if ($mu==0||$mu==1) {
            return Tools::base64_url_encode(URL::getAllUrl($user, $mu, 0));
        } 
		elseif ($mu == LinkController::V2RYA_MU){
            return Tools::base64_url_encode(URL::getAllVMessUrl($user));
        }
		elseif ($mu==LinkController::SSD_MU) {
			return URL::getAllSSDUrl($user);
		} elseif ($mu==LinkController::CLASH_MU) {
            // Clash
            $render = ConfRender::getTemplateRender();
            $confs = URL::getClashInfo($user);

            $render->assign('user', $user)->assign('confs', $confs)->assign('proxies', array_map(function ($conf) {
                return $conf['name'];
            }, $confs));

            return $render->fetch('clash.tpl');
        }
    }
}
