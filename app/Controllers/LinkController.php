<?php

//Thanks to http://blog.csdn.net/jollyjumper/article/details/9823047

namespace App\Controllers;

use App\Models\Link;
use App\Models\User;
use App\Models\Node;
use App\Models\Relay;
use App\Models\Smartline;
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

    public static function GenerateCode($type, $address, $port, $ios, $userid)
    {
        $Elink = Link::where("type", "=", $type)->where("address", "=", $address)->where("port", "=", $port)->where("ios", "=", $ios)->where("userid", "=", $userid)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = $type;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = $ios;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }





    public static function GenerateApnCode($isp, $address, $port, $userid)
    {
        $Elink = Link::where("type", "=", 6)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("isp", "=", $isp)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 6;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 1;
        $NLink->isp = $isp;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }


    public static function GenerateSurgeCode($address, $port, $userid, $geo, $method)
    {
        $Elink = Link::where("type", "=", 0)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("geo", "=", $geo)->where("method", "=", $method)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 0;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 1;
        $NLink->geo = $geo;
        $NLink->method = $method;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GenerateIosCode($address, $port, $userid, $geo, $method)
    {
        $Elink = Link::where("type", "=", -1)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("geo", "=", $geo)->where("method", "=", $method)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = -1;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 1;
        $NLink->geo = $geo;
        $NLink->method = $method;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GenerateClashCode($address, $port, $userid, $geo, $method)
    {
        $Elink = Link::where("type", "=", -2)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("geo", "=", $geo)->where("method", "=", $method)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = -2;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 0;
        $NLink->geo = $geo;
        $NLink->method = $method;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GenerateAclCode($address, $port, $userid, $geo, $method)
    {
        $Elink = Link::where("type", "=", 9)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("geo", "=", $geo)->where("method", "=", $method)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 9;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 0;
        $NLink->geo = $geo;
        $NLink->method = $method;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GenerateRouterCode($userid, $without_mu)
    {
        $Elink = Link::where("type", "=", 10)->where("userid", "=", $userid)->where("geo", $without_mu)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 10;
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

        switch ($Elink->type) {
            case -2:
                $user=User::where("id", $Elink->userid)->first();
                if ($user == null) {
                    return null;
                }

                $is_ss = 1;
                if (isset($request->getQueryParams()["is_ss"])) {
                    $is_ss = $request->getQueryParams()["is_ss"];
                }

                $is_mu = 0;
                if (isset($request->getQueryParams()["is_mu"])) {
                    $is_mu = $request->getQueryParams()["is_mu"];
                }

                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename=config.ini');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetClashConf($user, $is_mu, $is_ss));
                return $newResponse;
            case -1:
                $user=User::where("id", $Elink->userid)->first();
                if ($user == null) {
                    return null;
                }

                $is_ss = 1;
                if (isset($request->getQueryParams()["is_ss"])) {
                    $is_ss = $request->getQueryParams()["is_ss"];
                }

                $is_mu = 0;
                if (isset($request->getQueryParams()["is_mu"])) {
                    $is_mu = $request->getQueryParams()["is_mu"];
                }

                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename=Online.conf');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetIosConf($user, $is_mu, $is_ss));
                return $newResponse;
            case 3:
                $type = "PROXY";
                break;
            case 7:
                $type = "PROXY";
                break;
            case 6:
                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.mobileconfig');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetApn($Elink->isp, $Elink->address, $Elink->port, User::where("id", "=", $Elink->userid)->first()->pac));
                return $newResponse;
            case 0:
                if ($Elink->geo==0) {
                    $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.conf');//->getBody()->write($builder->output());
                    $newResponse->getBody()->write(LinkController::GetSurge(User::where("id", "=", $Elink->userid)->first()->passwd, $Elink->method, $Elink->address, $Elink->port, User::where("id", "=", $Elink->userid)->first()->pac));
                    return $newResponse;
                } else {
                    $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.conf');//->getBody()->write($builder->output());
                    $newResponse->getBody()->write(LinkController::GetSurgeGeo(User::where("id", "=", $Elink->userid)->first()->passwd, $Elink->method, $Elink->address, $Elink->port));
                    return $newResponse;
                }
            case 8:
                if ($Elink->ios==0) {
                    $type = "SOCKS5";
                } else {
                    $type = "SOCKS";
                }
                break;
            case 9:
                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.acl');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetAcl(User::where("id", "=", $Elink->userid)->first()));
                return $newResponse;
            case 10:
                $user=User::where("id", $Elink->userid)->first();
                if ($user == null) {
                    return null;
                }

                $is_ss = 0;
                if (isset($request->getQueryParams()["is_ss"])) {
                    $is_ss = $request->getQueryParams()["is_ss"];
                }

                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.sh');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetRouter(User::where("id", "=", $Elink->userid)->first(), $Elink->geo, $is_ss));
                return $newResponse;
            case 11:
                $user=User::where("id", $Elink->userid)->first();
                if ($user == null) {
                    return null;
                }

                $max = 0;
                if (isset($request->getQueryParams()["max"])) {
                    $max = (int)$request->getQueryParams()["max"];
                }

                $mu = 0;
                if (isset($request->getQueryParams()["mu"])) {
                    $mu = (int)$request->getQueryParams()["mu"];
                }

                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.txt');
                $newResponse->getBody()->write(LinkController::GetSSRSub(User::where("id", "=", $Elink->userid)->first(), $mu, $max));
                return $newResponse;
            default:
                break;
        }
        $newResponse = $response->withHeader('Content-type', ' application/x-ns-proxy-autoconfig; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate');//->getBody()->write($builder->output());
        $newResponse->getBody()->write(LinkController::GetPac($type, $Elink->address, $Elink->port, User::where("id", "=", $Elink->userid)->first()->pac));
        return $newResponse;
    }


    public static function GetGfwlistJs($request, $response, $args)
    {
        $newResponse = $response->withHeader('Content-type', ' application/x-ns-proxy-autoconfig; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename=gfwlist.js');
        ;//->getBody()->write($builder->output());
        $newResponse->getBody()->write(LinkController::GetMacPac());
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
                                            "plugin"=>($item['obfs']=='plain')?'':'obfs-local',
                                            "plugin_opts"=>str_replace(',',';',URL::getSurgeObfs($item)),
                                            "remarks"=>$item['remark'],
                                            "timeout"=>5));
            }
        }

        $json["configs"]=$temparray;
        return json_encode($json, JSON_PRETTY_PRINT);
    }


    public static function GetIosConf($user, $is_mu = 0, $is_ss = 0)
    {
        $proxy_name="";
        $proxy_group="";

        $rules = file_get_contents("https://raw.githubusercontent.com/lhie1/black-hole/master/Rule.conf");

        $items = URL::getAllItems($user, $is_mu, $is_ss);
        foreach($items as $item) {
            $proxy_group .= $item['remark'].' = custom,'.$item['address'].','.$item['port'].','.$item['method'].','.$item['passwd'].',http://omgib13x8.bkt.clouddn.com/SSEncrypt.module,'.URL::getSurgeObfs($item).',udp-relay=true,tfo=true'."\n";
            $proxy_name .= ",".$item['remark'];
        }

        return '#!MANAGED-CONFIG '.Config::get('baseUrl').''.$_SERVER['REQUEST_URI'].'

[General]
// Auto
loglevel = notify
dns-server = system,1.2.4.8,80.80.80.80,80.80.81.81,1.1.1.1,1.0.0.1
skip-proxy = 127.0.0.1,192.168.0.0/16,10.0.0.0/8,172.16.0.0/12,100.64.0.0/10,17.0.0.0/8,localhost,*.local,*.crashlytics.com

// iOS
external-controller-access = lhie1@0.0.0.0:6170

allow-wifi-access = true

// macOS
interface = 0.0.0.0
socks-interface = 0.0.0.0
port = 8888
socks-port = 8889

enhanced-mode-by-rule = false

// Auto
exclude-simple-hostnames = true
ipv6 = true
replica = false

[Proxy]
DIRECT = direct
'.$proxy_group.'

[Proxy Group]
PROXY = select,AUTO'.$proxy_name.'
Domestic = select,DIRECT,PROXY
Others = select,PROXY,DIRECT
Apple = select,DIRECT,PROXY,AUTO
Netflix & TVB & Spotify & YouTube = select,PROXY'.$proxy_name.'
AUTO = url-test'.$proxy_name.',url = http://www.gstatic.com/generate_204,interval = 1200

[Rule]

'.$rules.'

';
    }

    public static function GetClashConf($user, $is_mu = 0, $is_ss = 0)
    {
        $proxy_name="";
        $proxy_group="";

        $items = URL::getAllItems($user, $is_mu, $is_ss);
        foreach($items as $item) {
            $proxy_group .= $item['remark'].' = '.'ss, '.$item['address'].', '.$item['port'].', '.strtoupper($item['method']).', '.$item['passwd']."\n";
            $proxy_name .= ", ".$item['remark'];
        }

        return '#!MANAGED-CONFIG '.Config::get('baseUrl').''.$_SERVER['REQUEST_URI'].'

[General]
port = 8888
socks-port = 8889
external-controller = 127.0.0.1:7892

[Proxy]
# name = ss, server, port, cipter, password
# vmess = vmess, 127.0.0.1, 1234, uuid, alterid(1), auto/aes-128-gcm/chacha20-poly1305/none, tls=true

DIRECT = direct
'.$proxy_group.'

[Proxy Group]
# url-test select which proxy will be used by benchmarking speed to a URL.
# name = url-test, [proxys], url, interval(second)

PROXY = select'.$proxy_name.'

[Rule]
# Apple服务优化
# 其他服务
USER-AGENT,com.apple.appstored*,DIRECT
DOMAIN-SUFFIX,mzstatic.com,DIRECT
DOMAIN,gs.apple.com,Proxy
DOMAIN-SUFFIX,icloud-content.com,DIRECT
#DOMAIN,e.crashlytics.com,REJECT
# 通用部分
DOMAIN,beta.itunes.apple.com,Proxy
DOMAIN-SUFFIX,apple.com,DIRECT
DOMAIN-SUFFIX,icloud.com,DIRECT
# 国内直连
DOMAIN-KEYWORD,-cn,DIRECT
DOMAIN-SUFFIX,cn,DIRECT
DOMAIN-SUFFIX,126.com,DIRECT
DOMAIN-SUFFIX,126.net,DIRECT
DOMAIN-SUFFIX,127.net,DIRECT
DOMAIN-SUFFIX,163.com,DIRECT
DOMAIN-SUFFIX,360.cn,DIRECT
DOMAIN-SUFFIX,360buyimg.com,DIRECT
DOMAIN-SUFFIX,36kr.com,DIRECT
DOMAIN-SUFFIX,acfun.tv,DIRECT
DOMAIN-SUFFIX,air-matters.com,DIRECT
DOMAIN-SUFFIX,aixifan.com,DIRECT
DOMAIN-SUFFIX,alicdn.com,DIRECT
DOMAIN-SUFFIX,alipay.com,DIRECT
DOMAIN-SUFFIX,alipayobjects.com,DIRECT
DOMAIN-SUFFIX,amap.com,DIRECT
DOMAIN-SUFFIX,autonavi.com,DIRECT
DOMAIN-SUFFIX,baidu.com,DIRECT
DOMAIN-SUFFIX,baidupcs.com,DIRECT
DOMAIN-SUFFIX,bdimg.com,DIRECT
DOMAIN-SUFFIX,bdstatic.com,DIRECT
DOMAIN-SUFFIX,bilibili.com,DIRECT
DOMAIN-SUFFIX,caiyunapp.com,DIRECT
DOMAIN-SUFFIX,clouddn.com,DIRECT
DOMAIN-SUFFIX,cnbeta.com,DIRECT
DOMAIN-SUFFIX,cnbetacdn.com,DIRECT
DOMAIN-SUFFIX,cootekservice.com,DIRECT
DOMAIN-SUFFIX,csdn.net,DIRECT
DOMAIN-SUFFIX,csdnimg.cn,DIRECT
DOMAIN-SUFFIX,ctrip.com,DIRECT
DOMAIN-SUFFIX,dgtle.com,DIRECT
DOMAIN-SUFFIX,dianping.com,DIRECT
DOMAIN-SUFFIX,douban.com,DIRECT
DOMAIN-SUFFIX,doubanio.com,DIRECT
DOMAIN-SUFFIX,duokan.com,DIRECT
DOMAIN-SUFFIX,easou.com,DIRECT
DOMAIN-SUFFIX,ele.me,DIRECT
DOMAIN-SUFFIX,feng.com,DIRECT
DOMAIN-SUFFIX,fir.im,DIRECT
DOMAIN-SUFFIX,frdic.com,DIRECT
DOMAIN-SUFFIX,g-cores.com,DIRECT
DOMAIN-SUFFIX,godic.net,DIRECT
DOMAIN-SUFFIX,gtimg.com,DIRECT
DOMAIN-SUFFIX,hongxiu.com,DIRECT
DOMAIN-SUFFIX,hxcdn.net,DIRECT
DOMAIN-SUFFIX,iciba.com,DIRECT
DOMAIN-SUFFIX,ifeng.com,DIRECT
DOMAIN-SUFFIX,ifengimg.com,DIRECT
DOMAIN-SUFFIX,images-amazon.com,DIRECT
DOMAIN-SUFFIX,ipip.net,DIRECT
DOMAIN-SUFFIX,iqiyi.com,DIRECT
DOMAIN-SUFFIX,jd.com,DIRECT
DOMAIN-SUFFIX,jianshu.com,DIRECT
DOMAIN-SUFFIX,knewone.com,DIRECT
DOMAIN-SUFFIX,le.com,DIRECT
DOMAIN-SUFFIX,lecloud.com,DIRECT
DOMAIN-SUFFIX,lemicp.com,DIRECT
DOMAIN-SUFFIX,luoo.net,DIRECT
DOMAIN-SUFFIX,meituan.com,DIRECT
DOMAIN-SUFFIX,meituan.net,DIRECT
DOMAIN-SUFFIX,mi.com,DIRECT
DOMAIN-SUFFIX,miaopai.com,DIRECT
DOMAIN-SUFFIX,miui.com,DIRECT
DOMAIN-SUFFIX,miwifi.com,DIRECT
DOMAIN-SUFFIX,mob.com,DIRECT
DOMAIN-SUFFIX,netease.com,DIRECT
DOMAIN-SUFFIX,oschina.net,DIRECT
DOMAIN-SUFFIX,ppsimg.com,DIRECT
DOMAIN-SUFFIX,pstatp.com,DIRECT
DOMAIN-SUFFIX,qcloud.com,DIRECT
DOMAIN-SUFFIX,qdaily.com,DIRECT
DOMAIN-SUFFIX,qdmm.com,DIRECT
DOMAIN-SUFFIX,qhimg.com,DIRECT
DOMAIN-SUFFIX,qidian.com,DIRECT
DOMAIN-SUFFIX,qihucdn.com,DIRECT
DOMAIN-SUFFIX,qiniu.com,DIRECT
DOMAIN-SUFFIX,qiniucdn.com,DIRECT
DOMAIN-SUFFIX,qiyipic.com,DIRECT
DOMAIN-SUFFIX,qq.com,DIRECT
DOMAIN-SUFFIX,qqurl.com,DIRECT
DOMAIN-SUFFIX,rarbg.is,DIRECT
DOMAIN-SUFFIX,rr.tv,DIRECT
DOMAIN-SUFFIX,ruguoapp.com,DIRECT
DOMAIN-SUFFIX,segmentfault.com,DIRECT
DOMAIN-SUFFIX,sinaapp.com,DIRECT
DOMAIN-SUFFIX,sogou.com,DIRECT
DOMAIN-SUFFIX,sogoucdn.com,DIRECT
DOMAIN-SUFFIX,sohu.com,DIRECT
DOMAIN-SUFFIX,soku.com,DIRECT
DOMAIN-SUFFIX,sspai.com,DIRECT
DOMAIN-SUFFIX,suning.com,DIRECT
DOMAIN-SUFFIX,taobao.com,DIRECT
DOMAIN-SUFFIX,tenpay.com,DIRECT
DOMAIN-SUFFIX,tmall.com,DIRECT
DOMAIN-SUFFIX,tudou.com,DIRECT
DOMAIN-SUFFIX,umetrip.com,DIRECT
DOMAIN-SUFFIX,upaiyun.com,DIRECT
DOMAIN,update.microsoft.com,DIRECT
DOMAIN-SUFFIX,upyun.com,DIRECT
DOMAIN-SUFFIX,veryzhun.com,DIRECT
DOMAIN-SUFFIX,weibo.com,DIRECT
DOMAIN-SUFFIX,weiphone.net,DIRECT
DOMAIN-SUFFIX,xiami.com,DIRECT
DOMAIN-SUFFIX,xiaomicp.com,DIRECT
DOMAIN-SUFFIX,ximalaya.com,DIRECT
DOMAIN-SUFFIX,xmcdn.com,DIRECT
DOMAIN-SUFFIX,xunlei.com,DIRECT
DOMAIN-SUFFIX,yhd.com,DIRECT
DOMAIN-SUFFIX,yihaodianimg.com,DIRECT
DOMAIN-SUFFIX,yinxiang.com,DIRECT
DOMAIN-SUFFIX,ykimg.com,DIRECT
DOMAIN-SUFFIX,youdao.com,DIRECT
DOMAIN-SUFFIX,youku.com,DIRECT
DOMAIN-SUFFIX,zealer.com,DIRECT
DOMAIN-SUFFIX,zhihu.com,DIRECT
DOMAIN-SUFFIX,zhimg.com,DIRECT
# 纠正 GFW 的 DNS 污染问题
DOMAIN-KEYWORD,google,Proxy,force-remote-dns
DOMAIN-SUFFIX,gstatic.com,Proxy,force-remote-dns
DOMAIN-KEYWORD,gmail,Proxy,force-remote-dns
DOMAIN-KEYWORD,youtube,Proxy,force-remote-dns
DOMAIN-KEYWORD,facebook,Proxy,force-remote-dns
DOMAIN-SUFFIX,fb.me,Proxy,force-remote-dns
DOMAIN-SUFFIX,fbcdn.net,Proxy,force-remote-dns
DOMAIN-KEYWORD,twitter,Proxy,force-remote-dns
DOMAIN-KEYWORD,instagram,Proxy,force-remote-dns
DOMAIN-KEYWORD,dropbox,Proxy,force-remote-dns
DOMAIN-SUFFIX,twimg.com,Proxy,force-remote-dns
DOMAIN-KEYWORD,blogspot,Proxy,force-remote-dns
DOMAIN-SUFFIX,youtu.be,Proxy,force-remote-dns
DOMAIN-KEYWORD,whatsapp,Proxy,force-remote-dns
# 常见广告域名屏蔽
DOMAIN-KEYWORD,adsmogo,REJECT
DOMAIN-SUFFIX,acs86.com,REJECT
DOMAIN-SUFFIX,adcome.cn,REJECT
DOMAIN-SUFFIX,adinfuse.com,REJECT
DOMAIN-SUFFIX,admaster.com.cn,REJECT
DOMAIN-SUFFIX,admob.com,REJECT
DOMAIN-SUFFIX,adsage.cn,REJECT
DOMAIN-SUFFIX,adsage.com,REJECT
DOMAIN-SUFFIX,adsmogo.org,REJECT
DOMAIN-SUFFIX,ads.mobclix.com,REJECT
DOMAIN-SUFFIX,adview.cn,REJECT
DOMAIN-SUFFIX,adwhirl.com,REJECT
DOMAIN-SUFFIX,adwo.com,REJECT
DOMAIN-SUFFIX,appads.com,REJECT
DOMAIN-KEYWORD,domob,REJECT
DOMAIN-SUFFIX,doubleclick.net,REJECT
DOMAIN-KEYWORD,duomeng,REJECT
DOMAIN-SUFFIX,googeadsserving.cn,REJECT
DOMAIN-SUFFIX,guomob.com,REJECT
DOMAIN-SUFFIX,immob.cn,REJECT
DOMAIN-SUFFIX,inmobi.com,REJECT
DOMAIN-SUFFIX,mobads.baidu.com,REJECT
DOMAIN-SUFFIX,mobads-logs.baidu.com,REJECT
DOMAIN-SUFFIX,smartadserver.com,REJECT
DOMAIN-SUFFIX,tapjoyads.com,REJECT
DOMAIN-KEYWORD,umeng,REJECT
DOMAIN-SUFFIX,umtrack.com,REJECT
DOMAIN-SUFFIX,uyunad.com,REJECT
DOMAIN-SUFFIX,youmi.net,REJECT
# 此部分为最常访问但被 GFW 屏蔽的网站（若有特殊需要，请参考项目的 GFWList 列表）
DOMAIN-SUFFIX,2o7.net,Proxy
DOMAIN-SUFFIX,4sqi.net,Proxy
DOMAIN-SUFFIX,9to5mac.com,Proxy
DOMAIN-SUFFIX,abpchina.org,Proxy
DOMAIN-SUFFIX,adblockplus.org,Proxy
DOMAIN-SUFFIX,adobe.com,Proxy
DOMAIN-SUFFIX,adobedtm.com,Proxy
DOMAIN-SUFFIX,aerisapi.com,Proxy
DOMAIN-SUFFIX,akamaihd.net,Proxy
DOMAIN-SUFFIX,alfredapp.com,Proxy
DOMAIN-SUFFIX,amazon.com,Proxy
DOMAIN-SUFFIX,amazonaws.com,Proxy
DOMAIN-SUFFIX,amplitude.com,Proxy
DOMAIN-SUFFIX,ampproject.com,Proxy
DOMAIN-SUFFIX,ampproject.net,Proxy
DOMAIN-SUFFIX,ampproject.org,Proxy
DOMAIN-SUFFIX,android.com,Proxy
DOMAIN-SUFFIX,angularjs.org,Proxy
DOMAIN-SUFFIX,aolcdn.com,Proxy
DOMAIN-SUFFIX,apkpure.com,Proxy
DOMAIN-SUFFIX,apple-dns.net,Proxy
DOMAIN-SUFFIX,appledaily.com,Proxy
DOMAIN-SUFFIX,appledaily.com.tw,Proxy
DOMAIN-SUFFIX,appledailytw.com,Proxy
DOMAIN-SUFFIX,appshopper.com,Proxy
DOMAIN-SUFFIX,appsto.re,Proxy
DOMAIN-SUFFIX,arcgis.com,Proxy
DOMAIN-SUFFIX,archive.org,Proxy
DOMAIN-SUFFIX,armorgames.com,Proxy
DOMAIN-SUFFIX,aspnetcdn.com,Proxy
DOMAIN-SUFFIX,att.com,Proxy
DOMAIN-SUFFIX,awsstatic.com,Proxy
DOMAIN-SUFFIX,azureedge.net,Proxy
DOMAIN-SUFFIX,azurewebsites.net,Proxy
DOMAIN-SUFFIX,bing.com,Proxy
DOMAIN-SUFFIX,bintray.com,Proxy
DOMAIN-SUFFIX,bit.com,Proxy
DOMAIN-SUFFIX,bit.ly,Proxy
DOMAIN-SUFFIX,bitbucket.org,Proxy
DOMAIN-SUFFIX,bjango.com,Proxy
DOMAIN-SUFFIX,bkrtx.com,Proxy
DOMAIN-SUFFIX,blog.com,Proxy
DOMAIN-SUFFIX,blogcdn.com,Proxy
DOMAIN-SUFFIX,blogger.com,Proxy
DOMAIN-SUFFIX,blogsmithmedia.com,Proxy
DOMAIN-SUFFIX,blogspot.com,Proxy
DOMAIN-SUFFIX,blogspot.hk,Proxy
DOMAIN-SUFFIX,bloomberg.com,Proxy
DOMAIN-SUFFIX,box.com,Proxy
DOMAIN-SUFFIX,box.net,Proxy
DOMAIN-SUFFIX,cachefly.net,Proxy
DOMAIN-SUFFIX,chromium.org,Proxy
DOMAIN-SUFFIX,cl.ly,Proxy
DOMAIN-SUFFIX,cloudflare.com,Proxy
DOMAIN-SUFFIX,cloudfront.net,Proxy
DOMAIN-SUFFIX,cloudmagic.com,Proxy
DOMAIN-SUFFIX,cmail19.com,Proxy
DOMAIN-SUFFIX,cnet.com,Proxy
DOMAIN-SUFFIX,cocoapods.org,Proxy
DOMAIN-SUFFIX,comodoca.com,Proxy
DOMAIN-SUFFIX,content.office.net,Proxy
DOMAIN-SUFFIX,crashlytics.com,Proxy
DOMAIN-SUFFIX,culturedcode.com,Proxy
DOMAIN-SUFFIX,d.pr,Proxy
DOMAIN-SUFFIX,danilo.to,Proxy
DOMAIN-SUFFIX,dayone.me,Proxy
DOMAIN-SUFFIX,db.tt,Proxy
DOMAIN-SUFFIX,deskconnect.com,Proxy
DOMAIN-SUFFIX,digicert.com,Proxy
DOMAIN-SUFFIX,disq.us,Proxy
DOMAIN-SUFFIX,disqus.com,Proxy
DOMAIN-SUFFIX,disquscdn.com,Proxy
DOMAIN-SUFFIX,dnsimple.com,Proxy
DOMAIN-SUFFIX,docker.com,Proxy
DOMAIN-SUFFIX,dribbble.com,Proxy
DOMAIN-SUFFIX,droplr.com,Proxy
DOMAIN-SUFFIX,duckduckgo.com,Proxy
DOMAIN-SUFFIX,dueapp.com,Proxy
DOMAIN-SUFFIX,dytt8.net,Proxy
DOMAIN-SUFFIX,edgecastcdn.net,Proxy
DOMAIN-SUFFIX,edgekey.net,Proxy
DOMAIN-SUFFIX,edgesuite.net,Proxy
DOMAIN-SUFFIX,engadget.com,Proxy
DOMAIN-SUFFIX,entrust.net,Proxy
DOMAIN-SUFFIX,eurekavpt.com,Proxy
DOMAIN-SUFFIX,evernote.com,Proxy
DOMAIN-SUFFIX,fabric.io,Proxy
DOMAIN-SUFFIX,fastly.net,Proxy
DOMAIN-SUFFIX,fc2.com,Proxy
DOMAIN-SUFFIX,feedburner.com,Proxy
DOMAIN-SUFFIX,feedly.com,Proxy
DOMAIN-SUFFIX,feedsportal.com,Proxy
DOMAIN-SUFFIX,fiftythree.com,Proxy
DOMAIN-SUFFIX,firebaseio.com,Proxy
DOMAIN-SUFFIX,flexibits.com,Proxy
DOMAIN-SUFFIX,flickr.com,Proxy
DOMAIN-SUFFIX,flipboard.com,Proxy
DOMAIN-SUFFIX,g.co,Proxy
DOMAIN-SUFFIX,gabia.net,Proxy
DOMAIN-SUFFIX,geni.us,Proxy
DOMAIN-SUFFIX,gfx.ms,Proxy
DOMAIN-SUFFIX,ggpht.com,Proxy
DOMAIN-SUFFIX,ghostnoteapp.com,Proxy
DOMAIN-SUFFIX,git.io,Proxy
DOMAIN-SUFFIX,github.com,Proxy
DOMAIN-SUFFIX,github.io,Proxy
DOMAIN-SUFFIX,githubapp.com,Proxy
DOMAIN-SUFFIX,githubusercontent.com,Proxy
DOMAIN-SUFFIX,globalsign.com,Proxy
DOMAIN-SUFFIX,gmodules.com,Proxy
DOMAIN-SUFFIX,godaddy.com,Proxy
DOMAIN-SUFFIX,golang.org,Proxy
DOMAIN-SUFFIX,gongm.in,Proxy
DOMAIN-SUFFIX,goo.gl,Proxy
DOMAIN-SUFFIX,goodreaders.com,Proxy
DOMAIN-SUFFIX,goodreads.com,Proxy
DOMAIN-SUFFIX,gravatar.com,Proxy
DOMAIN-SUFFIX,gstatic.com,Proxy
DOMAIN-SUFFIX,gvt0.com,Proxy
DOMAIN-SUFFIX,hockeyapp.net,Proxy
DOMAIN-SUFFIX,hotmail.com,Proxy
DOMAIN-SUFFIX,icons8.com,Proxy
DOMAIN-SUFFIX,ift.tt,Proxy
DOMAIN-SUFFIX,ifttt.com,Proxy
DOMAIN-SUFFIX,imageshack.us,Proxy
DOMAIN-SUFFIX,img.ly,Proxy
DOMAIN-SUFFIX,imgur.com,Proxy
DOMAIN-SUFFIX,imore.com,Proxy
DOMAIN-SUFFIX,instapaper.com,Proxy
DOMAIN-SUFFIX,ipn.li,Proxy
DOMAIN-SUFFIX,is.gd,Proxy
DOMAIN-SUFFIX,issuu.com,Proxy
DOMAIN-SUFFIX,itgonglun.com,Proxy
DOMAIN-SUFFIX,itun.es,Proxy
DOMAIN-SUFFIX,ixquick.com,Proxy
DOMAIN-SUFFIX,j.mp,Proxy
DOMAIN-SUFFIX,js.revsci.net,Proxy
DOMAIN-SUFFIX,jshint.com,Proxy
DOMAIN-SUFFIX,jtvnw.net,Proxy
DOMAIN-SUFFIX,justgetflux.com,Proxy
DOMAIN-SUFFIX,kat.cr,Proxy
DOMAIN-SUFFIX,klip.me,Proxy
DOMAIN-SUFFIX,libsyn.com,Proxy
DOMAIN-SUFFIX,licdn.com,Proxy
DOMAIN-SUFFIX,linkedin.com,Proxy
DOMAIN-SUFFIX,linode.com,Proxy
DOMAIN-SUFFIX,lithium.com,Proxy
DOMAIN-SUFFIX,littlehj.com,Proxy
DOMAIN-SUFFIX,live.com,Proxy
DOMAIN-SUFFIX,live.net,Proxy
DOMAIN-SUFFIX,livefilestore.com,Proxy
DOMAIN-SUFFIX,llnwd.net,Proxy
DOMAIN-SUFFIX,macid.co,Proxy
DOMAIN-SUFFIX,macromedia.com,Proxy
DOMAIN-SUFFIX,macrumors.com,Proxy
DOMAIN-SUFFIX,mashable.com,Proxy
DOMAIN-SUFFIX,mathjax.org,Proxy
DOMAIN-SUFFIX,medium.com,Proxy
DOMAIN-SUFFIX,mega.co.nz,Proxy
DOMAIN-SUFFIX,mega.nz,Proxy
DOMAIN-SUFFIX,megaupload.com,Proxy
DOMAIN-SUFFIX,microsoft.com,Proxy
DOMAIN-SUFFIX,microsofttranslator.com,Proxy
DOMAIN-SUFFIX,mindnode.com,Proxy
DOMAIN-SUFFIX,mobile01.com,Proxy
DOMAIN-SUFFIX,modmyi.com,Proxy
DOMAIN-SUFFIX,msedge.net,Proxy
DOMAIN-SUFFIX,myfontastic.com,Proxy
DOMAIN-SUFFIX,name.com,Proxy
DOMAIN-SUFFIX,nextmedia.com,Proxy
DOMAIN-SUFFIX,nsstatic.net,Proxy
DOMAIN-SUFFIX,nssurge.com,Proxy
DOMAIN-SUFFIX,nyt.com,Proxy
DOMAIN-SUFFIX,nytimes.com,Proxy
DOMAIN-SUFFIX,office365.com,Proxy
DOMAIN-SUFFIX,omnigroup.com,Proxy
DOMAIN-SUFFIX,onedrive.com,Proxy
DOMAIN-SUFFIX,onenote.com,Proxy
DOMAIN-SUFFIX,ooyala.com,Proxy
DOMAIN-SUFFIX,openvpn.net,Proxy
DOMAIN-SUFFIX,openwrt.org,Proxy
DOMAIN-SUFFIX,orkut.com,Proxy
DOMAIN-SUFFIX,osxdaily.com,Proxy
DOMAIN-SUFFIX,outlook.com,Proxy
DOMAIN-SUFFIX,ow.ly,Proxy
DOMAIN-SUFFIX,paddleapi.com,Proxy
DOMAIN-SUFFIX,parallels.com,Proxy
DOMAIN-SUFFIX,parse.com,Proxy
DOMAIN-SUFFIX,pdfexpert.com,Proxy
DOMAIN-SUFFIX,periscope.tv,Proxy
DOMAIN-SUFFIX,pinboard.in,Proxy
DOMAIN-SUFFIX,pinterest.com,Proxy
DOMAIN-SUFFIX,pixelmator.com,Proxy
DOMAIN-SUFFIX,playpcesor.com,Proxy
DOMAIN-SUFFIX,playstation.com,Proxy
DOMAIN-SUFFIX,playstation.com.hk,Proxy
DOMAIN-SUFFIX,playstation.net,Proxy
DOMAIN-SUFFIX,playstationnetwork.com,Proxy
DOMAIN-SUFFIX,pushwoosh.com,Proxy
DOMAIN-SUFFIX,rime.im,Proxy
DOMAIN-SUFFIX,servebom.com,Proxy
DOMAIN-SUFFIX,sfx.ms,Proxy
DOMAIN-SUFFIX,shadowsocks.org,Proxy
DOMAIN-SUFFIX,sharethis.com,Proxy
DOMAIN-SUFFIX,shazam.com,Proxy
DOMAIN-SUFFIX,skype.com,Proxy
DOMAIN-SUFFIX,slack-edge.com,Proxy
DOMAIN-SUFFIX,slack.com,Proxy
DOMAIN-SUFFIX,slack-msgs.com,Proxy
DOMAIN-SUFFIX,smartdnsProxy.com,Proxy
DOMAIN-SUFFIX,smartmailcloud.com,Proxy
DOMAIN-SUFFIX,sndcdn.com,Proxy
DOMAIN-SUFFIX,sony.com,Proxy
DOMAIN-SUFFIX,sony.com.hk,Proxy
DOMAIN-SUFFIX,sonyentertainmentnetwork.com,Proxy
DOMAIN-SUFFIX,soundcloud.com,Proxy
DOMAIN-SUFFIX,sourceforge.net,Proxy
DOMAIN-SUFFIX,speedtest.net,Proxy
DOMAIN-SUFFIX,spotify.com,Proxy
DOMAIN-SUFFIX,squarespace.com,Proxy
DOMAIN-SUFFIX,sstatic.net,Proxy
DOMAIN-SUFFIX,st.luluku.pw,Proxy
DOMAIN-SUFFIX,stackoverflow.com,Proxy
DOMAIN-SUFFIX,startpage.com,Proxy
DOMAIN-SUFFIX,staticflickr.com,Proxy
DOMAIN-SUFFIX,surge.run,Proxy
DOMAIN-SUFFIX,symauth.com,Proxy
DOMAIN-SUFFIX,symcb.com,Proxy
DOMAIN-SUFFIX,symcd.com,Proxy
DOMAIN-SUFFIX,tapbots.com,Proxy
DOMAIN-SUFFIX,tapbots.net,Proxy
DOMAIN-SUFFIX,tdesktop.com,Proxy
DOMAIN-SUFFIX,techcrunch.com,Proxy
DOMAIN-SUFFIX,techsmith.com,Proxy
DOMAIN-SUFFIX,thepiratebay.org,Proxy
DOMAIN-SUFFIX,theverge.com,Proxy
DOMAIN-SUFFIX,time.com,Proxy
DOMAIN-SUFFIX,timeinc.net,Proxy
DOMAIN-SUFFIX,tiny.cc,Proxy
DOMAIN-SUFFIX,tinypic.com,Proxy
DOMAIN-SUFFIX,tmblr.co,Proxy
DOMAIN-SUFFIX,todoist.com,Proxy
DOMAIN-SUFFIX,trello.com,Proxy
DOMAIN-SUFFIX,trustasiassl.com,Proxy
DOMAIN-SUFFIX,tumblr.co,Proxy
DOMAIN-SUFFIX,tumblr.com,Proxy
DOMAIN-SUFFIX,tweetdeck.com,Proxy
DOMAIN-SUFFIX,tweetmarker.net,Proxy
DOMAIN-SUFFIX,twitch.tv,Proxy
DOMAIN-SUFFIX,txmblr.com,Proxy
DOMAIN-SUFFIX,typekit.net,Proxy
DOMAIN-SUFFIX,ubertags.com,Proxy
DOMAIN-SUFFIX,ublock.org,Proxy
DOMAIN-SUFFIX,ubnt.com,Proxy
DOMAIN-SUFFIX,ulyssesapp.com,Proxy
DOMAIN-SUFFIX,urchin.com,Proxy
DOMAIN-SUFFIX,usertrust.com,Proxy
DOMAIN-SUFFIX,v.gd,Proxy
DOMAIN-SUFFIX,v2ex.co,DIRECT
DOMAIN-SUFFIX,v2ex.com,DIRECT
DOMAIN-SUFFIX,vimeo.com,Proxy
DOMAIN-SUFFIX,vimeocdn.com,Proxy
DOMAIN-SUFFIX,vine.co,Proxy
DOMAIN-SUFFIX,vivaldi.com,Proxy
DOMAIN-SUFFIX,vox-cdn.com,Proxy
DOMAIN-SUFFIX,vsco.co,Proxy
DOMAIN-SUFFIX,vultr.com,Proxy
DOMAIN-SUFFIX,w.org,Proxy
DOMAIN-SUFFIX,w3schools.com,Proxy
DOMAIN-SUFFIX,weather.com,Proxy
DOMAIN-SUFFIX,webtype.com,Proxy
DOMAIN-SUFFIX,wikiwand.com,Proxy
DOMAIN-SUFFIX,wikileaks.org,Proxy
DOMAIN-SUFFIX,wikimedia.org,Proxy
DOMAIN-SUFFIX,wikipedia.com,Proxy
DOMAIN-SUFFIX,wikipedia.org,Proxy
DOMAIN-SUFFIX,windows.com,Proxy
DOMAIN-SUFFIX,windows.net,Proxy
DOMAIN-SUFFIX,wordpress.com,Proxy
DOMAIN-SUFFIX,workflowy.com,Proxy
DOMAIN-SUFFIX,wp.com,Proxy
DOMAIN-SUFFIX,wsj.com,Proxy
DOMAIN-SUFFIX,wsj.net,Proxy
DOMAIN-SUFFIX,xda-developers.com,Proxy
DOMAIN-SUFFIX,xeeno.com,Proxy
DOMAIN-SUFFIX,xiti.com,Proxy
DOMAIN-SUFFIX,yahoo.com,Proxy
DOMAIN-SUFFIX,yimg.com,Proxy
DOMAIN-SUFFIX,ying.com,Proxy
DOMAIN-SUFFIX,yoyo.org,Proxy
DOMAIN-SUFFIX,ytimg.com,Proxy
#此部分网站没有被 GFW 封锁，但使用代理会获得更快的速度，请选择性添加
#** 千万不要忘记最后一行的规则 **
#A
DOMAIN-SUFFIX,amazon.com,Proxy
DOMAIN-SUFFIX,amazonaws.com,Proxy
DOMAIN-SUFFIX,archive.org,Proxy
DOMAIN-SUFFIX,archive.is,Proxy
DOMAIN-SUFFIX,archives.gov,Proxy
DOMAIN-SUFFIX,appdownloader.net,Proxy
DOMAIN-SUFFIX,apk-dl.com,Proxy
DOMAIN-SUFFIX,apkfind.com,Proxy
DOMAIN-SUFFIX,apkpure.com,Proxy
DOMAIN-SUFFIX,apigee.com,Proxy
DOMAIN-SUFFIX,aol.com,Proxy
DOMAIN-SUFFIX,anthonycalzadilla.com,Proxy
DOMAIN-SUFFIX,android-x86.org,Proxy
DOMAIN-SUFFIX,ancsconf.org,Proxy
DOMAIN-SUFFIX,apkpure.com,Proxy
DOMAIN-SUFFIX,allconnected.co,Proxy
DOMAIN-SUFFIX,apkleecher.com,Proxy
DOMAIN-SUFFIX,appsonplaystore.com,Proxy
#B
DOMAIN-SUFFIX,books.com.tw,Proxy
DOMAIN-SUFFIX,bloomberg.com,Proxy
DOMAIN-SUFFIX,bloglovin.com,Proxy
DOMAIN-SUFFIX,bitshare.com,Proxy
DOMAIN-SUFFIX,bitcointalk.org,Proxy
DOMAIN-SUFFIX,bit.do,Proxy
DOMAIN-SUFFIX,bit.ly,Proxy
DOMAIN-SUFFIX,bigsound.org,Proxy
DOMAIN-SUFFIX,bbtoystore.com,Proxy
DOMAIN-SUFFIX,boxun.com,Proxy
DOMAIN-SUFFIX,bandwagonhost.com,Proxy
#C
DOMAIN-SUFFIX,cnn.com,Proxy
DOMAIN-SUFFIX,cdninstagram.com,Proxy
DOMAIN-SUFFIX,cbc.ca,Proxy
DOMAIN-SUFFIX,census.gov,Proxy
DOMAIN-SUFFIX,cloudfront.net,Proxy
DOMAIN-SUFFIX,cn-proxy.com,Proxy
DOMAIN-SUFFIX,cccat.cc,Proxy
#D
DOMAIN-SUFFIX,dw.com,Proxy
DOMAIN-SUFFIX,duckduckgo.com,Proxy
DOMAIN-SUFFIX,dropbox.com,Proxy
DOMAIN-SUFFIX,dropboxusercontent.com,Proxy
DOMAIN-SUFFIX,disconnect.me,Proxy
DOMAIN-SUFFIX,dcmilitary.com,Proxy
DOMAIN-SUFFIX,digitaltrends.com,Proxy
DOMAIN-SUFFIX,daolan.net,Proxy
#E
DOMAIN-SUFFIX,extmatrix.com,Proxy
DOMAIN-SUFFIX,easybib.com,Proxy
DOMAIN-SUFFIX,easybib.com,Proxy
DOMAIN-SUFFIX,economist.com,Proxy
DOMAIN-SUFFIX,edgecastcdn.net,Proxy
#F
DOMAIN-SUFFIX,facebook.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,facebook.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,fbcdn.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,freeopenproxy.com,Proxy
DOMAIN-SUFFIX,fzlm.net,Proxy
DOMAIN-SUFFIX,flitto.com,Proxy
DOMAIN-SUFFIX,flipkart.com,Proxy
DOMAIN-SUFFIX,flickr.com,Proxy
#G
DOMAIN-SUFFIX,getcloudapp.com,Proxy
DOMAIN-SUFFIX,gunsamerica.com,Proxy
DOMAIN-SUFFIX,gravatar.com,Proxy
DOMAIN-SUFFIX,getlantern.org,Proxy
DOMAIN-SUFFIX,getfoxyproxy.org,Proxy
DOMAIN-SUFFIX,go.com,Proxy
DOMAIN-SUFFIX,go.jp,Proxy
DOMAIN-SUFFIX,gfw.press,Proxy
#H
DOMAIN-SUFFIX,howtoforge.com,Proxy
DOMAIN-SUFFIX,hootsuite.com,Proxy
DOMAIN-SUFFIX,homedepot.com,Proxy
DOMAIN-SUFFIX,hulu.com,Proxy
#I
DOMAIN-SUFFIX,instagram.com,Proxy
DOMAIN-SUFFIX,icoco.com,Proxy
DOMAIN-SUFFIX,imgur.com,Proxy
DOMAIN-SUFFIX,instructables.com,Proxy
DOMAIN-SUFFIX,ift.tt,Proxy
#K
DOMAIN-SUFFIX,kenengba.com,Proxy
#L
DOMAIN-SUFFIX,logmein.com,Proxy
#M
DOMAIN-SUFFIX,mp3buscador.com,Proxy
DOMAIN-SUFFIX,medium.com,Proxy
DOMAIN-SUFFIX,mlssoccer.com,Proxy
DOMAIN-SUFFIX,marketwatch.com,Proxy
DOMAIN-SUFFIX,nih.gov,Proxy
DOMAIN-SUFFIX,mycnnews.com,Proxy
DOMAIN-SUFFIX,maplestage.com,Proxy
#N
DOMAIN-SUFFIX,nytimes.com,Proxy
DOMAIN-SUFFIX,nytimg.com,Proxy
DOMAIN-SUFFIX,nrk.no,Proxy
DOMAIN-SUFFIX,newipnow.com,Proxy
DOMAIN-SUFFIX,ndr.de,Proxy
DOMAIN-SUFFIX,nasa.gov,Proxy
DOMAIN-SUFFIX,netflix.com,Proxy
DOMAIN-SUFFIX,nintendo.com,Proxy
#O
DOMAIN-SUFFIX,onlineyoutube.com,Proxy
DOMAIN-SUFFIX,osha.gov,Proxy
DOMAIN-SUFFIX,optimizely.com,Proxy
#P
DOMAIN-SUFFIX,psiphon3.com,Proxy
DOMAIN-SUFFIX,puffinbrowser.com,Proxy
DOMAIN-SUFFIX,pubu.com.tw,Proxy
DOMAIN-SUFFIX,proxfree.com,Proxy
DOMAIN-SUFFIX,popo.tw,Proxy
DOMAIN-SUFFIX,pokemon.com,Proxy
DOMAIN-SUFFIX,pastebin.com,Proxy
DOMAIN-SUFFIX,pandora.com,Proxy
#R
DOMAIN-SUFFIX,rsf.org,Proxy
DOMAIN-SUFFIX,rileyguide.com,Proxy
DOMAIN-SUFFIX,rfi.fr,Proxy
DOMAIN-SUFFIX,reuters.com,Proxy
DOMAIN-SUFFIX,readmoo.com,Proxy
DOMAIN-SUFFIX,readingtimes.com.tw,Proxy
#S
DOMAIN-SUFFIX,scribd.com,Proxy
DOMAIN-SUFFIX,sydneytoday.com,Proxy
DOMAIN-SUFFIX,surrenderat20.net,Proxy
DOMAIN-SUFFIX,surfeasy.com.au,Proxy
DOMAIN-SUFFIX,sugarsync.com,Proxy
DOMAIN-SUFFIX,stumbleupon.com,Proxy
DOMAIN-SUFFIX,storify.com,Proxy
DOMAIN-SUFFIX,startpage.com,Proxy
DOMAIN-SUFFIX,starp2p.com,Proxy
DOMAIN-SUFFIX,state.gov,Proxy
DOMAIN-SUFFIX,spike.com,Proxy
DOMAIN-SUFFIX,sowers.org.hk,Proxy
DOMAIN-SUFFIX,soundcloud.com,Proxy
DOMAIN-SUFFIX,sockslist.net,Proxy
DOMAIN-SUFFIX,snapchat.com,Proxy
DOMAIN-SUFFIX,smh.com.au,Proxy
DOMAIN-SUFFIX,slideshare.net,Proxy
DOMAIN-SUFFIX,skype.com,Proxy
DOMAIN-SUFFIX,sketchappsources.com,Proxy
DOMAIN-SUFFIX,sidelinesnews.com,Proxy
DOMAIN-SUFFIX,shadowsocks.org,Proxy
DOMAIN-SUFFIX,search.com,Proxy
DOMAIN-SUFFIX,sciencemag.org,Proxy
DOMAIN-SUFFIX,ssa.gov,Proxy
DOMAIN-SUFFIX,shutterstock.com,Proxy
DOMAIN-SUFFIX,sciencedaily.com,Proxy
DOMAIN-SUFFIX,signalsitemap.com,Proxy
DOMAIN-SUFFIX,surge.run,Proxy
#T
DOMAIN-SUFFIX,twtkr.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twimg.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twitthat.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twitterrific.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twittercounter.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twittergadget.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twitterfeed.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twitter4j.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,twttr.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twitter.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,t.co,Proxy,force-remote-dns
DOMAIN-SUFFIX,tv.com,Proxy
DOMAIN-SUFFIX,tumblr.com,Proxy
DOMAIN-SUFFIX,turbobit.net,Proxy
DOMAIN-SUFFIX,tt-rss.org,Proxy
DOMAIN-SUFFIX,trulyergonomic.com,Proxy
DOMAIN-SUFFIX,trendsmap.com,Proxy
DOMAIN-SUFFIX,transparency.org,Proxy
DOMAIN-SUFFIX,traffichaus.com,Proxy
DOMAIN-SUFFIX,torrentz.eu,Proxy
DOMAIN-SUFFIX,torrentproject.se,Proxy
DOMAIN-SUFFIX,torrentprivacy.com,Proxy
DOMAIN-SUFFIX,torproject.org,Proxy
DOMAIN-SUFFIX,torcn.com,Proxy
DOMAIN-SUFFIX,tokyocn.com,Proxy
DOMAIN-SUFFIX,togetter.com,Proxy
DOMAIN-SUFFIX,tinychat.com,Proxy
DOMAIN-SUFFIX,tiny.cc,Proxy
DOMAIN-SUFFIX,time.com,Proxy
DOMAIN-SUFFIX,thewgo.org,Proxy
DOMAIN-SUFFIX,thepiratebay.org,Proxy
DOMAIN-SUFFIX,thebobs.com,Proxy
DOMAIN-SUFFIX,telegram.org,Proxy
DOMAIN-SUFFIX,telegram.me,Proxy
DOMAIN-SUFFIX,technorati.com,Proxy
DOMAIN-SUFFIX,talkboxapp.com,Proxy
DOMAIN-SUFFIX,talkonly.net,Proxy
DOMAIN-SUFFIX,talk853.com,Proxy
DOMAIN-SUFFIX,tabtter.jp,Proxy
DOMAIN-SUFFIX,tablesgenerator.com,Proxy
DOMAIN-SUFFIX,tomshardware.com,Proxy
DOMAIN-SUFFIX,theverge.com,Proxy
#U
DOMAIN-SUFFIX,ustream.tv,Proxy
DOMAIN-SUFFIX,uspto.gov,Proxy
DOMAIN-SUFFIX,usma.edu,Proxy
DOMAIN-SUFFIX,us.to,Proxy
DOMAIN-SUFFIX,urlparser.com,Proxy
DOMAIN-SUFFIX,uproxy.org,Proxy
DOMAIN-SUFFIX,uploaded.net,Proxy
DOMAIN-SUFFIX,untraceable.us,Proxy
DOMAIN-SUFFIX,unpo.org,Proxy
DOMAIN-SUFFIX,unblocksites.co,Proxy
DOMAIN-SUFFIX,unblockdmm.com,Proxy
DOMAIN-SUFFIX,uhdwallpapers.org,Proxy
DOMAIN-SUFFIX,ugo.com,Proxy
DOMAIN-SUFFIX,udn.com,Proxy
DOMAIN-SUFFIX,uchicago.edu,Proxy
DOMAIN-SUFFIX,usgs.gov,Proxy
#V
DOMAIN-SUFFIX,vpngate.net,Proxy
DOMAIN-SUFFIX,vpnbook.com,Proxy
DOMAIN-SUFFIX,vpnaccount.org,Proxy
DOMAIN-SUFFIX,vocativ.com,Proxy
DOMAIN-SUFFIX,visibletweets.com,Proxy
DOMAIN-SUFFIX,vimperator.org,Proxy
DOMAIN-SUFFIX,vimeo.com,Proxy
DOMAIN-SUFFIX,vimeocdn.com,Proxy
DOMAIN-SUFFIX,vidinfo.org,Proxy
DOMAIN-SUFFIX,videomega.tv,Proxy
DOMAIN-SUFFIX,vid.me,Proxy
DOMAIN-SUFFIX,viber.com,Proxy
DOMAIN-SUFFIX,veoh.com,Proxy
DOMAIN-SUFFIX,venchina.com,Proxy
DOMAIN-SUFFIX,vansky.com,Proxy
DOMAIN-SUFFIX,vanpeople.com,Proxy
DOMAIN-SUFFIX,van001.com,Proxy
DOMAIN-SUFFIX,v2ray.com,Proxy
DOMAIN-SUFFIX,verizonwireless.com,Proxy
DOMAIN-SUFFIX,vzw.com,Proxy
DOMAIN-SUFFIX,voachinese.com,Proxy
#W
DOMAIN-SUFFIX,wwitv.com,Proxy
DOMAIN-SUFFIX,wsj.com,Proxy
DOMAIN-SUFFIX,wordpress.com,Proxy
DOMAIN-SUFFIX,wp.com,Proxy
DOMAIN-SUFFIX,wow.com,Proxy
DOMAIN-SUFFIX,worldcat.org,Proxy
DOMAIN-SUFFIX,wn.com,Proxy
DOMAIN-SUFFIX,wikipedia.org,Proxy
DOMAIN-SUFFIX,wikileaks.info,Proxy
DOMAIN-SUFFIX,wikileaks-forum.com,Proxy
DOMAIN-SUFFIX,wikileaks.org,Proxy
DOMAIN-SUFFIX,westpoint.edu,Proxy
DOMAIN-SUFFIX,westca.com,Proxy
DOMAIN-SUFFIX,wenxuecity.com,Proxy
DOMAIN-SUFFIX,webwarper.net,Proxy
DOMAIN-SUFFIX,websnapr.com,Proxy
DOMAIN-SUFFIX,weblagu.com,Proxy
DOMAIN-SUFFIX,webfreer.com,Proxy
DOMAIN-SUFFIX,web2project.net,Proxy
DOMAIN-SUFFIX,wattpad.com,Proxy
DOMAIN-SUFFIX,w3schools.com,Proxy
DOMAIN-SUFFIX,whatsapp.net,Proxy
DOMAIN-SUFFIX,winudf.com,Proxy
#X
DOMAIN-SUFFIX,xuite.net,Proxy
DOMAIN-SUFFIX,xanga.com,Proxy
#Y
DOMAIN-SUFFIX,yahoo.com,Proxy
DOMAIN-SUFFIX,yourlisten.com,Proxy
DOMAIN-SUFFIX,youmaker.com,Proxy
DOMAIN-SUFFIX,yorkbbs.ca,Proxy
DOMAIN-SUFFIX,yidio.com,Proxy
DOMAIN-SUFFIX,yes-news.com,Proxy
DOMAIN-SUFFIX,yesasia.com,Proxy
DOMAIN-SUFFIX,yeeyi.com,Proxy
DOMAIN-SUFFIX,yasni.co.uk,Proxy
DOMAIN-SUFFIX,yastatic.net,Proxy
#Z
DOMAIN-SUFFIX,zacebook.com,Proxy
DOMAIN-SUFFIX,zalmos.com,Proxy
DOMAIN-SUFFIX,zaobao.com.sg,Proxy
DOMAIN-SUFFIX,zeutch.com,Proxy
#0-9
DOMAIN-SUFFIX,4everproxy.com,Proxy
DOMAIN-SUFFIX,4shared.com,Proxy
# Telegram
# 通用部分
DOMAIN-SUFFIX,telegra.ph,Proxy
DOMAIN-SUFFIX,telegram.org,Proxy
IP-CIDR,91.108.56.0/22,Proxy,no-resolve
IP-CIDR,91.108.4.0/22,Proxy,no-resolve
IP-CIDR,109.239.140.0/24,Proxy,no-resolve
IP-CIDR,149.154.164.0/22,Proxy,no-resolve
IP-CIDR,149.154.172.0/22,Proxy,no-resolve
IP-CIDR,149.154.160.0/22,Proxy,no-resolve
IP-CIDR,149.154.168.0/22,Proxy,no-resolve
# Slack
DOMAIN-SUFFIX,slack-edge.com,Proxy
DOMAIN-SUFFIX,slack.com,Proxy
DOMAIN-SUFFIX,slack-msgs.com,Proxy
# LAN
DOMAIN-SUFFIX,local,DIRECT
IP-CIDR,127.0.0.0/8,DIRECT
IP-CIDR,172.16.0.0/12,DIRECT
IP-CIDR,192.168.0.0/16,DIRECT
IP-CIDR,10.0.0.0/8,DIRECT
IP-CIDR,100.64.0.0/10,DIRECT
# Final Rules
GEOIP,CN,DIRECT
FINAL,,Proxy

';
    }

    private static function GetSurge($passwd, $method, $server, $port, $defined)
    {
        $rulelist = base64_decode(file_get_contents("https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt"))."\n".$defined;
        $gfwlist = explode("\n", $rulelist);

        $count = 0;
        $pac_content = '';
        $find_function_content = '
[General]
skip-proxy = 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12, localhost, *.local
bypass-tun = 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12
dns-server = 119.29.29.29, 223.5.5.5, 114.114.114.114
loglevel = notify

[Proxy]
Proxy = custom,'.$server.','.$port.','.$method.','.$passwd.','.Config::get('baseUrl').'/downloads/SSEncrypt.module

[Rule]
DOMAIN-KEYWORD,adsmogo,REJECT
DOMAIN-SUFFIX,acs86.com,REJECT
DOMAIN-SUFFIX,adcome.cn,REJECT
DOMAIN-SUFFIX,adinfuse.com,REJECT
DOMAIN-SUFFIX,admaster.com.cn,REJECT
DOMAIN-SUFFIX,admob.com,REJECT
DOMAIN-SUFFIX,adsage.cn,REJECT
DOMAIN-SUFFIX,adsage.com,REJECT
DOMAIN-SUFFIX,adsmogo.org,REJECT
DOMAIN-SUFFIX,ads.mobclix.com,REJECT
DOMAIN-SUFFIX,adview.cn,REJECT
DOMAIN-SUFFIX,adwhirl.com,REJECT
DOMAIN-SUFFIX,adwo.com,REJECT
DOMAIN-SUFFIX,appads.com,REJECT
DOMAIN-SUFFIX,domob.cn,REJECT
DOMAIN-SUFFIX,domob.com.cn,REJECT
DOMAIN-SUFFIX,domob.org,REJECT
DOMAIN-SUFFIX,doubleclick.net,REJECT
DOMAIN-SUFFIX,duomeng.cn,REJECT
DOMAIN-SUFFIX,duomeng.net,REJECT
DOMAIN-SUFFIX,duomeng.org,REJECT
DOMAIN-SUFFIX,googeadsserving.cn,REJECT
DOMAIN-SUFFIX,guomob.com,REJECT
DOMAIN-SUFFIX,immob.cn,REJECT
DOMAIN-SUFFIX,inmobi.com,REJECT
DOMAIN-SUFFIX,mobads.baidu.com,REJECT
DOMAIN-SUFFIX,mobads-logs.baidu.com,REJECT
DOMAIN-SUFFIX,smartadserver.com,REJECT
DOMAIN-SUFFIX,tapjoyads.com,REJECT
DOMAIN-SUFFIX,umeng.co,REJECT
DOMAIN-SUFFIX,umeng.com,REJECT
DOMAIN-SUFFIX,umtrack.com,REJECT
DOMAIN-SUFFIX,uyunad.com,REJECT
DOMAIN-SUFFIX,youmi.net,REJECT'."\n";
        $isget=array();
        foreach ($gfwlist as $index=>$rule) {
            if (empty($rule)) {
                continue;
            } elseif (substr($rule, 0, 1) == '!' || substr($rule, 0, 1) == '[') {
                continue;
            }

            if (substr($rule, 0, 2) == '@@') {
                // ||开头表示前面还有路径
                if (substr($rule, 2, 2) =='||') {
                    //$rule_reg = preg_match("/^((http|https):\/\/)?([^\/]+)/i",substr($rule, 2), $matches);
                    $host = substr($rule, 4);
                    //preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    $find_function_content.="DOMAIN,".$host.",DIRECT,force-remote-dns\n";
                    continue;
                // !开头相当于正则表达式^
                } elseif (substr($rule, 2, 1) == '|') {
                    preg_match("/(\d{1,3}\.){3}\d{1,3}/", substr($rule, 3), $matches);
                    if (!isset($matches[0])) {
                        continue;
                    }

                    $host = $matches[0];
                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        $find_function_content.="IP-CIDR,".$host."/32,DIRECT,no-resolve \n";
                        continue;
                    } else {
                        preg_match_all("~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i", substr($rule, 3), $matches);

                        if (!isset($matches[4][0])) {
                            continue;
                        }

                        $host = $matches[4][0];
                        if ($host != "") {
                            if (isset($isget[$host])) {
                                continue;
                            }
                            $isget[$host]=1;
                            $find_function_content.="DOMAIN-SUFFIX,".$host.",DIRECT,force-remote-dns\n";
                            continue;
                        }
                    }
                } elseif (substr($rule, 2, 1) == '.') {
                    $host = substr($rule, 3);
                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        $find_function_content.="DOMAIN-SUFFIX,".$host.",DIRECT,force-remote-dns \n";
                        continue;
                    }
                }
            }

            // ||开头表示前面还有路径
            if (substr($rule, 0, 2) =='||') {
                //$rule_reg = preg_match("/^((http|https):\/\/)?([^\/]+)/i",substr($rule, 2), $matches);
                $host = substr($rule, 2);
                //preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);

                if (strpos($host, "*")!==false) {
                    $host = substr($host, strpos($host, "*")+1);
                    if (strpos($host, ".")!==false) {
                        $host = substr($host, strpos($host, ".")+1);
                    }
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    $find_function_content.="DOMAIN-KEYWORD,".$host.",Proxy,force-remote-dns\n";
                    continue;
                }

                if (isset($isget[$host])) {
                    continue;
                }
                $isget[$host]=1;
                $find_function_content.="DOMAIN,".$host.",Proxy,force-remote-dns\n";
            // !开头相当于正则表达式^
            } elseif (substr($rule, 0, 1) == '|') {
                preg_match("/(\d{1,3}\.){3}\d{1,3}/", substr($rule, 1), $matches);

                if (!isset($matches[0])) {
                    continue;
                }

                $host = $matches[0];
                if ($host != "") {
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    $find_function_content.="IP-CIDR,".$host."/32,Proxy,no-resolve \n";
                    continue;
                } else {
                    preg_match_all("~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i", substr($rule, 1), $matches);

                    if (!isset($matches[4][0])) {
                        continue;
                    }

                    $host = $matches[4][0];
                    if (strpos($host, "*")!==false) {
                        $host = substr($host, strpos($host, "*")+1);
                        if (strpos($host, ".")!==false) {
                            $host = substr($host, strpos($host, ".")+1);
                        }
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        $find_function_content.="DOMAIN-KEYWORD,".$host.",Proxy,force-remote-dns\n";
                        continue;
                    }

                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        $find_function_content.="DOMAIN-SUFFIX,".$host.",Proxy,force-remote-dns\n";
                        continue;
                    }
                }
            } else {
                $host = substr($rule, 0);
                if (strpos($host, "/")!==false) {
                    $host = substr($host, 0, strpos($host, "/"));
                }

                if ($host != "") {
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    $find_function_content.="DOMAIN-KEYWORD,".$host.",PROXY,force-remote-dns \n";
                    continue;
                }
            }


            $count = $count + 1;
        }
        $find_function_content.='
DOMAIN-KEYWORD,google,Proxy,force-remote-dns
IP-CIDR,91.108.4.0/22,Proxy,no-resolve
IP-CIDR,91.108.56.0/22,Proxy,no-resolve
IP-CIDR,109.239.140.0/24,Proxy,no-resolve
IP-CIDR,149.154.160.0/20,Proxy,no-resolve
IP-CIDR,10.0.0.0/8,DIRECT
IP-CIDR,127.0.0.0/8,DIRECT
IP-CIDR,172.16.0.0/12,DIRECT
IP-CIDR,192.168.0.0/16,DIRECT
GEOIP,CN,DIRECT
FINAL,DIRECT
      ';
        $pac_content.=$find_function_content;
        return $pac_content;
    }


    private static function GetSurgeGeo($passwd, $method, $server, $port)
    {
        return '
[General]

skip-proxy = 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12, localhost, *.local

bypass-tun = 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12

dns-server = 119.29.29.29, 223.5.5.5, 114.114.114.114
loglevel = notify



[Proxy]

Proxy = custom,'.$server.','.$port.','.$method.','.$passwd.','.Config::get('baseUrl').'/downloads/SSEncrypt.module



[Rule]

DOMAIN-KEYWORD,adsmogo,REJECT

DOMAIN-SUFFIX,acs86.com,REJECT

DOMAIN-SUFFIX,adcome.cn,REJECT

DOMAIN-SUFFIX,adinfuse.com,REJECT

DOMAIN-SUFFIX,admaster.com.cn,REJECT

DOMAIN-SUFFIX,admob.com,REJECT

DOMAIN-SUFFIX,adsage.cn,REJECT

DOMAIN-SUFFIX,adsage.com,REJECT

DOMAIN-SUFFIX,adsmogo.org,REJECT

DOMAIN-SUFFIX,ads.mobclix.com,REJECT

DOMAIN-SUFFIX,adview.cn,REJECT

DOMAIN-SUFFIX,adwhirl.com,REJECT

DOMAIN-SUFFIX,adwo.com,REJECT

DOMAIN-SUFFIX,appads.com,REJECT

DOMAIN-SUFFIX,domob.cn,REJECT

DOMAIN-SUFFIX,domob.com.cn,REJECT

DOMAIN-SUFFIX,domob.org,REJECT

DOMAIN-SUFFIX,doubleclick.net,REJECT

DOMAIN-SUFFIX,duomeng.cn,REJECT

DOMAIN-SUFFIX,duomeng.net,REJECT

DOMAIN-SUFFIX,duomeng.org,REJECT

DOMAIN-SUFFIX,googeadsserving.cn,REJECT

DOMAIN-SUFFIX,guomob.com,REJECT

DOMAIN-SUFFIX,immob.cn,REJECT

DOMAIN-SUFFIX,inmobi.com,REJECT

DOMAIN-SUFFIX,mobads.baidu.com,REJECT

DOMAIN-SUFFIX,mobads-logs.baidu.com,REJECT

DOMAIN-SUFFIX,smartadserver.com,REJECT

DOMAIN-SUFFIX,tapjoyads.com,REJECT

DOMAIN-SUFFIX,umeng.co,REJECT

DOMAIN-SUFFIX,umeng.com,REJECT

DOMAIN-SUFFIX,umtrack.com,REJECT

DOMAIN-SUFFIX,uyunad.com,REJECT

DOMAIN-SUFFIX,youmi.net,REJECT

GEOIP,AD,Proxy
GEOIP,AE,Proxy
GEOIP,AF,Proxy
GEOIP,AG,Proxy
GEOIP,AI,Proxy
GEOIP,AL,Proxy
GEOIP,AM,Proxy
GEOIP,AO,Proxy
GEOIP,AQ,Proxy
GEOIP,AR,Proxy
GEOIP,AS,Proxy
GEOIP,AS,Proxy
GEOIP,AS,Proxy
GEOIP,AS,Proxy
GEOIP,AT,Proxy
GEOIP,AU,Proxy
GEOIP,AW,Proxy
GEOIP,AX,Proxy
GEOIP,AZ,Proxy
GEOIP,BA,Proxy
GEOIP,BD,Proxy
GEOIP,BE,Proxy
GEOIP,BF,Proxy
GEOIP,BG,Proxy
GEOIP,BH,Proxy
GEOIP,BI,Proxy
GEOIP,BJ,Proxy
GEOIP,BL,Proxy
GEOIP,BM,Proxy
GEOIP,BN,Proxy
GEOIP,BO,Proxy
GEOIP,BQ,Proxy
GEOIP,BR,Proxy
GEOIP,BS,Proxy
GEOIP,BT,Proxy
GEOIP,BW,Proxy
GEOIP,BY,Proxy
GEOIP,BZ,Proxy
GEOIP,CA,Proxy
GEOIP,CC,Proxy
GEOIP,CD,Proxy
GEOIP,CF,Proxy
GEOIP,CG,Proxy
GEOIP,CH,Proxy
GEOIP,CI,Proxy
GEOIP,CK,Proxy
GEOIP,CL,Proxy
GEOIP,CM,Proxy
GEOIP,CO,Proxy
GEOIP,CR,Proxy
GEOIP,CU,Proxy
GEOIP,CV,Proxy
GEOIP,CW,Proxy
GEOIP,CX,Proxy
GEOIP,CY,Proxy
GEOIP,CZ,Proxy
GEOIP,DE,Proxy
GEOIP,DJ,Proxy
GEOIP,DK,Proxy
GEOIP,DM,Proxy
GEOIP,DO,Proxy
GEOIP,DZ,Proxy
GEOIP,EC,Proxy
GEOIP,EE,Proxy
GEOIP,EG,Proxy
GEOIP,EG,Proxy
GEOIP,EH,Proxy
GEOIP,ER,Proxy
GEOIP,ES,Proxy
GEOIP,ET,Proxy
GEOIP,FI,Proxy
GEOIP,FJ,Proxy
GEOIP,FK,Proxy
GEOIP,FM,Proxy
GEOIP,FO,Proxy
GEOIP,FR,Proxy
GEOIP,GA,Proxy
GEOIP,GB,Proxy
GEOIP,GD,Proxy
GEOIP,GE,Proxy
GEOIP,GF,Proxy
GEOIP,GG,Proxy
GEOIP,GH,Proxy
GEOIP,GI,Proxy
GEOIP,GL,Proxy
GEOIP,GM,Proxy
GEOIP,GN,Proxy
GEOIP,GP,Proxy
GEOIP,GQ,Proxy
GEOIP,GR,Proxy
GEOIP,GS,Proxy
GEOIP,GT,Proxy
GEOIP,GU,Proxy
GEOIP,GW,Proxy
GEOIP,GY,Proxy
GEOIP,HK,Proxy
GEOIP,HM,Proxy
GEOIP,HN,Proxy
GEOIP,HR,Proxy
GEOIP,HT,Proxy
GEOIP,HU,Proxy
GEOIP,ID,Proxy
GEOIP,IE,Proxy
GEOIP,IL,Proxy
GEOIP,IM,Proxy
GEOIP,IN,Proxy
GEOIP,IO,Proxy
GEOIP,IQ,Proxy
GEOIP,IR,Proxy
GEOIP,IS,Proxy
GEOIP,IT,Proxy
GEOIP,JE,Proxy
GEOIP,JM,Proxy
GEOIP,JO,Proxy
GEOIP,JP,Proxy
GEOIP,KE,Proxy
GEOIP,KG,Proxy
GEOIP,KH,Proxy
GEOIP,KI,Proxy
GEOIP,KM,Proxy
GEOIP,KN,Proxy
GEOIP,KP,Proxy
GEOIP,KR,Proxy
GEOIP,KW,Proxy
GEOIP,KY,Proxy
GEOIP,KZ,Proxy
GEOIP,LA,Proxy
GEOIP,LB,Proxy
GEOIP,LC,Proxy
GEOIP,LI,Proxy
GEOIP,LK,Proxy
GEOIP,LR,Proxy
GEOIP,LS,Proxy
GEOIP,LT,Proxy
GEOIP,LU,Proxy
GEOIP,LV,Proxy
GEOIP,LY,Proxy
GEOIP,MA,Proxy
GEOIP,MC,Proxy
GEOIP,MD,Proxy
GEOIP,ME,Proxy
GEOIP,MF,Proxy
GEOIP,MG,Proxy
GEOIP,MH,Proxy
GEOIP,MK,Proxy
GEOIP,ML,Proxy
GEOIP,MM,Proxy
GEOIP,MN,Proxy
GEOIP,MO,Proxy
GEOIP,MP,Proxy
GEOIP,MQ,Proxy
GEOIP,MR,Proxy
GEOIP,MS,Proxy
GEOIP,MT,Proxy
GEOIP,MU,Proxy
GEOIP,MV,Proxy
GEOIP,MW,Proxy
GEOIP,MX,Proxy
GEOIP,MY,Proxy
GEOIP,MZ,Proxy
GEOIP,NA,Proxy
GEOIP,NC,Proxy
GEOIP,NE,Proxy
GEOIP,NF,Proxy
GEOIP,NG,Proxy
GEOIP,NI,Proxy
GEOIP,NL,Proxy
GEOIP,NO,Proxy
GEOIP,NP,Proxy
GEOIP,NR,Proxy
GEOIP,NU,Proxy
GEOIP,NZ,Proxy
GEOIP,OM,Proxy
GEOIP,PA,Proxy
GEOIP,PE,Proxy
GEOIP,PF,Proxy
GEOIP,PG,Proxy
GEOIP,PH,Proxy
GEOIP,PK,Proxy
GEOIP,PL,Proxy
GEOIP,PM,Proxy
GEOIP,PN,Proxy
GEOIP,PR,Proxy
GEOIP,PS,Proxy
GEOIP,PT,Proxy
GEOIP,PW,Proxy
GEOIP,PY,Proxy
GEOIP,QA,Proxy
GEOIP,RE,Proxy
GEOIP,RO,Proxy
GEOIP,RS,Proxy
GEOIP,RU,Proxy
GEOIP,RW,Proxy
GEOIP,SA,Proxy
GEOIP,SB,Proxy
GEOIP,SC,Proxy
GEOIP,SD,Proxy
GEOIP,SE,Proxy
GEOIP,SG,Proxy
GEOIP,SH,Proxy
GEOIP,SI,Proxy
GEOIP,SJ,Proxy
GEOIP,SK,Proxy
GEOIP,SL,Proxy
GEOIP,SM,Proxy
GEOIP,SN,Proxy
GEOIP,SO,Proxy
GEOIP,SR,Proxy
GEOIP,SS,Proxy
GEOIP,ST,Proxy
GEOIP,SV,Proxy
GEOIP,SX,Proxy
GEOIP,SY,Proxy
GEOIP,SZ,Proxy
GEOIP,TC,Proxy
GEOIP,TD,Proxy
GEOIP,TF,Proxy
GEOIP,TG,Proxy
GEOIP,TH,Proxy
GEOIP,TJ,Proxy
GEOIP,TK,Proxy
GEOIP,TL,Proxy
GEOIP,TM,Proxy
GEOIP,TN,Proxy
GEOIP,TO,Proxy
GEOIP,TR,Proxy
GEOIP,TT,Proxy
GEOIP,TV,Proxy
GEOIP,TW,Proxy
GEOIP,TZ,Proxy
GEOIP,UA,Proxy
GEOIP,UG,Proxy
GEOIP,UM,Proxy
GEOIP,US,Proxy
GEOIP,UY,Proxy
GEOIP,UZ,Proxy
GEOIP,VA,Proxy
GEOIP,VC,Proxy
GEOIP,VE,Proxy
GEOIP,VG,Proxy
GEOIP,VI,Proxy
GEOIP,VN,Proxy
GEOIP,VU,Proxy
GEOIP,WF,Proxy
GEOIP,WS,Proxy
GEOIP,YE,Proxy
GEOIP,YT,Proxy
GEOIP,ZA,Proxy
GEOIP,ZM,Proxy
GEOIP,ZW,Proxy
IP-CIDR,91.108.4.0/22,Proxy,no-resolve

IP-CIDR,91.108.56.0/22,Proxy,no-resolve

IP-CIDR,109.239.140.0/24,Proxy,no-resolve

IP-CIDR,149.154.160.0/20,Proxy,no-resolve

IP-CIDR,10.0.0.0/8,DIRECT

IP-CIDR,127.0.0.0/8,DIRECT

IP-CIDR,172.16.0.0/12,DIRECT

IP-CIDR,192.168.0.0/16,DIRECT

GEOIP,CN,DIRECT

FINAL,Proxy';
    }

    private static function GetApn($apn, $server, $port)
    {
        return '
        <?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
        <plist version="1.0">
        <dict>
            <key>PayloadContent</key>
            <array>
                <dict>
                    <key>PayloadContent</key>
                    <array>
                        <dict>
                            <key>DefaultsData</key>
                            <dict>
                                <key>apns</key>
                                <array>
                                    <dict>
                                        <key>apn</key>
                                        <string>'.$apn.'</string>
                                        <key>proxy</key>
                                        <string>'.$server.'</string>
                                        <key>proxyPort</key>
                                        <integer>'.$port.'</integer>
                                    </dict>
                                </array>
                            </dict>
                            <key>DefaultsDomainName</key>
                            <string>com.apple.managedCarrier</string>
                        </dict>
                    </array>
                    <key>PayloadDescription</key>
                    <string>提供对营运商“接入点名称”的自定义。</string>
                    <key>PayloadDisplayName</key>
                    <string>APN</string>
                    <key>PayloadIdentifier</key>
                    <string>com.tony.APNUNI'.$server.'.</string>
                    <key>PayloadOrganization</key>
                    <string>Tony</string>
                    <key>PayloadType</key>
                    <string>com.apple.apn.managed</string>
                    <key>PayloadUUID</key>
                    <string>7AC1FC00-7670-41CA-9EE1-4A5882DBD'.rand(100, 999).'D</string>
                    <key>PayloadVersion</key>
                    <integer>1</integer>
                </dict>
            </array>
            <key>PayloadDescription</key>
            <string>APN配置文件</string>
            <key>PayloadDisplayName</key>
            <string>APN快速配置 - '.$server.' ('.$apn.')</string>
            <key>PayloadIdentifier</key>
            <string>com.tony.APNUNI'.$server.'</string>
            <key>PayloadOrganization</key>
            <string>Tony</string>
            <key>PayloadRemovalDisallowed</key>
            <false/>
            <key>PayloadType</key>
            <string>Configuration</string>
            <key>PayloadUUID</key>
            <string>4C355D66-E72E-4DC8-864F-62C416015'.rand(100, 999).'D</string>
            <key>PayloadVersion</key>
            <integer>1</integer>
        </dict>
        </plist>
        ';
    }


    private static function GetPac($type, $address, $port, $defined)
    {
        header('Content-type: application/x-ns-proxy-autoconfig; charset=utf-8');
        return LinkController::get_pac($type, $address, $port, true, $defined);
    }

    private static function GetMacPac()
    {
        header('Content-type: application/x-ns-proxy-autoconfig; charset=utf-8');
        return LinkController::get_mac_pac();
    }


    private static function GetAcl($user)
    {
        $rulelist = base64_decode(file_get_contents("https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt"))."\n".$user->pac;
        $gfwlist = explode("\n", $rulelist);

        $count = 0;
        $acl_content = '';
        $find_function_content = '
#Generated by sspanel-glzjin-mod v3
#Time:'.date('Y-m-d H:i:s').'

[bypass_all]

';

        $proxy_list = '[proxy_list]

';
        $bypass_list = '[bypass_list]

';
        $outbound_block_list = '[outbound_block_list]

';

        $isget=array();
        foreach ($gfwlist as $index=>$rule) {
            if (empty($rule)) {
                continue;
            } elseif (substr($rule, 0, 1) == '!' || substr($rule, 0, 1) == '[') {
                continue;
            }

            if (substr($rule, 0, 2) == '@@') {
                // ||开头表示前面还有路径
                if (substr($rule, 2, 2) =='||') {
                    //$rule_reg = preg_match("/^((http|https):\/\/)?([^\/]+)/i",substr($rule, 2), $matches);
                    $host = substr($rule, 4);
                    //preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    //$find_function_content.="DOMAIN,".$host.",DIRECT,force-remote-dns\n";
                    $bypass_list .= $host."\n";
                    continue;
                // !开头相当于正则表达式^
                } elseif (substr($rule, 2, 1) == '|') {
                    preg_match("/(\d{1,3}\.){3}\d{1,3}/", substr($rule, 3), $matches);
                    if (!isset($matches[0])) {
                        continue;
                    }

                    $host = $matches[0];
                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        //$find_function_content.="IP-CIDR,".$host."/32,DIRECT,no-resolve \n";
                        $bypass_list .= $host."/32\n";
                        continue;
                    } else {
                        preg_match_all("~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i", substr($rule, 3), $matches);

                        if (!isset($matches[4][0])) {
                            continue;
                        }

                        $host = $matches[4][0];
                        if ($host != "") {
                            if (isset($isget[$host])) {
                                continue;
                            }
                            $isget[$host]=1;
                            //$find_function_content.="DOMAIN-SUFFIX,".$host.",DIRECT,force-remote-dns\n";
                            $bypass_list .= $host."\n";
                            continue;
                        }
                    }
                } elseif (substr($rule, 2, 1) == '.') {
                    $host = substr($rule, 3);
                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        //$find_function_content.="DOMAIN-SUFFIX,".$host.",DIRECT,force-remote-dns \n";
                        $bypass_list .= $host."\n";
                        continue;
                    }
                }
            }

            // ||开头表示前面还有路径
            if (substr($rule, 0, 2) =='||') {
                //$rule_reg = preg_match("/^((http|https):\/\/)?([^\/]+)/i",substr($rule, 2), $matches);
                $host = substr($rule, 2);
                //preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);

                if (strpos($host, "*")!==false) {
                    $host = substr($host, strpos($host, "*")+1);
                    if (strpos($host, ".")!==false) {
                        $host = substr($host, strpos($host, ".")+1);
                    }
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    //$find_function_content.="DOMAIN-KEYWORD,".$host.",Proxy,force-remote-dns\n";
                    $proxy_list .= $host."\n";
                    continue;
                }

                if (isset($isget[$host])) {
                    continue;
                }
                $isget[$host]=1;
                //$find_function_content.="DOMAIN,".$host.",Proxy,force-remote-dns\n";
                $proxy_list .= $host."\n";
            // !开头相当于正则表达式^
            } elseif (substr($rule, 0, 1) == '|') {
                preg_match("/(\d{1,3}\.){3}\d{1,3}/", substr($rule, 1), $matches);

                if (!isset($matches[0])) {
                    continue;
                }

                $host = $matches[0];
                if ($host != "") {
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;

                    preg_match("/(\d{1,3}\.){3}\d{1,3}\/\d{1,2}/", substr($rule, 1), $matches_ips);

                    if (!isset($matches_ips[0])) {
                        $proxy_list .= $host."/32\n";
                    } else {
                        $host = $matches_ips[0];
                        $proxy_list .= $host."\n";
                    }

                    //$find_function_content.="IP-CIDR,".$host."/32,Proxy,no-resolve \n";

                    continue;
                } else {
                    preg_match_all("~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i", substr($rule, 1), $matches);

                    if (!isset($matches[4][0])) {
                        continue;
                    }

                    $host = $matches[4][0];
                    if (strpos($host, "*")!==false) {
                        $host = substr($host, strpos($host, "*")+1);
                        if (strpos($host, ".")!==false) {
                            $host = substr($host, strpos($host, ".")+1);
                        }
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        //$find_function_content.="DOMAIN-KEYWORD,".$host.",Proxy,force-remote-dns\n";
                        $proxy_list .= $host."\n";
                        continue;
                    }

                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        //$find_function_content.="DOMAIN-SUFFIX,".$host.",Proxy,force-remote-dns\n";
                        $proxy_list .= $host."\n";
                        continue;
                    }
                }
            } else {
                $host = substr($rule, 0);
                if (strpos($host, "/")!==false) {
                    $host = substr($host, 0, strpos($host, "/"));
                }

                if ($host != "") {
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    //$find_function_content.="DOMAIN-KEYWORD,".$host.",PROXY,force-remote-dns \n";
                    $proxy_list .= $host."\n";
                    continue;
                }
            }


            $count = $count + 1;
        }

        $acl_content .= $find_function_content."\n".$proxy_list."\n".$bypass_list."\n".$outbound_block_list;
        return $acl_content;
    }



    /**
     * This is a php implementation of autoproxy2pac
     */
    private static function reg_encode($str)
    {
        $tmp_str = $str;
        $tmp_str = str_replace('/', "\\/", $tmp_str);
        $tmp_str = str_replace('.', "\\.", $tmp_str);
        $tmp_str = str_replace(':', "\\:", $tmp_str);
        $tmp_str = str_replace('%', "\\%", $tmp_str);
        $tmp_str = str_replace('*', ".*", $tmp_str);
        $tmp_str = str_replace('-', "\\-", $tmp_str);
        $tmp_str = str_replace('&', "\\&", $tmp_str);
        $tmp_str = str_replace('?', "\\?", $tmp_str);
        $tmp_str = str_replace('+', "\\+", $tmp_str);

        return $tmp_str;
    }

    private static function get_pac($proxy_type, $proxy_host, $proxy_port, $proxy_google, $defined)
    {
        $rulelist = base64_decode(file_get_contents("https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt"))."\n".$defined;
        $gfwlist = explode("\n", $rulelist);
        if ($proxy_google == "true") {
            $gfwlist[] = ".google.com";
        }

        $count = 0;
        $pac_content = '';
        $find_function_content = 'function FindProxyForURL(url, host) { var PROXY = "'.$proxy_type.' '.$proxy_host.':'.$proxy_port.'; DIRECT"; var DEFAULT = "DIRECT";'."\n";
        foreach ($gfwlist as $index=>$rule) {
            if (empty($rule)) {
                continue;
            } elseif (substr($rule, 0, 1) == '!' || substr($rule, 0, 1) == '[') {
                continue;
            }
            $return_proxy = 'PROXY';
        // @@开头表示默认是直接访问
        if (substr($rule, 0, 2) == '@@') {
            $rule = substr($rule, 2);
            $return_proxy = "DEFAULT";
        }

        // ||开头表示前面还有路径
        if (substr($rule, 0, 2) =='||') {
            $rule_reg = "^[\\w\\-]+:\\/+(?!\\/)(?:[^\\/]+\\.)?".LinkController::reg_encode(substr($rule, 2));
        // !开头相当于正则表达式^
        } elseif (substr($rule, 0, 1) == '|') {
            $rule_reg = "^" . LinkController::reg_encode(substr($rule, 1));
        // 前后匹配的/表示精确匹配
        } elseif (substr($rule, 0, 1) == '/' && substr($rule, -1) == '/') {
            $rule_reg = substr($rule, 1, strlen($rule) - 2);
        } else {
            $rule_reg = LinkController::reg_encode($rule);
        }
        // 以|结尾，替换为$结尾
        if (preg_match("/\|$/i", $rule_reg)) {
            $rule_reg = substr($rule_reg, 0, strlen($rule_reg) - 1)."$";
        }
            $find_function_content.='if (/' . $rule_reg . '/i.test(url)) return '.$return_proxy.';'."\n";
            $count = $count + 1;
        }
        $find_function_content.='return DEFAULT;'."}";
        $pac_content.=$find_function_content;
        return $pac_content;
    }


    private static function get_mac_pac()
    {
        $rulelist = base64_decode(file_get_contents("https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt"));
        $gfwlist = explode("\n", $rulelist);
        $gfwlist[] = ".google.com";

        $count = 0;
        $pac_content = '';
        $find_function_content = 'function FindProxyForURL(url, host) { var PROXY = "SOCKS5 127.0.0.1:1080; SOCKS 127.0.0.1:1080; DIRECT;"; var DEFAULT = "DIRECT";'."\n";
        foreach ($gfwlist as $index=>$rule) {
            if (empty($rule)) {
                continue;
            } elseif (substr($rule, 0, 1) == '!' || substr($rule, 0, 1) == '[') {
                continue;
            }
            $return_proxy = 'PROXY';
        // @@开头表示默认是直接访问
        if (substr($rule, 0, 2) == '@@') {
            $rule = substr($rule, 2);
            $return_proxy = "DEFAULT";
        }

        // ||开头表示前面还有路径
        if (substr($rule, 0, 2) =='||') {
            $rule_reg = "^[\\w\\-]+:\\/+(?!\\/)(?:[^\\/]+\\.)?".LinkController::reg_encode(substr($rule, 2));
        // !开头相当于正则表达式^
        } elseif (substr($rule, 0, 1) == '|') {
            $rule_reg = "^" . LinkController::reg_encode(substr($rule, 1));
        // 前后匹配的/表示精确匹配
        } elseif (substr($rule, 0, 1) == '/' && substr($rule, -1) == '/') {
            $rule_reg = substr($rule, 1, strlen($rule) - 2);
        } else {
            $rule_reg = LinkController::reg_encode($rule);
        }
        // 以|结尾，替换为$结尾
        if (preg_match("/\|$/i", $rule_reg)) {
            $rule_reg = substr($rule_reg, 0, strlen($rule_reg) - 1)."$";
        }
            $find_function_content.='if (/' . $rule_reg . '/i.test(url)) return '.$return_proxy.';'."\n";
            $count = $count + 1;
        }
        $find_function_content.='return DEFAULT;'."}";
        $pac_content.=$find_function_content;
        return $pac_content;
    }

    public static function GetRouter($user, $is_mu = 0, $is_ss = 0)
    {
        $bash = '#!/bin/sh'."\n";
        $bash .= 'export PATH=\'/opt/usr/sbin:/opt/usr/bin:/opt/sbin:/opt/bin:/usr/local/sbin:/usr/sbin:/usr/bin:/sbin:/bin\''."\n";
        $bash .= 'export LD_LIBRARY_PATH=/lib:/opt/lib'."\n";
        $bash .= 'nvram set ss_type='.($is_ss == 1 ? '0' : '1')."\n";

        $count = 0;

        $items = URL::getAllItems($user, $is_mu, $is_ss);
        foreach($items as $item) {
            if($is_ss == 0) {
                $bash .= 'nvram set rt_ss_name_x'.$count.'="'.$item['remark']."\"\n";
                $bash .= 'nvram set rt_ss_port_x'.$count.'='.$item['port']."\n";
                $bash .= 'nvram set rt_ss_password_x'.$count.'="'.$item['passwd']."\"\n";
                $bash .= 'nvram set rt_ss_server_x'.$count.'='.$item['address']."\n";
                $bash .= 'nvram set rt_ss_usage_x'.$count.'="'."-o ".$item['obfs']." -g ".$item['obfs_param']." -O ".$item['protocol']." -G ".$item['protocol_param']."\"\n";
                $bash .= 'nvram set rt_ss_method_x'.$count.'='.$item['method']."\n";
                $count += 1;
            }else{
                $bash .= 'nvram set rt_ss_name_x'.$count.'="'.$item['remark']."\"\n";
                $bash .= 'nvram set rt_ss_port_x'.$count.'='.$item['port']."\n";
                $bash .= 'nvram set rt_ss_password_x'.$count.'="'.$item['passwd']."\"\n";
                $bash .= 'nvram set rt_ss_server_x'.$count.'='.$item['address']."\n";
                $bash .= 'nvram set rt_ss_usage_x'.$count.'=""'."\n";
                $bash .= 'nvram set rt_ss_method_x'.$count.'='.$item['method']."\n";
                $count += 1;
            }
        }

        $bash .= "nvram set rt_ssnum_x=".$count."\n";

        return $bash;
    }

    public static function GetSSRSub($user, $mu = 0, $max = 0)
    {
        if ($mu==0||$mu==1) {
            return Tools::base64_url_encode(URL::getAllUrl($user, $mu, 0, 1));
        } 
		elseif ($mu==2){
            return Tools::base64_url_encode(URL::getAllVMessUrl($user));
        }
		elseif ($mu==3) {
			return Tools::base64_url_encode(URL::getAllSSDUrl($user));
		}
    }
}
