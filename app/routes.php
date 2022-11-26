<?php

declare(strict_types=1);

use App\Middleware\Admin;
use App\Middleware\Auth;
use App\Middleware\AuthorizationBearer;
use App\Middleware\Guest;
use App\Middleware\NodeToken;
use Slim\App as SlimApp;

return function (SlimApp $app): void {
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
    $app->group('/user', function (): void {
        $this->get('', App\Controllers\UserController::class . ':index');
        $this->get('/', App\Controllers\UserController::class . ':index');

        $this->post('/checkin', App\Controllers\UserController::class . ':doCheckin');

        $this->get('/announcement', App\Controllers\UserController::class . ':announcement');
        $this->get('/docs', App\Controllers\UserController::class . ':docs');

        $this->get('/media', App\Controllers\UserController::class . ':media');

        $this->get('/profile', App\Controllers\UserController::class . ':profile');
        $this->get('/invite', App\Controllers\UserController::class . ':invite');
        $this->get('/banned', App\Controllers\UserController::class . ':banned');

        $this->get('/server', App\Controllers\User\ServerController::class . ':userServerPage');

        $this->get('/detect', App\Controllers\User\DetectController::class . ':detectIndex');
        $this->get('/detect/log', App\Controllers\User\DetectController::class . ':detectLog');

        $this->get('/shop', App\Controllers\User\ShopController::class . ':shop');
        $this->post('/coupon_check', App\Controllers\User\ShopController::class . ':couponCheck');
        $this->post('/buy', App\Controllers\User\ShopController::class . ':buy');
        $this->post('/buy_traffic_package', App\Controllers\User\ShopController::class . ':buyTrafficPackage');

        $this->get('/ticket', App\Controllers\User\TicketController::class . ':ticket');
        $this->get('/ticket/create', App\Controllers\User\TicketController::class . ':ticketCreate');
        $this->post('/ticket', App\Controllers\User\TicketController::class . ':ticketAdd');
        $this->get('/ticket/{id}/view', App\Controllers\User\TicketController::class . ':ticketView');
        $this->put('/ticket/{id}', App\Controllers\User\TicketController::class . ':ticketUpdate');

        $this->post('/buy_invite', App\Controllers\UserController::class . ':buyInvite');
        $this->post('/custom_invite', App\Controllers\UserController::class . ':customInvite');
        $this->get('/edit', App\Controllers\UserController::class . ':edit');
        $this->post('/email', App\Controllers\UserController::class . ':updateEmail');
        $this->post('/username', App\Controllers\UserController::class . ':updateUsername');
        $this->post('/password', App\Controllers\UserController::class . ':updatePassword');
        $this->post('/send', App\Controllers\AuthController::class . ':sendVerify');
        $this->post('/contact_update', App\Controllers\UserController::class . ':updateContact');
        $this->post('/ssr', App\Controllers\UserController::class . ':updateSSR');
        $this->post('/theme', App\Controllers\UserController::class . ':updateTheme');
        $this->post('/mail', App\Controllers\UserController::class . ':updateMail');
        $this->post('/passwd_reset', App\Controllers\UserController::class . ':resetPasswd');
        $this->post('/method', App\Controllers\UserController::class . ':updateMethod');
        $this->get('/trafficlog', App\Controllers\UserController::class . ':trafficLog');
        $this->get('/kill', App\Controllers\UserController::class . ':kill');
        $this->post('/kill', App\Controllers\UserController::class . ':handleKill');
        $this->get('/logout', App\Controllers\UserController::class . ':logout');
        $this->get('/backtoadmin', App\Controllers\UserController::class . ':backtoadmin');
        $this->get('/code', App\Controllers\UserController::class . ':code');

        $this->get('/code_check', App\Controllers\UserController::class . ':codeCheck');
        $this->post('/code', App\Controllers\UserController::class . ':codePost');
        $this->post('/ga_check', App\Controllers\UserController::class . ':checkGa');
        $this->post('/ga_set', App\Controllers\UserController::class . ':setGa');
        $this->get('/ga_reset', App\Controllers\UserController::class . ':resetGa');
        $this->post('/telegram_reset', App\Controllers\UserController::class . ':resetTelegram');
        $this->post('/unblock', App\Controllers\UserController::class . ':unblock');
        $this->get('/bought', App\Controllers\UserController::class . ':bought');
        $this->delete('/bought', App\Controllers\UserController::class . ':deleteBoughtGet');
        $this->post('/url_reset', App\Controllers\UserController::class . ':resetURL');
        $this->put('/invite', App\Controllers\UserController::class . ':resetInviteURL');

        //深色模式
        $this->post('/switch_theme_mode', App\Controllers\UserController::class . ':switchThemeMode');

        // 订阅记录
        $this->get('/subscribe_log', App\Controllers\UserController::class . ':subscribeLog');

        // getUserAllURL
        $this->get('/getUserAllURL', App\Controllers\UserController::class . ':getUserAllURL');

        //Reconstructed Payment System
        $this->post('/payment/purchase/{type}', App\Services\Payment::class . ':purchase');
        $this->get('/payment/purchase/{type}', App\Services\Payment::class . ':purchase');
        $this->get('/payment/return/{type}', App\Services\Payment::class . ':returnHTML');
    })->add(new Auth());

    $app->group('/payment', function (): void {
        $this->get('/notify/{type}', App\Services\Payment::class . ':notify');
        $this->post('/notify/{type}', App\Services\Payment::class . ':notify');
        $this->post('/status/{type}', App\Services\Payment::class . ':getStatus');
    });

    // Auth
    $app->group('/auth', function (): void {
        $this->get('/login', App\Controllers\AuthController::class . ':login');
        $this->post('/qrcode_check', App\Controllers\AuthController::class . ':qrcodeCheck');
        $this->post('/login', App\Controllers\AuthController::class . ':loginHandle');
        $this->post('/qrcode_login', App\Controllers\AuthController::class . ':qrcodeLoginHandle');
        $this->get('/register', App\Controllers\AuthController::class . ':register');
        $this->post('/register', App\Controllers\AuthController::class . ':registerHandle');
        $this->post('/send', App\Controllers\AuthController::class . ':sendVerify');
        $this->get('/logout', App\Controllers\AuthController::class . ':logout');
        $this->get('/telegram_oauth', App\Controllers\AuthController::class . ':telegramOauth');
    })->add(new Guest());

    // Password
    $app->group('/password', function (): void {
        $this->get('/reset', App\Controllers\PasswordController::class . ':reset');
        $this->post('/reset', App\Controllers\PasswordController::class . ':handleReset');
        $this->get('/token/{token}', App\Controllers\PasswordController::class . ':token');
        $this->post('/token/{token}', App\Controllers\PasswordController::class . ':handleToken');
    })->add(new Guest());

    // Admin
    $app->group('/admin', function (): void {
        $this->get('', App\Controllers\AdminController::class . ':index');
        $this->get('/', App\Controllers\AdminController::class . ':index');

        $this->get('/sys', App\Controllers\AdminController::class . ':sys');
        $this->get('/invite', App\Controllers\AdminController::class . ':invite');
        $this->post('/invite', App\Controllers\AdminController::class . ':addInvite');
        $this->post('/chginvite', App\Controllers\AdminController::class . ':chgInvite');
        $this->post('/payback/ajax', App\Controllers\AdminController::class . ':ajaxPayback');

        // Node Mange
        $this->get('/node', App\Controllers\Admin\NodeController::class . ':index');
        $this->get('/node/create', App\Controllers\Admin\NodeController::class . ':create');
        $this->post('/node', App\Controllers\Admin\NodeController::class . ':add');
        $this->get('/node/{id}/edit', App\Controllers\Admin\NodeController::class . ':edit');
        $this->post('/node/{id}/password_reset', App\Controllers\Admin\NodeController::class . ':resetNodePassword');
        $this->put('/node/{id}', App\Controllers\Admin\NodeController::class . ':update');
        $this->delete('/node', App\Controllers\Admin\NodeController::class . ':delete');
        $this->post('/node/ajax', App\Controllers\Admin\NodeController::class . ':ajax');

        // Ticket Mange
        $this->get('/ticket', App\Controllers\Admin\TicketController::class . ':index');
        $this->post('/ticket', App\Controllers\Admin\TicketController::class . ':add');
        $this->get('/ticket/{id}/view', App\Controllers\Admin\TicketController::class . ':ticketView');
        $this->put('/ticket/{id}/close', App\Controllers\Admin\TicketController::class . ':close');
        $this->put('/ticket/{id}', App\Controllers\Admin\TicketController::class . ':update');
        $this->delete('/ticket/{id}', App\Controllers\Admin\TicketController::class . ':delete');
        $this->post('/ticket/ajax', App\Controllers\Admin\TicketController::class . ':ajax');

        // Shop Mange
        $this->get('/shop', App\Controllers\Admin\ShopController::class . ':index');
        $this->post('/shop/ajax', App\Controllers\Admin\ShopController::class . ':ajaxShop');
        $this->get('/shop/create', App\Controllers\Admin\ShopController::class . ':create');
        $this->post('/shop', App\Controllers\Admin\ShopController::class . ':add');
        $this->get('/shop/{id}/edit', App\Controllers\Admin\ShopController::class . ':edit');
        $this->put('/shop/{id}', App\Controllers\Admin\ShopController::class . ':update');
        $this->delete('/shop', App\Controllers\Admin\ShopController::class . ':deleteGet');

        // Bought Mange
        $this->get('/bought', App\Controllers\Admin\ShopController::class . ':bought');
        $this->delete('/bought', App\Controllers\Admin\ShopController::class . ':deleteBoughtGet');
        $this->post('/bought/ajax', App\Controllers\Admin\ShopController::class . ':ajaxBought');

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
        $this->post('/detect/ajax', App\Controllers\Admin\DetectController::class . ':ajaxRule');
        $this->post('/detect/log/ajax', App\Controllers\Admin\DetectController::class . ':ajaxLog');

        // IP Mange
        $this->get('/login', App\Controllers\Admin\IpController::class . ':index');
        $this->get('/alive', App\Controllers\Admin\IpController::class . ':alive');
        $this->post('/login/ajax', App\Controllers\Admin\IpController::class . ':ajaxLogin');
        $this->post('/alive/ajax', App\Controllers\Admin\IpController::class . ':ajaxAlive');

        // Code Mange
        $this->get('/code', App\Controllers\Admin\CodeController::class . ':index');
        $this->get('/code/create', App\Controllers\Admin\CodeController::class . ':create');
        $this->post('/code', App\Controllers\Admin\CodeController::class . ':add');
        $this->post('/code/ajax', App\Controllers\Admin\CodeController::class . ':ajaxCode');

        // User Mange
        $this->get('/user', App\Controllers\Admin\UserController::class . ':index');
        $this->get('/user/{id}/edit', App\Controllers\Admin\UserController::class . ':edit');
        $this->put('/user/{id}', App\Controllers\Admin\UserController::class . ':update');
        $this->post('/user/changetouser', App\Controllers\Admin\UserController::class . ':changetouser');
        $this->post('/user/create', App\Controllers\Admin\UserController::class . ':createNewUser');
        $this->delete('/user/{id}', App\Controllers\Admin\UserController::class . ':delete');
        $this->post('/user/ajax', App\Controllers\Admin\UserController::class . ':ajax');

        // Coupon Mange
        $this->get('/coupon', App\Controllers\AdminController::class . ':coupon');
        $this->post('/coupon', App\Controllers\AdminController::class . ':addCoupon');
        $this->post('/coupon/ajax', App\Controllers\AdminController::class . ':ajaxCoupon');

        // Subscribe Log Mange
        $this->get('/subscribe', App\Controllers\Admin\SubscribeLogController::class . ':index');
        $this->post('/subscribe/ajax', App\Controllers\Admin\SubscribeLogController::class . ':ajaxSubscribeLog');

        // Traffic Log Mange
        $this->get('/trafficlog', App\Controllers\Admin\TrafficLogController::class . ':index');
        $this->post('/trafficlog/ajax', App\Controllers\Admin\TrafficLogController::class . ':ajaxTrafficLog');

        // Detect Ban Mange
        $this->get('/detect/ban', App\Controllers\Admin\DetectBanLogController::class . ':index');
        $this->post('/detect/ban/ajax', App\Controllers\Admin\DetectBanLogController::class . ':ajaxLog');

        // 设置中心
        $this->get('/setting', App\Controllers\Admin\SettingController::class . ':index');
        $this->post('/setting', App\Controllers\Admin\SettingController::class . ':save');
        $this->post('/setting/email', App\Controllers\Admin\SettingController::class . ':test');
        $this->post('/setting/payment', App\Controllers\Admin\SettingController::class . ':payment');
    })->add(new Admin());

    if ($_ENV['enableAdminApi']) {
        $app->group('/admin/api', function (): void {
            $this->get('/nodes', App\Controllers\Admin\ApiController::class . ':getNodeList');
            $this->get('/node/{id}', App\Controllers\Admin\ApiController::class . ':getNodeInfo');
            $this->get('/ping', App\Controllers\Admin\ApiController::class . ':ping');

            // Re-bind controller, bypass admin token require
            $this->post('/node', App\Controllers\Admin\NodeController::class . ':add');
            $this->put('/node/{id}', App\Controllers\Admin\NodeController::class . ':update');
            $this->delete('/node', App\Controllers\Admin\NodeController::class . ':delete');
        })->add(new AuthorizationBearer($_ENV['adminApiToken']));
    }

    // mu
    $app->group('/mod_mu', function (): void {
        // 流媒体检测
        $this->post('/media/saveReport', App\Controllers\Node\NodeController::class . ':saveReport');
        // 节点
        $this->get('/nodes', App\Controllers\Node\NodeController::class . ':getAllInfo');
        $this->get('/nodes/{id}/info', App\Controllers\Node\NodeController::class . ':getInfo');
        $this->post('/nodes/{id}/info', App\Controllers\Node\NodeController::class . ':info');
        // 用户
        $this->get('/users', App\Controllers\Node\UserController::class . ':index');
        $this->post('/users/traffic', App\Controllers\Node\UserController::class . ':addTraffic');
        $this->post('/users/aliveip', App\Controllers\Node\UserController::class . ':addAliveIp');
        $this->post('/users/detectlog', App\Controllers\Node\UserController::class . ':addDetectLog');
        // 审计 & 杂七杂八的功能
        $this->get('/func/detect_rules', App\Controllers\Node\FuncController::class . ':getDetectLogs');
        $this->get('/func/ping', App\Controllers\Node\FuncController::class . ':ping');
        // Dummy API for old version
        $this->post('/func/block_ip', App\Controllers\Node\FuncController::class . ':addBlockIp');
        $this->get('/func/block_ip', App\Controllers\Node\FuncController::class . ':getBlockip');
        $this->get('/func/unblock_ip', App\Controllers\Node\FuncController::class . ':getUnblockip');
    })->add(new NodeToken());

    $app->group('/link', function (): void {
        $this->get('/{token}', App\Controllers\LinkController::class . ':getContent');
    });

    //通用訂閲
    $app->group('/sub', function (): void {
        $this->get('/{token}/{subtype}', App\Controllers\SubController::class . ':getContent');
    });
};
