<?php

use Slim\Container;
use App\Middleware\Auth;
use App\Middleware\Guest;
use App\Middleware\Admin;
use App\Middleware\Api;
use App\Middleware\Mu;
use App\Middleware\Mod_Mu;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

// config
$debug = false;
if (defined('DEBUG')) {
    $debug = true;
}

$configuration = [
    'settings' => [
        'debug' => $debug,
        'whoops.editor' => 'sublime',
        'displayErrorDetails' => $debug
    ]
];

$container = new Container($configuration);

// Init slim php view
$container['renderer'] = static function ($c) {
    return new Slim\Views\PhpRenderer();
};

$container['notFoundHandler'] = static function ($c) {
    return static function ($request, $response) use ($c) {
        return $response->withAddedHeader('Location', '/404');
    };
};

$container['notAllowedHandler'] = static function ($c) {
    return static function ($request, $response, $methods) use ($c) {
        return $response->withAddedHeader('Location', '/405');
    };
};

if ($debug == false) {
    $container['errorHandler'] = static function ($c) {
        return static function ($request, $response, $exception) use ($c) {
            return $response->withAddedHeader('Location', '/500');
        };
    };
}

$app = new Slim\App($container);
$app->add(new WhoopsMiddleware());


// Home
$app->post('/spay_back', App\Services\Payment::class . ':notify');
$app->get('/spay_back', App\Services\Payment::class . ':notify');
$app->get('/', App\Controllers\HomeController::class . ':index');
$app->get('/indexold', App\Controllers\HomeController::class . ':indexold');
$app->get('/404', App\Controllers\HomeController::class . ':page404');
$app->get('/405', App\Controllers\HomeController::class . ':page405');
$app->get('/500', App\Controllers\HomeController::class . ':page500');
$app->post('/notify', App\Controllers\HomeController::class . ':notify');
$app->get('/tos', App\Controllers\HomeController::class . ':tos');
$app->get('/staff', App\Controllers\HomeController::class . ':staff');
$app->post('/telegram_callback', App\Controllers\HomeController::class . ':telegram');

// User Center
$app->group('/user', function () {
    $this->get('', App\Controllers\UserController::class . ':index');
    $this->get('/', App\Controllers\UserController::class . ':index');
    $this->post('/checkin', App\Controllers\UserController::class . ':doCheckin');
    $this->get('/node', App\Controllers\UserController::class . ':node');
    $this->get('/tutorial', App\Controllers\UserController::class . ':tutorial');
    $this->get('/announcement', App\Controllers\UserController::class . ':announcement');
    $this->get('/donate', App\Controllers\UserController::class . ':donate');
    $this->get('/lookingglass', App\Controllers\UserController::class . ':lookingglass');
    $this->get('/node/{id}', App\Controllers\UserController::class . ':nodeInfo');
    $this->get('/node/{id}/ajax', App\Controllers\UserController::class . ':nodeAjax');
    $this->get('/profile', App\Controllers\UserController::class . ':profile');
    $this->get('/invite', App\Controllers\UserController::class . ':invite');

    $this->get('/detect', App\Controllers\UserController::class . ':detect_index');
    $this->get('/detect/log', App\Controllers\UserController::class . ':detect_log');

    $this->get('/disable', App\Controllers\UserController::class . ':disable');

    $this->get('/shop', App\Controllers\UserController::class . ':shop');
    $this->post('/coupon_check', App\Controllers\UserController::class . ':CouponCheck');
    $this->post('/buy', App\Controllers\UserController::class . ':buy');

    // Relay Mange
    $this->get('/relay', App\Controllers\RelayController::class . ':index');
    $this->get('/relay/create', App\Controllers\RelayController::class . ':create');
    $this->post('/relay', App\Controllers\RelayController::class . ':add');
    $this->get('/relay/{id}/edit', App\Controllers\RelayController::class . ':edit');
    $this->put('/relay/{id}', App\Controllers\RelayController::class . ':update');
    $this->delete('/relay', App\Controllers\RelayController::class . ':delete');

    $this->get('/ticket', App\Controllers\UserController::class . ':ticket');
    $this->get('/ticket/create', App\Controllers\UserController::class . ':ticket_create');
    $this->post('/ticket', App\Controllers\UserController::class . ':ticket_add');
    $this->get('/ticket/{id}/view', App\Controllers\UserController::class . ':ticket_view');
    $this->put('/ticket/{id}', App\Controllers\UserController::class . ':ticket_update');

    $this->post('/buy_invite', App\Controllers\UserController::class . ':buyInvite');
    $this->post('/custom_invite', App\Controllers\UserController::class . ':customInvite');
    $this->get('/edit', App\Controllers\UserController::class . ':edit');
    $this->post('/password', App\Controllers\UserController::class . ':updatePassword');
    $this->post('/wechat', App\Controllers\UserController::class . ':updateWechat');
    $this->post('/ssr', App\Controllers\UserController::class . ':updateSSR');
    $this->post('/theme', App\Controllers\UserController::class . ':updateTheme');
    $this->post('/mail', App\Controllers\UserController::class . ':updateMail');
    $this->post('/sspwd', App\Controllers\UserController::class . ':updateSsPwd');
    $this->post('/method', App\Controllers\UserController::class . ':updateMethod');
    $this->post('/hide', App\Controllers\UserController::class . ':updateHide');
    $this->get('/sys', App\Controllers\UserController::class . ':sys');
    $this->get('/trafficlog', App\Controllers\UserController::class . ':trafficLog');
    $this->get('/kill', App\Controllers\UserController::class . ':kill');
    $this->post('/kill', App\Controllers\UserController::class . ':handleKill');
    $this->get('/logout', App\Controllers\UserController::class . ':logout');
    $this->get('/backtoadmin', App\Controllers\UserController::class . ':backtoadmin');
    $this->get('/code', App\Controllers\UserController::class . ':code');
    $this->get('/alipay', App\Controllers\UserController::class . ':alipay');
    $this->post('/code/f2fpay', App\Services\Payment::class . ':purchase');
    $this->get('/code/codepay', App\Services\Payment::class . ':purchase');
    $this->get('/code_check', App\Controllers\UserController::class . ':code_check');
    $this->post('/code', App\Controllers\UserController::class . ':codepost');
    $this->post('/gacheck', App\Controllers\UserController::class . ':GaCheck');
    $this->post('/gaset', App\Controllers\UserController::class . ':GaSet');
    $this->get('/gareset', App\Controllers\UserController::class . ':GaReset');
    $this->get('/telegram_reset', App\Controllers\UserController::class . ':telegram_reset');
    $this->get('/discord_reset', App\Controllers\UserController::class . ':discord_reset');
    $this->post('/resetport', App\Controllers\UserController::class . ':ResetPort');
    $this->post('/specifyport', App\Controllers\UserController::class . ':SpecifyPort');
    $this->post('/pacset', App\Controllers\UserController::class . ':PacSet');
    $this->post('/unblock', App\Controllers\UserController::class . ':Unblock');
    $this->get('/bought', App\Controllers\UserController::class . ':bought');
    $this->delete('/bought', App\Controllers\UserController::class . ':deleteBoughtGet');

    $this->get('/url_reset', App\Controllers\UserController::class . ':resetURL');

    $this->get('/inviteurl_reset', App\Controllers\UserController::class . ':resetInviteURL');

    //Reconstructed Payment System
    $this->post('/payment/purchase', App\Services\Payment::class . ':purchase');
    $this->get('/payment/return', App\Services\Payment::class . ':returnHTML');

    // Crypto Payment - BTC, ETH, EOS, BCH, LTC etch
    $this->post('/payment/bitpay/purchase', App\Services\BitPayment::class . ':purchase');
    $this->get('/payment/bitpay/return', App\Services\BitPayment::class . ':returnHTML');
})->add(new Auth());

$app->group('/payment', function () {
    $this->post('/notify', App\Services\Payment::class . ':notify');
    $this->post('/notify/{type}', App\Services\Payment::class . ':notify');
    $this->post('/status', App\Services\Payment::class . ':getStatus');

    $this->post('/bitpay/notify', App\Services\BitPayment::class . ':notify');
    $this->post('/bitpay/status', App\Services\BitPayment::class . ':getStatus');
});

// Auth
$app->group('/auth', function () {
    $this->get('/login', App\Controllers\AuthController::class . ':login');
    $this->post('/qrcode_check', App\Controllers\AuthController::class . ':qrcode_check');
    $this->post('/login', App\Controllers\AuthController::class . ':loginHandle');
    $this->post('/qrcode_login', App\Controllers\AuthController::class . ':qrcode_loginHandle');
    $this->get('/register', App\Controllers\AuthController::class . ':register');
    $this->post('/register', App\Controllers\AuthController::class . ':registerHandle');
    $this->post('/send', App\Controllers\AuthController::class . ':sendVerify');
    $this->get('/logout', App\Controllers\AuthController::class . ':logout');
    $this->get('/telegram_oauth', App\Controllers\AuthController::class . ':telegram_oauth');
    $this->get('/login_getCaptcha', App\Controllers\AuthController::class . ':getCaptcha');
})->add(new Guest());

// Password
$app->group('/password', function () {
    $this->get('/reset', App\Controllers\PasswordController::class . ':reset');
    $this->post('/reset', App\Controllers\PasswordController::class . ':handleReset');
    $this->get('/token/{token}', App\Controllers\PasswordController::class . ':token');
    $this->post('/token/{token}', App\Controllers\PasswordController::class . ':handleToken');
})->add(new Guest());

// Admin
$app->group('/admin', function () {
    $this->get('', App\Controllers\AdminController::class . ':index');
    $this->get('/', App\Controllers\AdminController::class . ':index');

    $this->get('/trafficlog', App\Controllers\AdminController::class . ':trafficLog');
    $this->post('/trafficlog/ajax', App\Controllers\AdminController::class . ':ajax_trafficLog');
    // Node Mange
    $this->get('/node', App\Controllers\Admin\NodeController::class . ':index');

    $this->get('/node/create', App\Controllers\Admin\NodeController::class . ':create');
    $this->post('/node', App\Controllers\Admin\NodeController::class . ':add');
    $this->get('/node/{id}/edit', App\Controllers\Admin\NodeController::class . ':edit');
    $this->put('/node/{id}', App\Controllers\Admin\NodeController::class . ':update');
    $this->delete('/node', App\Controllers\Admin\NodeController::class . ':delete');
    $this->post('/node/ajax', App\Controllers\Admin\NodeController::class . ':ajax');


    $this->get('/ticket', App\Controllers\Admin\TicketController::class . ':index');
    $this->get('/ticket/{id}/view', App\Controllers\Admin\TicketController::class . ':show');
    $this->put('/ticket/{id}', App\Controllers\Admin\TicketController::class . ':update');
    $this->post('/ticket/ajax', App\Controllers\Admin\TicketController::class . ':ajax');

    // Relay Mange
    $this->get('/relay', App\Controllers\Admin\RelayController::class . ':index');
    $this->get('/relay/create', App\Controllers\Admin\RelayController::class . ':create');
    $this->post('/relay', App\Controllers\Admin\RelayController::class . ':add');
    $this->get('/relay/{id}/edit', App\Controllers\Admin\RelayController::class . ':edit');
    $this->put('/relay/{id}', App\Controllers\Admin\RelayController::class . ':update');
    $this->delete('/relay', App\Controllers\Admin\RelayController::class . ':delete');
    $this->get('/relay/path_search/{id}', App\Controllers\Admin\RelayController::class . ':path_search');
    $this->post('/relay/ajax', App\Controllers\Admin\RelayController::class . ':ajax_relay');

    // Shop Mange
    $this->get('/shop', App\Controllers\Admin\ShopController::class . ':index');
    $this->post('/shop/ajax', App\Controllers\Admin\ShopController::class . ':ajax_shop');

    $this->get('/bought', App\Controllers\Admin\ShopController::class . ':bought');
    $this->delete('/bought', App\Controllers\Admin\ShopController::class . ':deleteBoughtGet');
    $this->post('/bought/ajax', App\Controllers\Admin\ShopController::class . ':ajax_bought');

    $this->get('/shop/create', App\Controllers\Admin\ShopController::class . ':create');
    $this->post('/shop', App\Controllers\Admin\ShopController::class . ':add');
    $this->get('/shop/{id}/edit', App\Controllers\Admin\ShopController::class . ':edit');
    $this->put('/shop/{id}', App\Controllers\Admin\ShopController::class . ':update');
    $this->delete('/shop', App\Controllers\Admin\ShopController::class . ':deleteGet');

    // Ann Mange
    $this->get('/announcement', App\Controllers\Admin\AnnController::class . ':index');
    $this->get('/announcement/create', App\Controllers\Admin\AnnController::class . ':create');
    $this->post('/announcement', App\Controllers\Admin\AnnController::class . ':add');
    $this->get('/announcement/{id}/edit', App\Controllers\Admin\AnnController::class . ':edit');
    $this->put('/announcement/{id}', App\Controllers\Admin\AnnController::class . ':update');
    $this->delete('/announcement', App\Controllers\Admin\AnnController::class . ':delete');
    $this->post('/announcement/ajax', App\Controllers\Admin\AnnController::class . ':ajax');

    // Detect Mange
    $this->get('/detect', App\Controllers\Admin\DetectController::class . ':index');
    $this->get('/detect/create', App\Controllers\Admin\DetectController::class . ':create');
    $this->post('/detect', App\Controllers\Admin\DetectController::class . ':add');
    $this->get('/detect/{id}/edit', App\Controllers\Admin\DetectController::class . ':edit');
    $this->put('/detect/{id}', App\Controllers\Admin\DetectController::class . ':update');
    $this->delete('/detect', App\Controllers\Admin\DetectController::class . ':delete');
    $this->get('/detect/log', App\Controllers\Admin\DetectController::class . ':log');
    $this->post('/detect/ajax', App\Controllers\Admin\DetectController::class . ':ajax_rule');
    $this->post('/detect/log/ajax', App\Controllers\Admin\DetectController::class . ':ajax_log');

    $this->get('/auto', App\Controllers\Admin\AutoController::class . ':index');
    $this->get('/auto/create', App\Controllers\Admin\AutoController::class . ':create');
    $this->post('/auto', App\Controllers\Admin\AutoController::class . ':add');
    $this->delete('/auto', App\Controllers\Admin\AutoController::class . ':delete');
    $this->post('/auto/ajax', App\Controllers\Admin\AutoController::class . ':ajax');

    // IP Mange
    $this->get('/block', App\Controllers\Admin\IpController::class . ':block');
    $this->get('/unblock', App\Controllers\Admin\IpController::class . ':unblock');
    $this->post('/unblock', App\Controllers\Admin\IpController::class . ':doUnblock');
    $this->get('/login', App\Controllers\Admin\IpController::class . ':index');
    $this->get('/alive', App\Controllers\Admin\IpController::class . ':alive');
    $this->post('/block/ajax', App\Controllers\Admin\IpController::class . ':ajax_block');
    $this->post('/unblock/ajax', App\Controllers\Admin\IpController::class . ':ajax_unblock');
    $this->post('/login/ajax', App\Controllers\Admin\IpController::class . ':ajax_login');
    $this->post('/alive/ajax', App\Controllers\Admin\IpController::class . ':ajax_alive');

    // Code Mange
    $this->get('/code', App\Controllers\Admin\CodeController::class . ':index');
    $this->get('/code/create', App\Controllers\Admin\CodeController::class . ':create');
    $this->post('/code', App\Controllers\Admin\CodeController::class . ':add');
    $this->get('/donate/create', App\Controllers\Admin\CodeController::class . ':donate_create');
    $this->post('/donate', App\Controllers\Admin\CodeController::class . ':donate_add');
    $this->post('/code/ajax', App\Controllers\Admin\CodeController::class . ':ajax_code');

    // User Mange
    $this->get('/user', App\Controllers\Admin\UserController::class . ':index');
    $this->get('/user/{id}/edit', App\Controllers\Admin\UserController::class . ':edit');
    $this->put('/user/{id}', App\Controllers\Admin\UserController::class . ':update');
    $this->delete('/user', App\Controllers\Admin\UserController::class . ':delete');
    $this->post('/user/changetouser', App\Controllers\Admin\UserController::class . ':changetouser');
    $this->post('/user/ajax', App\Controllers\Admin\UserController::class . ':ajax');
    $this->post('/user/create', App\Controllers\Admin\UserController::class . ':createNewUser');
    $this->post('/user/buy', App\Controllers\Admin\UserController::class . ':buy');


    $this->get('/coupon', App\Controllers\AdminController::class . ':coupon');
    $this->post('/coupon', App\Controllers\AdminController::class . ':addCoupon');
    $this->post('/coupon/ajax', App\Controllers\AdminController::class . ':ajax_coupon');

    $this->get('/profile', App\Controllers\AdminController::class . ':profile');
    $this->get('/invite', App\Controllers\AdminController::class . ':invite');
    $this->post('/invite', App\Controllers\AdminController::class . ':addInvite');
    $this->get('/sys', App\Controllers\AdminController::class . ':sys');
    $this->get('/logout', App\Controllers\AdminController::class . ':logout');
    $this->post('/payback/ajax', App\Controllers\AdminController::class . ':ajax_payback');
})->add(new Admin());

// API
$app->group('/api', function () {
    $this->get('/token/{token}', App\Controllers\ApiController::class . ':token');
    $this->post('/token', App\Controllers\ApiController::class . ':newToken');
    $this->get('/node', App\Controllers\ApiController::class . ':node')->add(new Api());
    $this->get('/user/{id}', App\Controllers\ApiController::class . ':userInfo')->add(new Api());
    $this->get('/sublink', App\Controllers\Client\ClientApiController::class . ':GetSubLink');
});

// mu
$app->group('/mu', function () {
    $this->get('/users', App\Controllers\Mu\UserController::class . ':index');
    $this->post('/users/{id}/traffic', App\Controllers\Mu\UserController::class . ':addTraffic');
    $this->post('/nodes/{id}/online_count', App\Controllers\Mu\NodeController::class . ':onlineUserLog');
    $this->post('/nodes/{id}/info', App\Controllers\Mu\NodeController::class . ':info');
})->add(new Mu());

// mu
$app->group('/mod_mu', function () {
    $this->get('/nodes/{id}/info', App\Controllers\Mod_Mu\NodeController::class . ':get_info');
    $this->get('/users', App\Controllers\Mod_Mu\UserController::class . ':index');
    $this->post('/users/traffic', App\Controllers\Mod_Mu\UserController::class . ':addTraffic');
    $this->post('/users/aliveip', App\Controllers\Mod_Mu\UserController::class . ':addAliveIp');
    $this->post('/users/detectlog', App\Controllers\Mod_Mu\UserController::class . ':addDetectLog');
    $this->post('/nodes/{id}/info', App\Controllers\Mod_Mu\NodeController::class . ':info');

    $this->get('/nodes', App\Controllers\Mod_Mu\NodeController::class . ':get_all_info');

    $this->get('/func/detect_rules', App\Controllers\Mod_Mu\FuncController::class . ':get_detect_logs');
    $this->get('/func/relay_rules', App\Controllers\Mod_Mu\FuncController::class . ':get_relay_rules');
    $this->post('/func/block_ip', App\Controllers\Mod_Mu\FuncController::class . ':addBlockIp');
    $this->get('/func/block_ip', App\Controllers\Mod_Mu\FuncController::class . ':get_blockip');
    $this->get('/func/unblock_ip', App\Controllers\Mod_Mu\FuncController::class . ':get_unblockip');
    $this->post('/func/speedtest', App\Controllers\Mod_Mu\FuncController::class . ':addSpeedtest');
    $this->get('/func/autoexec', App\Controllers\Mod_Mu\FuncController::class . ':get_autoexec');
    $this->post('/func/autoexec', App\Controllers\Mod_Mu\FuncController::class . ':addAutoexec');

    $this->get('/func/ping', App\Controllers\Mod_Mu\FuncController::class . ':ping');
    //============================================
})->add(new Mod_Mu());

// res
$app->group('/res', function () {
    $this->get('/captcha/{id}', App\Controllers\ResController::class . ':captcha');
});


$app->group('/link', function () {
    $this->get('/{token}', App\Controllers\LinkController::class . ':GetContent');
});

$app->group('/user', function () {
    $this->post('/doiam', App\Services\Payment::class . ':purchase');
})->add(new Auth());
$app->group('/doiam', function () {
    $this->post('/callback/{type}', App\Services\Payment::class . ':notify');
    $this->get('/return/alipay', App\Services\Payment::class . ':returnHTML');
    $this->post('/status', App\Services\Payment::class . ':getStatus');
});

// Vue

$app->get('/logout', App\Controllers\VueController::class . ':vuelogout');
$app->get('/globalconfig', App\Controllers\VueController::class . ':getGlobalConfig');
$app->get('/getuserinfo', App\Controllers\VueController::class . ':getUserInfo');
$app->post('/getuserinviteinfo', App\Controllers\VueController::class . ':getUserInviteInfo');
$app->get('/getusershops', App\Controllers\VueController::class . ':getUserShops');
$app->get('/getallresourse', App\Controllers\VueController::class . ':getAllResourse');
$app->get('/getnewsubtoken', App\Controllers\VueController::class . ':getNewSubToken');
$app->get('/getnewinvotecode', App\Controllers\VueController::class . ':getNewInviteCode');
$app->get('/gettransfer', App\Controllers\VueController::class . ':getTransfer');
$app->get('/getCaptcha', App\Controllers\VueController::class . ':getCaptcha');
$app->post('/getChargeLog', App\Controllers\VueController::class . ':getChargeLog');
$app->get('/getnodelist', App\Controllers\VueController::class . ':getNodeList');

/**
 * chenPay
 */
$app->group('/user', function () {
    $this->get('/chenPay', App\Services\Payment::class . ':purchase');
    $this->get('/orderDelete', App\Controllers\UserController::class . ':orderDelete');
})->add(new Auth());
$app->group('/chenPay', function () {
    $this->get('/status', App\Services\Payment::class . ':getStatus');
});
$app->group('/admin', function () {
    $this->get('/editConfig', App\Controllers\AdminController::class . ':editConfig');
    $this->post('/saveConfig', App\Controllers\AdminController::class . ':saveConfig');
})->add(new Admin());
// chenPay end

// Run Slim Routes for App
$app->run();
