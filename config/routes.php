<?php

use Slim\App;
use Slim\Container;
use App\Controllers;
use App\Middleware\Auth;
use App\Middleware\Guest;
use App\Middleware\Admin;
use App\Middleware\Api;
use App\Middleware\Mu;
use App\Middleware\Mod_Mu;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

/***
 * The slim documents: http://www.slimframework.com/docs/objects/router.html
 */

// config
$debug = false;
if (defined("DEBUG")) {
    $debug = true;
}
/***
 * $configuration = [
 * 'settings' => [
 * 'displayErrorDetails' => $debug,
 * ]
 * ];
 * $c = new Container($configuration);
 ***/

// Make a Slim App
// $app = new App($c);

$configuration = [
    'settings' => [
        'debug' => $debug,
        'whoops.editor' => 'sublime',
        'displayErrorDetails' => $debug
    ]
];

$container = new Container($configuration);

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $response->withAddedHeader('Location', '/404');
    };
};

$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $response->withAddedHeader('Location', '/405');
    };
};

if ($debug==false) {
    $container['errorHandler'] = function ($c) {
        return function ($request, $response, $exception) use ($c) {
            return $response->withAddedHeader('Location', '/500');
        };
    };
}

$app = new App($container);
$app->add(new WhoopsMiddleware);


// Home
$app->get('/', 'App\Controllers\HomeController:index');
$app->get('/404', 'App\Controllers\HomeController:page404');
$app->get('/405', 'App\Controllers\HomeController:page405');
$app->get('/500', 'App\Controllers\HomeController:page500');
$app->get('/pwm_pingback', 'App\Controllers\HomeController:pay_callback');
$app->post('/notify', 'App\Controllers\HomeController:notify');
$app->post('/alipay_callback', 'App\Controllers\HomeController:pay_callback');
$app->post('/pay_callback', 'App\Controllers\HomeController:f2fpay_pay_callback');
$app->get('/pay_callback', 'App\Controllers\HomeController:pay_callback');
$app->get('/code', 'App\Controllers\HomeController:code');
$app->get('/tos', 'App\Controllers\HomeController:tos');
$app->get('/staff', 'App\Controllers\HomeController:staff');
$app->get('/gfwlistjs', 'App\Controllers\LinkController:GetGfwlistJs');
$app->post('/telegram_callback', 'App\Controllers\HomeController:telegram');
$app->get('/yft/notify','App\Controllers\YFTPayCallBackController:yft_notify');
$app->get('/codepay_callback', 'App\Controllers\HomeController:codepay_callback');
$app->post('/codepay_callback', 'App\Controllers\HomeController:codepay_pay_callback');


// User Center
$app->group('/user', function () {
    $this->get('', 'App\Controllers\UserController:index');
    $this->get('/', 'App\Controllers\UserController:index');
    $this->post('/checkin', 'App\Controllers\UserController:doCheckin');
    $this->get('/node', 'App\Controllers\UserController:node');
    $this->get('/announcement', 'App\Controllers\UserController:announcement');
    $this->get('/donate', 'App\Controllers\UserController:donate');
    $this->get('/lookingglass', 'App\Controllers\UserController:lookingglass');
    $this->get('/node/{id}', 'App\Controllers\UserController:nodeInfo');
    $this->get('/node/{id}/ajax', 'App\Controllers\UserController:nodeAjax');
    $this->get('/profile', 'App\Controllers\UserController:profile');
    $this->get('/invite', 'App\Controllers\UserController:invite');

    $this->get('/detect', 'App\Controllers\UserController:detect_index');
    $this->get('/detect/log', 'App\Controllers\UserController:detect_log');

    $this->get('/disable', 'App\Controllers\UserController:disable');

    $this->get('/shop', 'App\Controllers\UserController:shop');
    $this->post('/coupon_check', 'App\Controllers\UserController:CouponCheck');
    $this->post('/buy', 'App\Controllers\UserController:buy');


    // Relay Mange
    $this->get('/relay', 'App\Controllers\RelayController:index');
    $this->get('/relay/create', 'App\Controllers\RelayController:create');
    $this->post('/relay', 'App\Controllers\RelayController:add');
    $this->get('/relay/{id}/edit', 'App\Controllers\RelayController:edit');
    $this->put('/relay/{id}', 'App\Controllers\RelayController:update');
    $this->delete('/relay', 'App\Controllers\RelayController:delete');

    $this->get('/ticket', 'App\Controllers\UserController:ticket');
    $this->get('/ticket/create', 'App\Controllers\UserController:ticket_create');
    $this->post('/ticket', 'App\Controllers\UserController:ticket_add');
    $this->get('/ticket/{id}/view', 'App\Controllers\UserController:ticket_view');
    $this->put('/ticket/{id}', 'App\Controllers\UserController:ticket_update');

    $this->post('/invite', 'App\Controllers\UserController:doInvite');
    $this->get('/edit', 'App\Controllers\UserController:edit');
    $this->post('/password', 'App\Controllers\UserController:updatePassword');
    $this->post('/wechat', 'App\Controllers\UserController:updateWechat');
    $this->post('/ssr', 'App\Controllers\UserController:updateSSR');
    $this->post('/theme', 'App\Controllers\UserController:updateTheme');
    $this->post('/mail', 'App\Controllers\UserController:updateMail');
    $this->post('/sspwd', 'App\Controllers\UserController:updateSsPwd');
    $this->post('/method', 'App\Controllers\UserController:updateMethod');
    $this->post('/hide', 'App\Controllers\UserController:updateHide');
    $this->get('/sys', 'App\Controllers\UserController:sys');
    $this->get('/trafficlog', 'App\Controllers\UserController:trafficLog');
    $this->get('/kill', 'App\Controllers\UserController:kill');
    $this->post('/kill', 'App\Controllers\UserController:handleKill');
    $this->get('/logout', 'App\Controllers\UserController:logout');
    $this->get('/code', 'App\Controllers\UserController:code');
	//易付通路由定义 start
    $this->post('/code/yft/pay', 'App\Controllers\YftPay:yftPay');
    $this->get('/code/yft/pay/result', 'App\Controllers\YftPay:yftPayResult');
    $this->post('/code/yft', 'App\Controllers\YftPay:yft');
    $this->get('/yftOrder','App\Controllers\YftPay:yftOrder');
	//易付通路由定义 end
    $this->get('/alipay', 'App\Controllers\UserController:alipay');
    $this->post('/code/f2fpay', 'App\Controllers\UserController:f2fpay');
    $this->get('/code/f2fpay', 'App\Controllers\UserController:f2fpayget');
    $this->get('/code/codepay', 'App\Controllers\UserController:codepay');
    $this->get('/code_check', 'App\Controllers\UserController:code_check');
    $this->post('/code', 'App\Controllers\UserController:codepost');
    $this->post('/gacheck', 'App\Controllers\UserController:GaCheck');
    $this->post('/gaset', 'App\Controllers\UserController:GaSet');
    $this->get('/gareset', 'App\Controllers\UserController:GaReset');
    $this->get('/telegram_reset', 'App\Controllers\UserController:telegram_reset');
    $this->post('/resetport', 'App\Controllers\UserController:ResetPort');
    $this->post('/pacset', 'App\Controllers\UserController:PacSet');
    $this->get('/getpcconf', 'App\Controllers\UserController:GetPcConf');
    $this->get('/getiosconf', 'App\Controllers\UserController:GetIosConf');
    $this->post('/unblock', 'App\Controllers\UserController:Unblock');
    $this->get('/bought', 'App\Controllers\UserController:bought');
    $this->delete('/bought', 'App\Controllers\UserController:deleteBoughtGet');

    $this->get('/url_reset', 'App\Controllers\UserController:resetURL');
})->add(new Auth());

// Auth
$app->group('/auth', function () {
    $this->get('/login', 'App\Controllers\AuthController:login');
    $this->get('/qrcode_check', 'App\Controllers\AuthController:qrcode_check');
    $this->post('/login', 'App\Controllers\AuthController:loginHandle');
    $this->post('/qrcode_login', 'App\Controllers\AuthController:qrcode_loginHandle');
    $this->get('/register', 'App\Controllers\AuthController:register');
    $this->post('/register', 'App\Controllers\AuthController:registerHandle');
    $this->post('/send', 'App\Controllers\AuthController:sendVerify');
    $this->get('/logout', 'App\Controllers\AuthController:logout');
})->add(new Guest());

// Password
$app->group('/password', function () {
    $this->get('/reset', 'App\Controllers\PasswordController:reset');
    $this->post('/reset', 'App\Controllers\PasswordController:handleReset');
    $this->get('/token/{token}', 'App\Controllers\PasswordController:token');
    $this->post('/token/{token}', 'App\Controllers\PasswordController:handleToken');
})->add(new Guest());

// Admin
$app->group('/admin', function () {
    $this->get('', 'App\Controllers\AdminController:index');
    $this->get('/', 'App\Controllers\AdminController:index');
    $this->get('/trafficlog', 'App\Controllers\AdminController:trafficLog');
    $this->post('/trafficlog/ajax', 'App\Controllers\AdminController:ajax_trafficLog');
    // Node Mange
    $this->get('/node', 'App\Controllers\Admin\NodeController:index');

    $this->get('/node/create', 'App\Controllers\Admin\NodeController:create');
    $this->post('/node', 'App\Controllers\Admin\NodeController:add');
    $this->get('/node/{id}/edit', 'App\Controllers\Admin\NodeController:edit');
    $this->put('/node/{id}', 'App\Controllers\Admin\NodeController:update');
    $this->delete('/node', 'App\Controllers\Admin\NodeController:delete');
    $this->post('/node/ajax', 'App\Controllers\Admin\NodeController:ajax');


    $this->get('/ticket', 'App\Controllers\Admin\TicketController:index');
    $this->get('/ticket/{id}/view', 'App\Controllers\Admin\TicketController:show');
    $this->put('/ticket/{id}', 'App\Controllers\Admin\TicketController:update');
    $this->post('/ticket/ajax', 'App\Controllers\Admin\TicketController:ajax');

    // Relay Mange
    $this->get('/relay', 'App\Controllers\Admin\RelayController:index');
    $this->get('/relay/create', 'App\Controllers\Admin\RelayController:create');
    $this->post('/relay', 'App\Controllers\Admin\RelayController:add');
    $this->get('/relay/{id}/edit', 'App\Controllers\Admin\RelayController:edit');
    $this->put('/relay/{id}', 'App\Controllers\Admin\RelayController:update');
    $this->delete('/relay', 'App\Controllers\Admin\RelayController:delete');
    $this->get('/relay/path_search/{id}', 'App\Controllers\Admin\RelayController:path_search');
    $this->post('/relay/ajax', 'App\Controllers\Admin\RelayController:ajax_relay');

    // Shop Mange
    $this->get('/shop', 'App\Controllers\Admin\ShopController:index');
    $this->post('/shop/ajax', 'App\Controllers\Admin\ShopController:ajax_shop');

    $this->get('/bought', 'App\Controllers\Admin\ShopController:bought');
    $this->delete('/bought', 'App\Controllers\Admin\ShopController:deleteBoughtGet');
    $this->post('/bought/ajax', 'App\Controllers\Admin\ShopController:ajax_bought');

    $this->get('/shop/create', 'App\Controllers\Admin\ShopController:create');
    $this->post('/shop', 'App\Controllers\Admin\ShopController:add');
    $this->get('/shop/{id}/edit', 'App\Controllers\Admin\ShopController:edit');
    $this->put('/shop/{id}', 'App\Controllers\Admin\ShopController:update');
    $this->delete('/shop', 'App\Controllers\Admin\ShopController:deleteGet');

    // Ann Mange
    $this->get('/announcement', 'App\Controllers\Admin\AnnController:index');
    $this->get('/announcement/create', 'App\Controllers\Admin\AnnController:create');
    $this->post('/announcement', 'App\Controllers\Admin\AnnController:add');
    $this->get('/announcement/{id}/edit', 'App\Controllers\Admin\AnnController:edit');
    $this->put('/announcement/{id}', 'App\Controllers\Admin\AnnController:update');
    $this->delete('/announcement', 'App\Controllers\Admin\AnnController:delete');
    $this->post('/announcement/ajax', 'App\Controllers\Admin\AnnController:ajax');

    // Detect Mange
    $this->get('/detect', 'App\Controllers\Admin\DetectController:index');
    $this->get('/detect/create', 'App\Controllers\Admin\DetectController:create');
    $this->post('/detect', 'App\Controllers\Admin\DetectController:add');
    $this->get('/detect/{id}/edit', 'App\Controllers\Admin\DetectController:edit');
    $this->put('/detect/{id}', 'App\Controllers\Admin\DetectController:update');
    $this->delete('/detect', 'App\Controllers\Admin\DetectController:delete');
    $this->get('/detect/log', 'App\Controllers\Admin\DetectController:log');
    $this->post('/detect/ajax', 'App\Controllers\Admin\DetectController:ajax_rule');
    $this->post('/detect/log/ajax', 'App\Controllers\Admin\DetectController:ajax_log');

    $this->get('/auto', 'App\Controllers\Admin\AutoController:index');
    $this->get('/auto/create', 'App\Controllers\Admin\AutoController:create');
    $this->post('/auto', 'App\Controllers\Admin\AutoController:add');
    $this->delete('/auto', 'App\Controllers\Admin\AutoController:delete');
    $this->post('/auto/ajax', 'App\Controllers\Admin\AutoController:ajax');

    // IP Mange
    $this->get('/block', 'App\Controllers\Admin\IpController:block');
    $this->get('/unblock', 'App\Controllers\Admin\IpController:unblock');
    $this->post('/unblock', 'App\Controllers\Admin\IpController:doUnblock');
    $this->get('/login', 'App\Controllers\Admin\IpController:index');
    $this->get('/alive', 'App\Controllers\Admin\IpController:alive');
    $this->post('/block/ajax', 'App\Controllers\Admin\IpController:ajax_block');
    $this->post('/unblock/ajax', 'App\Controllers\Admin\IpController:ajax_unblock');
    $this->post('/login/ajax', 'App\Controllers\Admin\IpController:ajax_login');
    $this->post('/alive/ajax', 'App\Controllers\Admin\IpController:ajax_alive');

    // Code Mange
    $this->get('/code', 'App\Controllers\Admin\CodeController:index');
    $this->get('/code/create', 'App\Controllers\Admin\CodeController:create');
    $this->post('/code', 'App\Controllers\Admin\CodeController:add');
    $this->get('/donate/create', 'App\Controllers\Admin\CodeController:donate_create');
    $this->post('/donate', 'App\Controllers\Admin\CodeController:donate_add');
    $this->post('/code/ajax', 'App\Controllers\Admin\CodeController:ajax_code');

    // User Mange
    $this->get('/user', 'App\Controllers\Admin\UserController:index');
    $this->get('/user/{id}/edit', 'App\Controllers\Admin\UserController:edit');
    $this->put('/user/{id}', 'App\Controllers\Admin\UserController:update');
    $this->delete('/user', 'App\Controllers\Admin\UserController:delete');
    $this->get('/user/ajax', 'App\Controllers\Admin\UserController:ajax');


    $this->get('/coupon', 'App\Controllers\AdminController:coupon');
    $this->post('/coupon', 'App\Controllers\AdminController:addCoupon');
    $this->post('/coupon/ajax', 'App\Controllers\AdminController:ajax_coupon');

    $this->get('/profile', 'App\Controllers\AdminController:profile');
    $this->get('/invite', 'App\Controllers\AdminController:invite');
    $this->post('/invite', 'App\Controllers\AdminController:addInvite');
    $this->get('/sys', 'App\Controllers\AdminController:sys');
    $this->get('/logout', 'App\Controllers\AdminController:logout');
    $this->post('/payback/ajax', 'App\Controllers\AdminController:ajax_payback');
	$this->get('/yftOrder','App\Controllers\YftPay:yftOrderForAdmin');
})->add(new Admin());

// API
$app->group('/api', function () {
    $this->get('/token/{token}', 'App\Controllers\ApiController:token');
    $this->post('/token', 'App\Controllers\ApiController:newToken');
    $this->get('/node', 'App\Controllers\ApiController:node')->add(new Api());
    $this->get('/user/{id}', 'App\Controllers\ApiController:userInfo')->add(new Api());
});

// mu
$app->group('/mu', function () {
    $this->get('/users', 'App\Controllers\Mu\UserController:index');
    $this->post('/users/{id}/traffic', 'App\Controllers\Mu\UserController:addTraffic');
    $this->post('/nodes/{id}/online_count', 'App\Controllers\Mu\NodeController:onlineUserLog');
    $this->post('/nodes/{id}/info', 'App\Controllers\Mu\NodeController:info');
})->add(new Mu());

// mu
$app->group('/mod_mu', function () {
    $this->get('/nodes/{id}/info', 'App\Controllers\Mod_Mu\NodeController:get_info');
    $this->get('/users', 'App\Controllers\Mod_Mu\UserController:index');
    $this->post('/users/traffic', 'App\Controllers\Mod_Mu\UserController:addTraffic');
    $this->post('/users/aliveip', 'App\Controllers\Mod_Mu\UserController:addAliveIp');
    $this->post('/users/detectlog', 'App\Controllers\Mod_Mu\UserController:addDetectLog');
    $this->post('/nodes/{id}/info', 'App\Controllers\Mod_Mu\NodeController:info');

    $this->get('/nodes', 'App\Controllers\Mod_Mu\NodeController:get_all_info');

    $this->get('/func/detect_rules', 'App\Controllers\Mod_Mu\FuncController:get_detect_logs');
    $this->get('/func/relay_rules', 'App\Controllers\Mod_Mu\FuncController:get_relay_rules');
    $this->post('/func/block_ip', 'App\Controllers\Mod_Mu\FuncController:addBlockIp');
    $this->get('/func/block_ip', 'App\Controllers\Mod_Mu\FuncController:get_blockip');
    $this->get('/func/unblock_ip', 'App\Controllers\Mod_Mu\FuncController:get_unblockip');
    $this->post('/func/speedtest', 'App\Controllers\Mod_Mu\FuncController:addSpeedtest');
    $this->get('/func/autoexec', 'App\Controllers\Mod_Mu\FuncController:get_autoexec');
    $this->post('/func/autoexec', 'App\Controllers\Mod_Mu\FuncController:addAutoexec');

    $this->get('/func/ping', 'App\Controllers\Mod_Mu\FuncController:ping');
    //============================================
})->add(new Mod_Mu());

// res
$app->group('/res', function () {
    $this->get('/captcha/{id}', 'App\Controllers\ResController:captcha');
});


$app->group('/link', function () {
    $this->get('/{token}', 'App\Controllers\LinkController:GetContent');
});

$app->group('/user',function(){
    $this->post("/doiam","App\Utils\DoiAMPay:handle");
})->add(new Auth());
$app->group("/doiam",function(){
    $this->post("/callback/{type}","App\Utils\DoiAMPay:handle_callback");
    $this->get("/return/alipay","App\Utils\DoiAMPay:handle_return");
    $this->post("/status","App\Utils\DoiAMPay:status");
});



// Run Slim Routes for App
$app->run();
