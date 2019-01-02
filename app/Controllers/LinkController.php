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

    public static function GetPcConf($user, $is_mu = 0, $is_ss = 0)
    {
    if ($is_ss==0) {
        $string='
            {
                "index" : 0,
                "random" : false,
                "sysProxyMode" : 0,
                "shareOverLan" : false,
                "bypassWhiteList" : false,
                "localPort" : 1080,
                "localAuthPassword" : "'.Tools::genRandomChar(26).'",
                "dns_server" : "",
                "reconnectTimes" : 4,
                "randomAlgorithm" : 0,
                "TTL" : 60,
                "connect_timeout" : 5,
                "proxyRuleMode" : 1,
                "proxyEnable" : false,
                "pacDirectGoProxy" : false,
                "proxyType" : 0,
                "proxyHost" : "",
                "proxyPort" : 0,
                "proxyAuthUser" : "",
                "proxyAuthPass" : "",
                "proxyUserAgent" : "",
                "authUser" : "",
                "authPass" : "",
                "autoBan" : false,
                "sameHostForSameTarget" : true,
                "keepVisitTime" : 180,
                "isHideTips" : true,
                "token" : {

                },
                "portMap" : {

                }
            }
        ';
    } else {
        $string='
            {
                "strategy": null,
                "index": 6,
                "global": false,
                "enabled": false,
                "shareOverLan": false,
                "isDefault": false,
                "localPort": 1080,
                "pacUrl": null,
                "useOnlinePac": false,
                "secureLocalPac": true,
                "availabilityStatistics": false,
                "autoCheckUpdate": false,
                "checkPreRelease": false,
                "isVerboseLogging": true,
                "logViewer": {
                "topMost": false,
                "wrapText": false,
                "toolbarShown": false,
                "Font": "Consolas, 8pt",
                "BackgroundColor": "Black",
                "TextColor": "White"
                },
                "proxy": {
                "useProxy": false,
                "proxyType": 0,
                "proxyServer": "",
                "proxyPort": 0,
                "proxyTimeout": 3
                },
                "hotkey": {
                "SwitchSystemProxy": "",
                "SwitchSystemProxyMode": "",
                "SwitchAllowLan": "",
                "ShowLogs": "",
                "ServerMoveUp": "",
                "ServerMoveDown": ""
                }
            }
        ';
    }

        $json=json_decode($string, true);
        $temparray=array();

        $items = URL::getAllItems($user, $is_mu, $is_ss);
        foreach($items as $item) {
            if ($is_ss==0) {
                array_push($temparray, array("remarks"=>$item['remark'],
                                            "server"=>$item['address'],
                                            "server_port"=>$item['port'],
                                            "method"=>$item['method'],
                                            "obfs"=>$item['obfs'],
                                            "obfsparam"=>$item['obfs_param'],
                                            "remarks_base64"=>base64_encode($item['remark']),
                                            "password"=>$item['passwd'],
                                            "tcp_over_udp"=>false,
                                            "udp_over_tcp"=>false,
                                            "group"=>$item['group'],
                                            "protocol"=>$item['protocol'],
                                            "protoparam"=>$item['protocol_param'],
                                            "protocolparam"=>$item['protocol_param'],
                                            "obfs_udp"=>false,
                                            "enable"=>true));
            } else {
                array_push($temparray, array("server"=>$item['address'],
                                            "server_port"=>$item['port'],
                                            "password"=>$item['passwd'],
                                            "method"=>$item['method'],
                                            "plugin"=>"obfs-local",
                                            "plugin_opts"=>str_replace(',',';',URL::getSurgeObfs($item)),
                                            "remarks"=>$item['remark'],
                                            "timeout"=>5));
            }
        }

        $json["configs"]=$temparray;
        return json_encode($json, JSON_PRETTY_PRINT);
    }	  

    const V2RYA_MU = 2;
    const SSD_MU = 3;
    const CLASH_MU = 4;

    public static function GetSSRSub($user, $mu = 0)
    {
        if ($mu==0||$mu==1) {
            return Tools::base64_url_encode(URL::getAllUrl($user, $mu, 0, 1));
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
