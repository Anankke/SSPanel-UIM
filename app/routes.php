<?php

declare(strict_types=1);

use App\Middleware\Admin;
use App\Middleware\Auth;
use App\Middleware\AuthorizationBearer;
use App\Middleware\Guest;
use App\Middleware\NodeToken;
use Slim\Routing\RouteCollectorProxy;

return function (Slim\App $app): void {
    // Home
    $app->get('/', App\Controllers\HomeController::class . ':index');
    $app->get('/404', App\Controllers\HomeController::class . ':page404');
    $app->get('/405', App\Controllers\HomeController::class . ':page405');
    $app->get('/500', App\Controllers\HomeController::class . ':page500');
    $app->get('/tos', App\Controllers\HomeController::class . ':tos');
    $app->get('/staff', App\Controllers\HomeController::class . ':staff');

    // other
    $app->post('/notify', App\Controllers\HomeController::class . ':notify');

    // Telegram
    $app->post('/telegram_callback', App\Controllers\HomeController::class . ':telegram');

    // User Center
    $app->group('/user', function (RouteCollectorProxy $group): void {
        $group->get('', App\Controllers\UserController::class . ':index');
        $group->get('/', App\Controllers\UserController::class . ':index');

        $group->post('/checkin', App\Controllers\UserController::class . ':doCheckin');

        $group->get('/announcement', App\Controllers\UserController::class . ':announcement');
        $group->get('/docs', App\Controllers\UserController::class . ':docs');

        $group->get('/media', App\Controllers\UserController::class . ':media');

        $group->get('/profile', App\Controllers\UserController::class . ':profile');
        $group->get('/invite', App\Controllers\UserController::class . ':invite');
        $group->get('/banned', App\Controllers\UserController::class . ':banned');

        $group->get('/server', App\Controllers\User\ServerController::class . ':userServerPage');

        $group->get('/detect', App\Controllers\User\DetectController::class . ':detectIndex');
        $group->get('/detect/log', App\Controllers\User\DetectController::class . ':detectLog');

        $group->get('/shop', App\Controllers\User\ShopController::class . ':shop');
        $group->post('/coupon_check', App\Controllers\User\ShopController::class . ':couponCheck');
        $group->post('/buy', App\Controllers\User\ShopController::class . ':buy');
        $group->post('/buy_traffic_package', App\Controllers\User\ShopController::class . ':buyTrafficPackage');

        $group->get('/ticket', App\Controllers\User\TicketController::class . ':ticket');
        $group->get('/ticket/create', App\Controllers\User\TicketController::class . ':ticketCreate');
        $group->post('/ticket', App\Controllers\User\TicketController::class . ':ticketAdd');
        $group->get('/ticket/{id}/view', App\Controllers\User\TicketController::class . ':ticketView');
        $group->put('/ticket/{id}', App\Controllers\User\TicketController::class . ':ticketUpdate');

        $group->post('/buy_invite', App\Controllers\UserController::class . ':buyInvite');
        $group->post('/custom_invite', App\Controllers\UserController::class . ':customInvite');
        $group->get('/edit', App\Controllers\UserController::class . ':edit');
        $group->post('/email', App\Controllers\UserController::class . ':updateEmail');
        $group->post('/username', App\Controllers\UserController::class . ':updateUsername');
        $group->post('/password', App\Controllers\UserController::class . ':updatePassword');
        $group->post('/send', App\Controllers\AuthController::class . ':sendVerify');
        $group->post('/contact_update', App\Controllers\UserController::class . ':updateContact');
        $group->post('/ssr', App\Controllers\UserController::class . ':updateSSR');
        $group->post('/theme', App\Controllers\UserController::class . ':updateTheme');
        $group->post('/mail', App\Controllers\UserController::class . ':updateMail');
        $group->post('/passwd_reset', App\Controllers\UserController::class . ':resetPasswd');
        $group->post('/method', App\Controllers\UserController::class . ':updateMethod');
        $group->get('/trafficlog', App\Controllers\UserController::class . ':trafficLog');
        $group->get('/kill', App\Controllers\UserController::class . ':kill');
        $group->post('/kill', App\Controllers\UserController::class . ':handleKill');
        $group->get('/logout', App\Controllers\UserController::class . ':logout');
        $group->get('/backtoadmin', App\Controllers\UserController::class . ':backtoadmin');
        $group->get('/code', App\Controllers\UserController::class . ':code');

        $group->get('/code_check', App\Controllers\UserController::class . ':codeCheck');
        $group->post('/code', App\Controllers\UserController::class . ':codePost');
        $group->post('/ga_check', App\Controllers\UserController::class . ':checkGa');
        $group->post('/ga_set', App\Controllers\UserController::class . ':setGa');
        $group->get('/ga_reset', App\Controllers\UserController::class . ':resetGa');
        $group->post('/telegram_reset', App\Controllers\UserController::class . ':resetTelegram');
        $group->post('/unblock', App\Controllers\UserController::class . ':unblock');
        $group->get('/bought', App\Controllers\UserController::class . ':bought');
        $group->delete('/bought', App\Controllers\UserController::class . ':deleteBoughtGet');
        $group->post('/url_reset', App\Controllers\UserController::class . ':resetURL');
        $group->put('/invite', App\Controllers\UserController::class . ':resetInviteURL');

        //深色模式
        $group->post('/switch_theme_mode', App\Controllers\UserController::class . ':switchThemeMode');

        // 订阅记录
        $group->get('/subscribe_log', App\Controllers\UserController::class . ':subscribeLog');

        // getUserAllURL
        $group->get('/getUserAllURL', App\Controllers\UserController::class . ':getUserAllURL');

        //Reconstructed Payment System
        $group->post('/payment/purchase/{type}', App\Services\Payment::class . ':purchase');
        $group->get('/payment/purchase/{type}', App\Services\Payment::class . ':purchase');
        $group->get('/payment/return/{type}', App\Services\Payment::class . ':returnHTML');
    })->add(new Auth());

    $app->group('/payment', function (RouteCollectorProxy $group): void {
        $group->get('/notify/{type}', App\Services\Payment::class . ':notify');
        $group->post('/notify/{type}', App\Services\Payment::class . ':notify');
        $group->post('/status/{type}', App\Services\Payment::class . ':getStatus');
    });

    // Auth
    $app->group('/auth', function (RouteCollectorProxy $group): void {
        $group->get('/login', App\Controllers\AuthController::class . ':login');
        $group->post('/qrcode_check', App\Controllers\AuthController::class . ':qrcodeCheck');
        $group->post('/login', App\Controllers\AuthController::class . ':loginHandle');
        $group->post('/qrcode_login', App\Controllers\AuthController::class . ':qrcodeLoginHandle');
        $group->get('/register', App\Controllers\AuthController::class . ':register');
        $group->post('/register', App\Controllers\AuthController::class . ':registerHandle');
        $group->post('/send', App\Controllers\AuthController::class . ':sendVerify');
        $group->get('/logout', App\Controllers\AuthController::class . ':logout');
        $group->get('/telegram_oauth', App\Controllers\AuthController::class . ':telegramOauth');
    })->add(new Guest());

    // Password
    $app->group('/password', function (RouteCollectorProxy $group): void {
        $group->get('/reset', App\Controllers\PasswordController::class . ':reset');
        $group->post('/reset', App\Controllers\PasswordController::class . ':handleReset');
        $group->get('/token/{token}', App\Controllers\PasswordController::class . ':token');
        $group->post('/token/{token}', App\Controllers\PasswordController::class . ':handleToken');
    })->add(new Guest());

    // Admin
    $app->group('/admin', function (RouteCollectorProxy $group): void {
        $group->get('', App\Controllers\AdminController::class . ':index');
        $group->get('/', App\Controllers\AdminController::class . ':index');

        $group->get('/sys', App\Controllers\AdminController::class . ':sys');
        $group->get('/invite', App\Controllers\AdminController::class . ':invite');
        $group->post('/invite', App\Controllers\AdminController::class . ':addInvite');
        $group->post('/chginvite', App\Controllers\AdminController::class . ':chgInvite');
        $group->post('/payback/ajax', App\Controllers\AdminController::class . ':ajaxPayback');

        // Node Mange
        $group->get('/node', App\Controllers\Admin\NodeController::class . ':index');
        $group->get('/node/create', App\Controllers\Admin\NodeController::class . ':create');
        $group->post('/node', App\Controllers\Admin\NodeController::class . ':add');
        $group->get('/node/{id}/edit', App\Controllers\Admin\NodeController::class . ':edit');
        $group->post('/node/{id}/password_reset', App\Controllers\Admin\NodeController::class . ':resetNodePassword');
        $group->post('/node/{id}/copy', App\Controllers\Admin\NodeController::class . ':copy');
        $group->put('/node/{id}', App\Controllers\Admin\NodeController::class . ':update');
        $group->delete('/node/{id}', App\Controllers\Admin\NodeController::class . ':delete');
        $group->post('/node/ajax', App\Controllers\Admin\NodeController::class . ':ajax');

        // Ticket Mange
        $group->get('/ticket', App\Controllers\Admin\TicketController::class . ':index');
        $group->post('/ticket', App\Controllers\Admin\TicketController::class . ':add');
        $group->get('/ticket/{id}/view', App\Controllers\Admin\TicketController::class . ':ticketView');
        $group->put('/ticket/{id}/close', App\Controllers\Admin\TicketController::class . ':close');
        $group->put('/ticket/{id}', App\Controllers\Admin\TicketController::class . ':update');
        $group->delete('/ticket/{id}', App\Controllers\Admin\TicketController::class . ':delete');
        $group->post('/ticket/ajax', App\Controllers\Admin\TicketController::class . ':ajax');

        // Shop Mange
        $group->get('/shop', App\Controllers\Admin\ShopController::class . ':index');
        $group->post('/shop/ajax', App\Controllers\Admin\ShopController::class . ':ajaxShop');
        $group->get('/shop/create', App\Controllers\Admin\ShopController::class . ':create');
        $group->post('/shop', App\Controllers\Admin\ShopController::class . ':add');
        $group->get('/shop/{id}/edit', App\Controllers\Admin\ShopController::class . ':edit');
        $group->put('/shop/{id}', App\Controllers\Admin\ShopController::class . ':update');
        $group->delete('/shop', App\Controllers\Admin\ShopController::class . ':deleteGet');

        // Bought Mange
        $group->get('/bought', App\Controllers\Admin\ShopController::class . ':bought');
        $group->delete('/bought', App\Controllers\Admin\ShopController::class . ':deleteBoughtGet');
        $group->post('/bought/ajax', App\Controllers\Admin\ShopController::class . ':ajaxBought');

        // Ann Mange
        $group->get('/announcement', App\Controllers\Admin\AnnController::class . ':index');
        $group->get('/announcement/create', App\Controllers\Admin\AnnController::class . ':create');
        $group->post('/announcement', App\Controllers\Admin\AnnController::class . ':add');
        $group->get('/announcement/{id}/edit', App\Controllers\Admin\AnnController::class . ':edit');
        $group->put('/announcement/{id}', App\Controllers\Admin\AnnController::class . ':update');
        $group->delete('/announcement/{id}', App\Controllers\Admin\AnnController::class . ':delete');
        $group->post('/announcement/ajax', App\Controllers\Admin\AnnController::class . ':ajax');

        // Detect Mange
        $group->get('/detect', App\Controllers\Admin\DetectController::class . ':index');
        $group->get('/detect/create', App\Controllers\Admin\DetectController::class . ':create');
        $group->post('/detect', App\Controllers\Admin\DetectController::class . ':add');
        $group->get('/detect/{id}/edit', App\Controllers\Admin\DetectController::class . ':edit');
        $group->put('/detect/{id}', App\Controllers\Admin\DetectController::class . ':update');
        $group->delete('/detect', App\Controllers\Admin\DetectController::class . ':delete');
        $group->get('/detect/log', App\Controllers\Admin\DetectController::class . ':log');
        $group->post('/detect/ajax', App\Controllers\Admin\DetectController::class . ':ajaxRule');
        $group->post('/detect/log/ajax', App\Controllers\Admin\DetectController::class . ':ajaxLog');

        // IP Mange
        $group->get('/login', App\Controllers\Admin\IpController::class . ':login');
        $group->get('/alive', App\Controllers\Admin\IpController::class . ':alive');
        $group->post('/login/ajax', App\Controllers\Admin\IpController::class . ':ajaxLogin');
        $group->post('/alive/ajax', App\Controllers\Admin\IpController::class . ':ajaxAlive');

        // Code Mange
        $group->get('/code', App\Controllers\Admin\CodeController::class . ':index');
        $group->get('/code/create', App\Controllers\Admin\CodeController::class . ':create');
        $group->post('/code', App\Controllers\Admin\CodeController::class . ':add');
        $group->post('/code/ajax', App\Controllers\Admin\CodeController::class . ':ajaxCode');

        // User Mange
        $group->get('/user', App\Controllers\Admin\UserController::class . ':index');
        $group->get('/user/{id}/edit', App\Controllers\Admin\UserController::class . ':edit');
        $group->put('/user/{id}', App\Controllers\Admin\UserController::class . ':update');
        $group->post('/user/changetouser', App\Controllers\Admin\UserController::class . ':changetouser');
        $group->post('/user/create', App\Controllers\Admin\UserController::class . ':createNewUser');
        $group->delete('/user/{id}', App\Controllers\Admin\UserController::class . ':delete');
        $group->post('/user/ajax', App\Controllers\Admin\UserController::class . ':ajax');

        // Coupon Mange
        $group->get('/coupon', App\Controllers\AdminController::class . ':coupon');
        $group->post('/coupon', App\Controllers\AdminController::class . ':addCoupon');
        $group->post('/coupon/ajax', App\Controllers\AdminController::class . ':ajaxCoupon');

        // Subscribe Log Mange
        $group->get('/subscribe', App\Controllers\Admin\SubscribeLogController::class . ':index');
        $group->post('/subscribe/ajax', App\Controllers\Admin\SubscribeLogController::class . ':ajaxSubscribeLog');

        // Traffic Log Mange
        $group->get('/trafficlog', App\Controllers\Admin\TrafficLogController::class . ':index');
        $group->post('/trafficlog/ajax', App\Controllers\Admin\TrafficLogController::class . ':ajaxTrafficLog');

        // Detect Ban Mange
        $group->get('/detect/ban', App\Controllers\Admin\DetectBanLogController::class . ':index');
        $group->post('/detect/ban/ajax', App\Controllers\Admin\DetectBanLogController::class . ':ajaxLog');

        // 设置中心
        $group->get('/setting', App\Controllers\Admin\SettingController::class . ':index');
        $group->post('/setting', App\Controllers\Admin\SettingController::class . ':save');
        $group->post('/setting/email', App\Controllers\Admin\SettingController::class . ':test');
        $group->post('/setting/payment', App\Controllers\Admin\SettingController::class . ':payment');
    })->add(new Admin());

    if ($_ENV['enableAdminApi']) {
        $app->group('/admin/api', function (RouteCollectorProxy $group): void {
            $group->get('/nodes', App\Controllers\Admin\ApiController::class . ':getNodeList');
            $group->get('/node/{id}', App\Controllers\Admin\ApiController::class . ':getNodeInfo');
            $group->get('/ping', App\Controllers\Admin\ApiController::class . ':ping');

            // Re-bind controller, bypass admin token require
            $group->post('/node', App\Controllers\Admin\NodeController::class . ':add');
            $group->put('/node/{id}', App\Controllers\Admin\NodeController::class . ':update');
            $group->delete('/node', App\Controllers\Admin\NodeController::class . ':delete');
        })->add(new AuthorizationBearer($_ENV['adminApiToken']));
    }

    // mu
    $app->group('/mod_mu', function (RouteCollectorProxy $group): void {
        // 流媒体检测
        $group->post('/media/saveReport', App\Controllers\Node\NodeController::class . ':saveReport');
        // 节点
        $group->get('/nodes', App\Controllers\Node\NodeController::class . ':getAllInfo');
        $group->get('/nodes/{id}/info', App\Controllers\Node\NodeController::class . ':getInfo');
        $group->post('/nodes/{id}/info', App\Controllers\Node\NodeController::class . ':info');
        // 用户
        $group->get('/users', App\Controllers\Node\UserController::class . ':index');
        $group->post('/users/traffic', App\Controllers\Node\UserController::class . ':addTraffic');
        $group->post('/users/aliveip', App\Controllers\Node\UserController::class . ':addAliveIp');
        $group->post('/users/detectlog', App\Controllers\Node\UserController::class . ':addDetectLog');
        // 审计 & 杂七杂八的功能
        $group->get('/func/detect_rules', App\Controllers\Node\FuncController::class . ':getDetectLogs');
        $group->get('/func/ping', App\Controllers\Node\FuncController::class . ':ping');
        // Dummy API for old version
        $group->post('/func/block_ip', App\Controllers\Node\FuncController::class . ':addBlockIp');
        $group->get('/func/block_ip', App\Controllers\Node\FuncController::class . ':getBlockip');
        $group->get('/func/unblock_ip', App\Controllers\Node\FuncController::class . ':getUnblockip');
    })->add(new NodeToken());

    $app->get('/link/{token}', App\Controllers\LinkController::class . ':getContent');

    //通用訂閲
    $app->get('/sub/{token}/{subtype}', App\Controllers\SubController::class . ':getContent');
};
