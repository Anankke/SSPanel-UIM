<?php

declare(strict_types=1);

use App\Middleware\Admin;
use App\Middleware\Auth;
use App\Middleware\Guest;
use App\Middleware\NodeToken;
use Slim\Routing\RouteCollectorProxy;

return static function (Slim\App $app): void {
    // Home
    $app->get('/', App\Controllers\HomeController::class . ':index');
    $app->get('/tos', App\Controllers\HomeController::class . ':tos');
    $app->get('/staff', App\Controllers\HomeController::class . ':staff');
    // Error Page
    $app->get('/404', App\Controllers\HomeController::class . ':notFound');
    $app->get('/405', App\Controllers\HomeController::class . ':methodNotAllowed');
    $app->get('/500', App\Controllers\HomeController::class . ':internalServerError');
    // Telegram
    $app->post('/telegram_callback', App\Controllers\HomeController::class . ':telegram');
    // User Center
    $app->group('/user', static function (RouteCollectorProxy $group): void {
        $group->get('', App\Controllers\UserController::class . ':index');
        $group->get('/', App\Controllers\UserController::class . ':index');
        // 签到
        $group->post('/checkin', App\Controllers\UserController::class . ':doCheckin');
        // 公告
        $group->get('/announcement', App\Controllers\UserController::class . ':announcement');
        // 文档
        $group->get('/docs', App\Controllers\UserController::class . ':docs');
        // 流媒体解锁
        $group->get('/media', App\Controllers\UserController::class . ':media');
        // 个人资料
        $group->get('/profile', App\Controllers\UserController::class . ':profile');
        $group->get('/invite', App\Controllers\UserController::class . ':invite');
        // 封禁
        $group->get('/banned', App\Controllers\UserController::class . ':banned');
        // 节点
        $group->get('/server', App\Controllers\User\ServerController::class . ':server');
        // 审计
        $group->get('/detect', App\Controllers\User\DetectController::class . ':index');
        // 工单
        $group->get('/ticket', App\Controllers\User\TicketController::class . ':ticket');
        $group->get('/ticket/create', App\Controllers\User\TicketController::class . ':ticketCreate');
        $group->post('/ticket', App\Controllers\User\TicketController::class . ':ticketAdd');
        $group->get('/ticket/{id}/view', App\Controllers\User\TicketController::class . ':ticketView');
        $group->put('/ticket/{id}', App\Controllers\User\TicketController::class . ':ticketUpdate');
        // 资料编辑
        $group->get('/edit', App\Controllers\UserController::class . ':edit');
        $group->post('/email', App\Controllers\UserController::class . ':updateEmail');
        $group->post('/username', App\Controllers\UserController::class . ':updateUsername');
        $group->post('/password', App\Controllers\UserController::class . ':updatePassword');
        $group->post('/send', App\Controllers\AuthController::class . ':sendVerify');
        $group->post('/contact_update', App\Controllers\UserController::class . ':updateContact');
        $group->post('/theme', App\Controllers\UserController::class . ':updateTheme');
        $group->post('/mail', App\Controllers\UserController::class . ':updateMail');
        $group->post('/passwd_reset', App\Controllers\UserController::class . ':resetPasswd');
        $group->post('/apitoken_reset', App\Controllers\UserController::class . ':resetApiToken');
        $group->post('/method', App\Controllers\UserController::class . ':updateMethod');
        $group->get('/kill', App\Controllers\UserController::class . ':kill');
        $group->post('/kill', App\Controllers\UserController::class . ':handleKill');
        $group->get('/logout', App\Controllers\UserController::class . ':logout');
        // MFA
        $group->post('/ga_check', App\Controllers\User\MFAController::class . ':checkGa');
        $group->post('/ga_set', App\Controllers\User\MFAController::class . ':setGa');
        $group->post('/ga_reset', App\Controllers\User\MFAController::class . ':resetGa');
        // Telegram
        $group->post('/telegram_reset', App\Controllers\UserController::class . ':resetTelegram');
        // URL
        $group->post('/url_reset', App\Controllers\UserController::class . ':resetURL');
        $group->put('/invite', App\Controllers\UserController::class . ':resetInviteURL');
        // 深色模式切换
        $group->post('/switch_theme_mode', App\Controllers\UserController::class . ':switchThemeMode');
        // 记录
        $group->get('/subscribe/log', App\Controllers\User\LogController::class . ':subscribe');
        $group->get('/detect/log', App\Controllers\User\LogController::class . ':detect');
        // 账户余额
        $group->get('/money', App\Controllers\User\MoneyController::class . ':money');
        // 礼品卡兑换
        $group->post('/giftcard', App\Controllers\User\MoneyController::class . ':applyGiftCard');
        // 产品页面
        $group->get('/product', App\Controllers\User\ProductController::class . ':product');
        // 订单页面
        $group->get('/order', App\Controllers\User\OrderController::class . ':order');
        $group->get('/order/create', App\Controllers\User\OrderController::class . ':create');
        $group->post('/order/create', App\Controllers\User\OrderController::class . ':process');
        $group->get('/order/{id}/view', App\Controllers\User\OrderController::class . ':detail');
        $group->post('/order/ajax', App\Controllers\User\OrderController::class . ':ajax');
        // 账单页面
        $group->get('/invoice', App\Controllers\User\InvoiceController::class . ':invoice');
        $group->get('/invoice/{id}/view', App\Controllers\User\InvoiceController::class . ':detail');
        $group->post('/invoice/pay_balance', App\Controllers\User\InvoiceController::class . ':payBalance');
        $group->post('/invoice/ajax', App\Controllers\User\InvoiceController::class . ':ajax');
        // 新优惠码系统
        $group->post('/coupon', App\Controllers\User\CouponController::class . ':check');
        // 支付
        $group->post('/payment/purchase/{type}', App\Services\Payment::class . ':purchase');
        $group->get('/payment/purchase/{type}', App\Services\Payment::class . ':purchase');
        $group->get('/payment/return/{type}', App\Services\Payment::class . ':returnHTML');
        // Get Clients
        $group->get('/clients/{name}', App\Controllers\User\ClientController::class . ':getClients');
    })->add(new Auth());

    $app->group('/payment', static function (RouteCollectorProxy $group): void {
        $group->get('/notify/{type}', App\Services\Payment::class . ':notify');
        $group->post('/notify/{type}', App\Services\Payment::class . ':notify');
        $group->post('/status/{type}', App\Services\Payment::class . ':getStatus');
    });

    // Auth
    $app->group('/auth', static function (RouteCollectorProxy $group): void {
        $group->get('/login', App\Controllers\AuthController::class . ':login');
        $group->post('/login', App\Controllers\AuthController::class . ':loginHandle');
        $group->get('/register', App\Controllers\AuthController::class . ':register');
        $group->post('/register', App\Controllers\AuthController::class . ':registerHandle');
        $group->post('/send', App\Controllers\AuthController::class . ':sendVerify');
        $group->get('/logout', App\Controllers\AuthController::class . ':logout');
    })->add(new Guest());

    // Password
    $app->group('/password', static function (RouteCollectorProxy $group): void {
        $group->get('/reset', App\Controllers\PasswordController::class . ':reset');
        $group->post('/reset', App\Controllers\PasswordController::class . ':handleReset');
        $group->get('/token/{token}', App\Controllers\PasswordController::class . ':token');
        $group->post('/token/{token}', App\Controllers\PasswordController::class . ':handleToken');
    })->add(new Guest());

    // Admin
    $app->group('/admin', static function (RouteCollectorProxy $group): void {
        $group->get('', App\Controllers\AdminController::class . ':index');
        $group->get('/', App\Controllers\AdminController::class . ':index');
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
        $group->put('/ticket/{id}/ai', App\Controllers\Admin\TicketController::class . ':updateAI');
        $group->delete('/ticket/{id}', App\Controllers\Admin\TicketController::class . ':delete');
        $group->post('/ticket/ajax', App\Controllers\Admin\TicketController::class . ':ajax');
        // Ann Mange
        $group->get('/announcement', App\Controllers\Admin\AnnController::class . ':index');
        $group->get('/announcement/create', App\Controllers\Admin\AnnController::class . ':create');
        $group->post('/announcement', App\Controllers\Admin\AnnController::class . ':add');
        $group->get('/announcement/{id}/edit', App\Controllers\Admin\AnnController::class . ':edit');
        $group->put('/announcement/{id}', App\Controllers\Admin\AnnController::class . ':update');
        $group->delete('/announcement/{id}', App\Controllers\Admin\AnnController::class . ':delete');
        $group->post('/announcement/ajax', App\Controllers\Admin\AnnController::class . ':ajax');
        // 审计
        $group->get('/detect', App\Controllers\Admin\DetectController::class . ':detect');
        $group->get('/detect/create', App\Controllers\Admin\DetectController::class . ':create');
        $group->post('/detect/add', App\Controllers\Admin\DetectController::class . ':add');
        $group->delete('/detect/{id}', App\Controllers\Admin\DetectController::class . ':delete');
        $group->post('/detect/ajax', App\Controllers\Admin\DetectController::class . ':ajaxRule');
        $group->get('/detect/log', App\Controllers\Admin\DetectController::class . ':log');
        $group->post('/detect/log/ajax', App\Controllers\Admin\DetectController::class . ':ajaxLog');
        $group->get('/detect/ban', App\Controllers\Admin\DetectController::class . ':ban');
        $group->post('/detect/ban/ajax', App\Controllers\Admin\DetectController::class . ':ajaxBan');
        // User Mange
        $group->get('/user', App\Controllers\Admin\UserController::class . ':index');
        $group->get('/user/{id}/edit', App\Controllers\Admin\UserController::class . ':edit');
        $group->put('/user/{id}', App\Controllers\Admin\UserController::class . ':update');
        $group->post('/user/create', App\Controllers\Admin\UserController::class . ':createNewUser');
        $group->delete('/user/{id}', App\Controllers\Admin\UserController::class . ':delete');
        $group->post('/user/ajax', App\Controllers\Admin\UserController::class . ':ajax');
        // Coupon Mange
        $group->get('/coupon', App\Controllers\Admin\CouponController::class . ':index');
        $group->post('/coupon', App\Controllers\Admin\CouponController::class . ':add');
        $group->post('/coupon/ajax', App\Controllers\Admin\CouponController::class . ':ajax');
        $group->delete('/coupon/{id}', App\Controllers\Admin\CouponController::class . ':delete');
        $group->post('/coupon/{id}/disable', App\Controllers\Admin\CouponController::class . ':disable');
        // 登录日志
        $group->get('/login', App\Controllers\Admin\IpController::class . ':login');
        $group->post('/login/ajax', App\Controllers\Admin\IpController::class . ':ajaxLogin');
        // 在线IP
        $group->get('/online', App\Controllers\Admin\IpController::class . ':online');
        $group->post('/online/ajax', App\Controllers\Admin\IpController::class . ':ajaxOnline');
        // 订阅日志
        $group->get('/subscribe', App\Controllers\Admin\SubscribeLogController::class . ':index');
        $group->post('/subscribe/ajax', App\Controllers\Admin\SubscribeLogController::class . ':ajax');
        // 邀请日志
        $group->get('/invite', App\Controllers\Admin\InviteController::class . ':invite');
        $group->post('/invite/update_invite', App\Controllers\Admin\InviteController::class . ':update');
        $group->post('/invite/add_invite', App\Controllers\Admin\InviteController::class . ':add');
        $group->post('/invite/ajax', App\Controllers\Admin\InviteController::class . ':ajax');
        // 流量日志
        $group->get('/trafficlog', App\Controllers\Admin\TrafficLogController::class . ':index');
        $group->post('/trafficlog/ajax', App\Controllers\Admin\TrafficLogController::class . ':ajax');
        // 用户余额日志
        $group->get('/moneylog', App\Controllers\Admin\MoneyLogController::class . ':log');
        $group->post('/moneylog/ajax', App\Controllers\Admin\MoneyLogController::class . ':ajax');
        // 支付网关日志
        $group->get('/gateway', App\Controllers\Admin\PaylistController::class . ':gateway');
        $group->post('/gateway/ajax', App\Controllers\Admin\PaylistController::class . ':ajax');
        // 设置中心
        $group->get('/setting/billing', App\Controllers\Admin\Setting\BillingController::class . ':billing');
        $group->post('/setting/billing', App\Controllers\Admin\Setting\BillingController::class . ':saveBilling');
        $group->get('/setting/captcha', App\Controllers\Admin\Setting\CaptchaController::class . ':captcha');
        $group->post('/setting/captcha', App\Controllers\Admin\Setting\CaptchaController::class . ':saveCaptcha');
        $group->get('/setting/cron', App\Controllers\Admin\Setting\CronController::class . ':cron');
        $group->post('/setting/cron', App\Controllers\Admin\Setting\CronController::class . ':saveCron');
        $group->get('/setting/email', App\Controllers\Admin\Setting\EmailController::class . ':email');
        $group->post('/setting/email', App\Controllers\Admin\Setting\EmailController::class . ':saveEmail');
        $group->get('/setting/feature', App\Controllers\Admin\Setting\FeatureController::class . ':feature');
        $group->post('/setting/feature', App\Controllers\Admin\Setting\FeatureController::class . ':saveFeature');
        $group->get('/setting/im', App\Controllers\Admin\Setting\ImController::class . ':im');
        $group->post('/setting/im', App\Controllers\Admin\Setting\ImController::class . ':saveIm');
        $group->get('/setting/ref', App\Controllers\Admin\Setting\RefController::class . ':ref');
        $group->post('/setting/ref', App\Controllers\Admin\Setting\RefController::class . ':saveRef');
        $group->get('/setting/reg', App\Controllers\Admin\Setting\RegController::class . ':reg');
        $group->post('/setting/reg', App\Controllers\Admin\Setting\RegController::class . ':saveReg');
        $group->get('/setting/sub', App\Controllers\Admin\Setting\SubscribeController::class . ':sub');
        $group->post('/setting/sub', App\Controllers\Admin\Setting\SubscribeController::class . ':saveSub');
        $group->get('/setting/support', App\Controllers\Admin\Setting\SupportController::class . ':support');
        $group->post('/setting/support', App\Controllers\Admin\Setting\SupportController::class . ':saveSupport');
        $group->post('/setting/test_email', App\Controllers\Admin\Setting\EmailController::class . ':testEmail');
        // 礼品卡
        $group->get('/giftcard', App\Controllers\Admin\GiftCardController::class . ':index');
        $group->post('/giftcard', App\Controllers\Admin\GiftCardController::class . ':add');
        $group->post('/giftcard/ajax', App\Controllers\Admin\GiftCardController::class . ':ajax');
        $group->delete('/giftcard/{id}', App\Controllers\Admin\GiftCardController::class . ':delete');
        // 商品
        $group->get('/product', App\Controllers\Admin\ProductController::class . ':index');
        $group->get('/product/create', App\Controllers\Admin\ProductController::class . ':create');
        $group->post('/product', App\Controllers\Admin\ProductController::class . ':add');
        $group->get('/product/{id}/edit', App\Controllers\Admin\ProductController::class . ':edit');
        $group->post('/product/{id}/copy', App\Controllers\Admin\ProductController::class . ':copy');
        $group->put('/product/{id}', App\Controllers\Admin\ProductController::class . ':update');
        $group->delete('/product/{id}', App\Controllers\Admin\ProductController::class . ':delete');
        $group->post('/product/ajax', App\Controllers\Admin\ProductController::class . ':ajax');
        // 订单
        $group->get('/order', App\Controllers\Admin\OrderController::class . ':index');
        $group->get('/order/{id}/view', App\Controllers\Admin\OrderController::class . ':detail');
        $group->post('/order/{id}/cancel', App\Controllers\Admin\OrderController::class . ':cancel');
        $group->delete('/order/{id}', App\Controllers\Admin\OrderController::class . ':delete');
        $group->post('/order/ajax', App\Controllers\Admin\OrderController::class . ':ajax');
        // 账单
        $group->get('/invoice', App\Controllers\Admin\InvoiceController::class . ':index');
        $group->get('/invoice/{id}/view', App\Controllers\Admin\InvoiceController::class . ':detail');
        $group->post('/invoice/{id}/mark_paid', App\Controllers\Admin\InvoiceController::class . ':markPaid');
        $group->post('/invoice/ajax', App\Controllers\Admin\InvoiceController::class . ':ajax');
    })->add(new Admin());

    //$app->group('/admin/api', function (RouteCollectorProxy $group): void {
    //    $group->post('/{action}', App\Controllers\Api\AdminApiController::class . ':actionHandler');
    //})->add(new AdminApiToken());

    //$app->group('/user/api', function (RouteCollectorProxy $group): void {
    //    $group->post('/{action}', App\Controllers\Api\UserApiController::class . ':actionHandler');
    //})->add(new UserApiToken());

    // WebAPI
    $app->group('/mod_mu', static function (RouteCollectorProxy $group): void {
        // 流媒体检测
        $group->post('/media/save_report', App\Controllers\WebAPI\NodeController::class . ':saveReport');
        // 节点
        $group->get('/nodes/{id}/info', App\Controllers\WebAPI\NodeController::class . ':getInfo');
        // 用户
        $group->get('/users', App\Controllers\WebAPI\UserController::class . ':index');
        $group->post('/users/traffic', App\Controllers\WebAPI\UserController::class . ':addTraffic');
        $group->post('/users/aliveip', App\Controllers\WebAPI\UserController::class . ':addAliveIp');
        $group->post('/users/detectlog', App\Controllers\WebAPI\UserController::class . ':addDetectLog');
        // 审计 & 杂七杂八的功能
        $group->get('/func/detect_rules', App\Controllers\WebAPI\FuncController::class . ':getDetectLogs');
        $group->get('/func/ping', App\Controllers\WebAPI\FuncController::class . ':ping');
    })->add(new NodeToken());

    // 传统订阅（SS/V2Ray/Trojan etc.）
    $app->get('/link/{token}', App\Controllers\LinkController::class . ':getContent');

    // 通用订阅（Json/Clash/SIP008）
    $app->get('/sub/{token}/{subtype}', App\Controllers\SubController::class . ':getContent');
};
