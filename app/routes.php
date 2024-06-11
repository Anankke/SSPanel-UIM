<?php

declare(strict_types=1);

use App\Middleware\Admin;
use App\Middleware\Guest;
use App\Middleware\NodeToken;
use App\Middleware\User;
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
    // Bot Callback
    $app->post('/callback/{type}', App\Controllers\CallbackController::class . ':index');
    // OAuth
    $app->post('/oauth/{type}', App\Controllers\OAuthController::class . ':index');
    $app->get('/oauth/{type}', App\Controllers\OAuthController::class . ':index');
    // 通用订阅
    $app->get('/sub/{token}/{subtype}', App\Controllers\SubController::class . ':index');
    // User
    $app->group('/user', static function (RouteCollectorProxy $group): void {
        $group->get('', App\Controllers\UserController::class . ':index');
        $group->get('/', App\Controllers\UserController::class . ':index');
        // 签到
        $group->post('/checkin', App\Controllers\UserController::class . ':checkin');
        // 公告
        $group->get('/announcement', App\Controllers\UserController::class . ':announcement');
        // 文档
        $group->get('/docs', App\Controllers\User\DocsController::class . ':index');
        $group->get('/docs/{id:[0-9]+}/view', App\Controllers\User\DocsController::class . ':detail');
        // 个人资料
        $group->get('/profile', App\Controllers\User\ProfileController::class . ':index');
        // Invite
        $group->get('/invite', App\Controllers\User\InviteController::class . ':index');
        $group->post('/invite/reset', App\Controllers\User\InviteController::class . ':reset');
        // 封禁
        $group->get('/banned', App\Controllers\UserController::class . ':banned');
        // 节点
        $group->get('/server', App\Controllers\User\ServerController::class . ':index');
        // 动态倍率
        $group->get('/rate', App\Controllers\User\RateController::class . ':index');
        $group->post('/rate', App\Controllers\User\RateController::class . ':ajax');
        // 审计
        $group->get('/detect', App\Controllers\User\DetectRuleController::class . ':index');
        $group->get('/detect/log', App\Controllers\User\DetectLogController::class . ':index');
        // 工单
        $group->get('/ticket', App\Controllers\User\TicketController::class . ':index');
        $group->get('/ticket/create', App\Controllers\User\TicketController::class . ':create');
        $group->post('/ticket', App\Controllers\User\TicketController::class . ':add');
        $group->get('/ticket/{id:[0-9]+}/view', App\Controllers\User\TicketController::class . ':detail');
        $group->post('/ticket/{id:[0-9]+}', App\Controllers\User\TicketController::class . ':reply');
        // 资料编辑
        $group->get('/edit', App\Controllers\User\InfoController::class . ':index');
        $group->post('/edit/email', App\Controllers\User\InfoController::class . ':updateEmail');
        $group->post('/edit/username', App\Controllers\User\InfoController::class . ':updateUsername');
        $group->post('/edit/unbind_im', App\Controllers\User\InfoController::class . ':unbindIm');
        $group->post('/edit/password', App\Controllers\User\InfoController::class . ':updatePassword');
        $group->post('/edit/passwd_reset', App\Controllers\User\InfoController::class . ':resetPasswd');
        $group->post('/edit/apitoken_reset', App\Controllers\User\InfoController::class . ':resetApiToken');
        $group->post('/edit/method', App\Controllers\User\InfoController::class . ':updateMethod');
        $group->post('/edit/url_reset', App\Controllers\User\InfoController::class . ':resetUrl');
        $group->post('/edit/daily_mail', App\Controllers\User\InfoController::class . ':updateDailyMail');
        $group->post('/edit/contact_method', App\Controllers\User\InfoController::class . ':updateContactMethod');
        $group->post('/edit/theme', App\Controllers\User\InfoController::class . ':updateTheme');
        $group->post('/edit/theme_mode', App\Controllers\User\InfoController::class . ':updateThemeMode');
        $group->post('/edit/kill', App\Controllers\User\InfoController::class . ':sendToGulag');
        // 发送验证邮件
        $group->post('/edit/send', App\Controllers\AuthController::class . ':sendVerify');
        // MFA
        $group->post('/ga_check', App\Controllers\User\MFAController::class . ':checkGa');
        $group->post('/ga_set', App\Controllers\User\MFAController::class . ':setGa');
        $group->post('/ga_reset', App\Controllers\User\MFAController::class . ':resetGa');
        // 账户余额
        $group->get('/money', App\Controllers\User\MoneyController::class . ':index');
        $group->post('/giftcard', App\Controllers\User\MoneyController::class . ':applyGiftCard');
        // 产品页面
        $group->get('/product', App\Controllers\User\ProductController::class . ':index');
        // 订单页面
        $group->get('/order', App\Controllers\User\OrderController::class . ':index');
        $group->get('/order/create', App\Controllers\User\OrderController::class . ':create');
        $group->post('/order/create', App\Controllers\User\OrderController::class . ':process');
        $group->get('/order/{id:[0-9]+}/view', App\Controllers\User\OrderController::class . ':detail');
        $group->post('/order/ajax', App\Controllers\User\OrderController::class . ':ajax');
        // 账单页面
        $group->get('/invoice', App\Controllers\User\InvoiceController::class . ':index');
        $group->get('/invoice/{id:[0-9]+}/view', App\Controllers\User\InvoiceController::class . ':detail');
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
        // 登出
        $group->get('/logout', App\Controllers\UserController::class . ':logout');
    })->add(new User());

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
        $group->post('/token', App\Controllers\PasswordController::class . ':handleToken');
    })->add(new Guest());
    // Admin
    $app->group('/admin', static function (RouteCollectorProxy $group): void {
        $group->get('', App\Controllers\AdminController::class . ':index');
        $group->get('/', App\Controllers\AdminController::class . ':index');
        // Node
        $group->get('/node', App\Controllers\Admin\NodeController::class . ':index');
        $group->get('/node/create', App\Controllers\Admin\NodeController::class . ':create');
        $group->post('/node', App\Controllers\Admin\NodeController::class . ':add');
        $group->get('/node/{id:[0-9]+}/edit', App\Controllers\Admin\NodeController::class . ':edit');
        $group->post(
            '/node/{id:[0-9]+}/reset_password',
            App\Controllers\Admin\NodeController::class . ':resetPassword'
        );
        $group->post(
            '/node/{id:[0-9]+}/reset_bandwidth',
            App\Controllers\Admin\NodeController::class . ':resetBandwidth'
        );
        $group->post('/node/{id:[0-9]+}/copy', App\Controllers\Admin\NodeController::class . ':copy');
        $group->put('/node/{id:[0-9]+}', App\Controllers\Admin\NodeController::class . ':update');
        $group->delete('/node/{id:[0-9]+}', App\Controllers\Admin\NodeController::class . ':delete');
        $group->post('/node/ajax', App\Controllers\Admin\NodeController::class . ':ajax');
        // Ticket
        $group->get('/ticket', App\Controllers\Admin\TicketController::class . ':index');
        $group->post('/ticket', App\Controllers\Admin\TicketController::class . ':add');
        $group->get('/ticket/{id:[0-9]+}/view', App\Controllers\Admin\TicketController::class . ':detail');
        $group->post('/ticket/{id:[0-9]+}/close', App\Controllers\Admin\TicketController::class . ':close');
        $group->post('/ticket/{id:[0-9]+}', App\Controllers\Admin\TicketController::class . ':reply');
        $group->post('/ticket/{id:[0-9]+}/llm_reply', App\Controllers\Admin\TicketController::class . ':llmReply');
        $group->delete('/ticket/{id:[0-9]+}', App\Controllers\Admin\TicketController::class . ':delete');
        $group->post('/ticket/ajax', App\Controllers\Admin\TicketController::class . ':ajax');
        // Ann
        $group->get('/announcement', App\Controllers\Admin\AnnController::class . ':index');
        $group->get('/announcement/create', App\Controllers\Admin\AnnController::class . ':create');
        $group->post('/announcement', App\Controllers\Admin\AnnController::class . ':add');
        $group->get('/announcement/{id:[0-9]+}/edit', App\Controllers\Admin\AnnController::class . ':edit');
        $group->put('/announcement/{id:[0-9]+}', App\Controllers\Admin\AnnController::class . ':update');
        $group->delete('/announcement/{id:[0-9]+}', App\Controllers\Admin\AnnController::class . ':delete');
        $group->post('/announcement/ajax', App\Controllers\Admin\AnnController::class . ':ajax');
        // Docs
        $group->get('/docs', App\Controllers\Admin\DocsController::class . ':index');
        $group->get('/docs/create', App\Controllers\Admin\DocsController::class . ':create');
        $group->post('/docs', App\Controllers\Admin\DocsController::class . ':add');
        $group->post('/docs/generate', App\Controllers\Admin\DocsController::class . ':generate');
        $group->get('/docs/{id:[0-9]+}/edit', App\Controllers\Admin\DocsController::class . ':edit');
        $group->put('/docs/{id:[0-9]+}', App\Controllers\Admin\DocsController::class . ':update');
        $group->delete('/docs/{id:[0-9]+}', App\Controllers\Admin\DocsController::class . ':delete');
        $group->post('/docs/ajax', App\Controllers\Admin\DocsController::class . ':ajax');
        // 审计规则
        $group->get('/detect', App\Controllers\Admin\DetectRuleController::class . ':index');
        $group->get('/detect/create', App\Controllers\Admin\DetectRuleController::class . ':create');
        $group->post('/detect/add', App\Controllers\Admin\DetectRuleController::class . ':add');
        $group->delete('/detect/{id:[0-9]+}', App\Controllers\Admin\DetectRuleController::class . ':delete');
        $group->post('/detect/ajax', App\Controllers\Admin\DetectRuleController::class . ':ajax');
        // 审计触发日志
        $group->get('/detect/log', App\Controllers\Admin\DetectLogController::class . ':index');
        $group->post('/detect/log/ajax', App\Controllers\Admin\DetectLogController::class . ':ajax');
        // 审计封禁日志
        $group->get('/detect/ban', App\Controllers\Admin\DetectBanLogController::class . ':index');
        $group->post('/detect/ban/ajax', App\Controllers\Admin\DetectBanLogController::class . ':ajax');
        // User
        $group->get('/user', App\Controllers\Admin\UserController::class . ':index');
        $group->get('/user/{id:[0-9]+}/edit', App\Controllers\Admin\UserController::class . ':edit');
        $group->put('/user/{id:[0-9]+}', App\Controllers\Admin\UserController::class . ':update');
        $group->post('/user/create', App\Controllers\Admin\UserController::class . ':create');
        $group->delete('/user/{id}', App\Controllers\Admin\UserController::class . ':delete');
        $group->post('/user/ajax', App\Controllers\Admin\UserController::class . ':ajax');
        // Coupon
        $group->get('/coupon', App\Controllers\Admin\CouponController::class . ':index');
        $group->post('/coupon', App\Controllers\Admin\CouponController::class . ':add');
        $group->post('/coupon/ajax', App\Controllers\Admin\CouponController::class . ':ajax');
        $group->delete('/coupon/{id:[0-9]+}', App\Controllers\Admin\CouponController::class . ':delete');
        $group->post('/coupon/{id:[0-9]+}/disable', App\Controllers\Admin\CouponController::class . ':disable');
        // 登录日志
        $group->get('/login', App\Controllers\Admin\LoginLogController::class . ':index');
        $group->post('/login/ajax', App\Controllers\Admin\LoginLogController::class . ':ajax');
        // 在线IP日志
        $group->get('/online', App\Controllers\Admin\OnlineLogController::class . ':index');
        $group->post('/online/ajax', App\Controllers\Admin\OnlineLogController::class . ':ajax');
        // 订阅日志
        $group->get('/subscribe', App\Controllers\Admin\SubLogController::class . ':index');
        $group->post('/subscribe/ajax', App\Controllers\Admin\SubLogController::class . ':ajax');
        // 返利日志
        $group->get('/payback', App\Controllers\Admin\PaybackController::class . ':index');
        $group->post('/payback/ajax', App\Controllers\Admin\PaybackController::class . ':ajax');
        // 用户余额日志
        $group->get('/money', App\Controllers\Admin\MoneyLogController::class . ':index');
        $group->post('/money/ajax', App\Controllers\Admin\MoneyLogController::class . ':ajax');
        // 支付网关日志
        $group->get('/gateway', App\Controllers\Admin\PaylistController::class . ':index');
        $group->post('/gateway/ajax', App\Controllers\Admin\PaylistController::class . ':ajax');
        // 系统日志
        $group->get('/syslog', App\Controllers\Admin\SysLogController::class . ':index');
        $group->get('/syslog/{id:[0-9]+}/view', App\Controllers\Admin\SysLogController::class . ':detail');
        $group->post('/syslog/ajax', App\Controllers\Admin\SysLogController::class . ':ajax');
        // 系统状态
        $group->get('/system', App\Controllers\Admin\SystemController::class . ':index');
        $group->post('/system/check_update', App\Controllers\Admin\SystemController::class . ':checkUpdate');
        // 设置中心
        $group->get('/setting/billing', App\Controllers\Admin\Setting\BillingController::class . ':index');
        $group->post('/setting/billing', App\Controllers\Admin\Setting\BillingController::class . ':save');
        $group->post(
            '/setting/billing/set_stripe_webhook',
            App\Controllers\Admin\Setting\BillingController::class . ':setStripeWebhook'
        );
        $group->get('/setting/captcha', App\Controllers\Admin\Setting\CaptchaController::class . ':index');
        $group->post('/setting/captcha', App\Controllers\Admin\Setting\CaptchaController::class . ':save');
        $group->get('/setting/cron', App\Controllers\Admin\Setting\CronController::class . ':index');
        $group->post('/setting/cron', App\Controllers\Admin\Setting\CronController::class . ':save');
        $group->get('/setting/email', App\Controllers\Admin\Setting\EmailController::class . ':index');
        $group->post('/setting/email', App\Controllers\Admin\Setting\EmailController::class . ':save');
        $group->get('/setting/feature', App\Controllers\Admin\Setting\FeatureController::class . ':index');
        $group->post('/setting/feature', App\Controllers\Admin\Setting\FeatureController::class . ':save');
        $group->get('/setting/im', App\Controllers\Admin\Setting\ImController::class . ':index');
        $group->post('/setting/im', App\Controllers\Admin\Setting\ImController::class . ':save');
        $group->post(
            '/setting/im/reset_webhook_token/{type}',
            App\Controllers\Admin\Setting\ImController::class . ':resetWebhookToken'
        );
        $group->post(
            '/setting/im/set_webhook/{type}',
            App\Controllers\Admin\Setting\ImController::class . ':setWebhook'
        );
        $group->get('/setting/llm', App\Controllers\Admin\Setting\LlmController::class . ':index');
        $group->post('/setting/llm', App\Controllers\Admin\Setting\LlmController::class . ':save');
        $group->get('/setting/ref', App\Controllers\Admin\Setting\RefController::class . ':index');
        $group->post('/setting/ref', App\Controllers\Admin\Setting\RefController::class . ':save');
        $group->get('/setting/reg', App\Controllers\Admin\Setting\RegController::class . ':index');
        $group->post('/setting/reg', App\Controllers\Admin\Setting\RegController::class . ':save');
        $group->get('/setting/sub', App\Controllers\Admin\Setting\SubController::class . ':index');
        $group->post('/setting/sub', App\Controllers\Admin\Setting\SubController::class . ':save');
        $group->get('/setting/support', App\Controllers\Admin\Setting\SupportController::class . ':index');
        $group->post('/setting/support', App\Controllers\Admin\Setting\SupportController::class . ':save');
        // 设置测试
        $group->post(
            '/setting/test/email',
            App\Controllers\Admin\Setting\EmailController::class . ':testEmail'
        );
        $group->post(
            '/setting/test/telegram',
            App\Controllers\Admin\Setting\ImController::class . ':testTelegram'
        );
        $group->post(
            '/setting/test/discord',
            App\Controllers\Admin\Setting\ImController::class . ':testDiscord'
        );
        $group->post(
            '/setting/test/slack',
            App\Controllers\Admin\Setting\ImController::class . ':testSlack'
        );
        // 礼品卡
        $group->get('/giftcard', App\Controllers\Admin\GiftCardController::class . ':index');
        $group->post('/giftcard', App\Controllers\Admin\GiftCardController::class . ':add');
        $group->post('/giftcard/ajax', App\Controllers\Admin\GiftCardController::class . ':ajax');
        $group->delete('/giftcard/{id:[0-9]+}', App\Controllers\Admin\GiftCardController::class . ':delete');
        // 商品
        $group->get('/product', App\Controllers\Admin\ProductController::class . ':index');
        $group->get('/product/create', App\Controllers\Admin\ProductController::class . ':create');
        $group->post('/product', App\Controllers\Admin\ProductController::class . ':add');
        $group->get('/product/{id:[0-9]+}/edit', App\Controllers\Admin\ProductController::class . ':edit');
        $group->post('/product/{id:[0-9]+}/copy', App\Controllers\Admin\ProductController::class . ':copy');
        $group->put('/product/{id:[0-9]+}', App\Controllers\Admin\ProductController::class . ':update');
        $group->delete('/product/{id:[0-9]+}', App\Controllers\Admin\ProductController::class . ':delete');
        $group->post('/product/ajax', App\Controllers\Admin\ProductController::class . ':ajax');
        // 订单
        $group->get('/order', App\Controllers\Admin\OrderController::class . ':index');
        $group->get('/order/{id:[0-9]+}/view', App\Controllers\Admin\OrderController::class . ':detail');
        $group->post('/order/{id:[0-9]+}/cancel', App\Controllers\Admin\OrderController::class . ':cancel');
        $group->delete('/order/{id:[0-9]+}', App\Controllers\Admin\OrderController::class . ':delete');
        $group->post('/order/ajax', App\Controllers\Admin\OrderController::class . ':ajax');
        // 账单
        $group->get('/invoice', App\Controllers\Admin\InvoiceController::class . ':index');
        $group->get('/invoice/{id:[0-9]+}/view', App\Controllers\Admin\InvoiceController::class . ':detail');
        $group->post('/invoice/{id:[0-9]+}/mark_paid', App\Controllers\Admin\InvoiceController::class . ':markPaid');
        $group->post('/invoice/ajax', App\Controllers\Admin\InvoiceController::class . ':ajax');
    })->add(new Admin());
    // WebAPI
    $app->group('/mod_mu', static function (RouteCollectorProxy $group): void {
        // 节点
        $group->get('/nodes/{id:[0-9]+}/info', App\Controllers\WebAPI\NodeController::class . ':getInfo');
        // 用户
        $group->get('/users', App\Controllers\WebAPI\UserController::class . ':index');
        $group->post('/users/traffic', App\Controllers\WebAPI\UserController::class . ':addTraffic');
        $group->post('/users/aliveip', App\Controllers\WebAPI\UserController::class . ':addAliveIp');
        $group->post('/users/detectlog', App\Controllers\WebAPI\UserController::class . ':addDetectLog');
        // 审计 & 杂七杂八的功能
        $group->get('/func/detect_rules', App\Controllers\WebAPI\FuncController::class . ':getDetectRules');
        $group->get('/func/ping', App\Controllers\WebAPI\FuncController::class . ':ping');
    })->add(new NodeToken());

    // Admin REST API
    //$app->group('/admin/api/v1', function (RouteCollectorProxy $group): void {
    //    $group->post('/{action}', App\Controllers\Api\AdminApiV1Controller::class . ':actionHandler');
    //})->add(new AdminApi());

    // User REST API
    //$app->group('/user/api/v1', function (RouteCollectorProxy $group): void {
    //    $group->post('/{action}', App\Controllers\Api\UserApiV1Controller::class . ':actionHandler');
    //})->add(new UserApi());

    // WebAPI V2(Aka Node API V1)
    //$app->group('/node/api/v1', function (RouteCollectorProxy $group): void {
    //    $group->put('/heartbeat', App\Controllers\Api\NodeApiV1Controller::class . ':getHeartbeat');
    //    $group->get('/info', App\Controllers\Api\NodeApiV1Controller::class . ':getInfo');
    //    $group->get('/user', App\Controllers\Api\NodeApiV1Controller::class . ':getUser');
    //    $group->get('/detect_rule', App\Controllers\Api\NodeApiV1Controller::class . ':getDetectRule');
    //    $group->post('/user/traffic', App\Controllers\Api\NodeApiV1Controller::class . ':addUserTraffic');
    //    $group->post('/user/online_ip', App\Controllers\Api\NodeApiV1Controller::class . ':addUserOnlineIp');
    //    $group->post('/user/detect_log', App\Controllers\Api\NodeApiV1Controller::class . ':addUserDetectLog');
    //})->add(new NodeApi());
};
