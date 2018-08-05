<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if(preg_match('/360SE/i',$userAgent) || preg_match('/The World/i',$userAgent) || preg_match('/BIDUBrowser/i',$userAgent) || preg_match('/Maxthon/i',$userAgent) || preg_match('/QQBrowser/i',$userAgent) || preg_match('/LBBROWSER/i',$userAgent) || preg_match('/MicroMessenger/i',$userAgent) || preg_match('/QQ/i',$userAgent) || preg_match('/360/i',$userAgent) || preg_match('/TaoBrowser/i',$userAgent) || preg_match('/MetaSr/i',$userAgent) || preg_match('/Tencent/i',$userAgent) || preg_match('/UCBrowser/i',$userAgent) || preg_match('/MiuiBrowser/i',$userAgent) || preg_match('/baiduboxapp/i',$userAgent) || preg_match('/115Browser/i',$userAgent)) 
{
        header("Content-type: text/html; charset=utf-8");
        echo("请在使用本站时：");
        die("请勿使用国产浏览器");
    }

//  PUBLIC_PATH
define('PUBLIC_PATH', __DIR__);

// Bootstrap
require PUBLIC_PATH.'/../bootstrap.php';


// Init slim routes
require BASE_PATH.'/config/routes.php';
