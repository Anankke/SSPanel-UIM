<?php
declare(strict_types=1);

use Slim\App as SlimApp;
use App\Middleware\{Auth, Guest, Admin, Mod_Mu, AuthorizationBearer};

return function (SlimApp $app) {
    // Home
    $app->get('/',          App\Controllers\HomeController::class . ':index');
    $app->get('/404',       App\Controllers\HomeController::class . ':page404');
    $app->get('/405',       App\Controllers\HomeController::class . ':page405');
    $app->get('/500',       App\Controllers\HomeController::class . ':page500');
    $app->get('/tos',       App\Controllers\HomeController::class . ':tos');
    $app->get('/staff',     App\Controllers\HomeController::class . ':staff');

    // Other
    $app->post('/notify',               App\Controllers\HomeController::class . ':notify');

    // Telegram
    $app->post('/telegram_callback',    App\Controllers\HomeController::class . ':telegram');

    // User Center
    $app->group('/user', function () {
        // 用户中心首页
        $this->get('',                          App\Controllers\UserController::class . ':index');
        $this->get('/',                         App\Controllers\UserController::class . ':index');
        $this->post('/checkin',                 App\Controllers\UserController::class . ':doCheckin');

        // 单页面
        $this->get('/media',                    App\Controllers\UserController::class . ':media');
        $this->get('/profile',                  App\Controllers\UserController::class . ':profile');
        $this->post('/kill',                    App\Controllers\UserController::class . ':handleKill');
        $this->get('/disable',                  App\Controllers\UserController::class . ':disable');
        $this->get('/announcement',             App\Controllers\UserController::class . ':announcement');
        $this->get('/subscribe_log',            App\Controllers\UserController::class . ':subscribe_log');

        // 文档中心
        $this->get('/docs/{client}',            App\Controllers\DocsController::class . ':index');

        // 邀请系统
        $this->get('/invite',                   App\Controllers\UserController::class . ':invite');
        $this->put('/invite',                   App\Controllers\UserController::class . ':resetInviteURL');

        // 审计系统
        $this->get('/detect',                   App\Controllers\UserController::class . ':detect_index');
        $this->get('/detect/log',               App\Controllers\UserController::class . ':detect_log');

        // 工单系统
        $this->get('/ticket',                   App\Controllers\User\TicketController::class . ':ticket');
        $this->get('/ticket/create',            App\Controllers\User\TicketController::class . ':ticket_create');
        $this->post('/ticket',                  App\Controllers\User\TicketController::class . ':ticket_add');
        $this->get('/ticket/{id}/view',         App\Controllers\User\TicketController::class . ':ticket_view');
        $this->put('/ticket/{id}',              App\Controllers\User\TicketController::class . ':ticket_update');

        // 新商店系统
        $this->get('/product',                  App\Controllers\UserController::class . ':productIndex');
        $this->get('/order',                    App\Controllers\UserController::class . ':orderIndex');
        $this->get('/order/{no}',               App\Controllers\UserController::class . ':orderDetails');
        $this->get('/order/status/{no}',        App\Controllers\UserController::class . ':orderStatus');
        $this->post('/order',                   App\Controllers\UserController::class . ':createOrder');
        $this->put('/order',                    App\Controllers\UserController::class . ':processOrder');
        $this->post('/redeem',                  App\Controllers\UserController::class . ':redeemGiftCard');
        $this->post('/coupon_check',            App\Controllers\UserController::class . ':couponCheck');

        // 编辑页面
        $this->get('/edit',                     App\Controllers\UserController::class . ':edit');
        $this->get('/telegram_reset',           App\Controllers\UserController::class . ':telegram_reset');
        $this->post('/email',                   App\Controllers\UserController::class . ':updateEmail');
        $this->post('/username',                App\Controllers\UserController::class . ':updateUsername');
        $this->post('/password',                App\Controllers\UserController::class . ':updatePassword');
        $this->post('/send',                    App\Controllers\AuthController::class . ':sendVerify');
        $this->post('/wechat',                  App\Controllers\UserController::class . ':updateWechat');
        $this->post('/ssr',                     App\Controllers\UserController::class . ':updateSSR');
        $this->post('/theme',                   App\Controllers\UserController::class . ':updateTheme');
        $this->post('/mail',                    App\Controllers\UserController::class . ':updateMail');
        $this->post('/sspwd',                   App\Controllers\UserController::class . ':updateSsPwd');
        $this->post('/url_reset',               App\Controllers\UserController::class . ':resetURL');
        $this->post('/gacheck',                 App\Controllers\UserController::class . ':gaCheck');
        $this->post('/gaset',                   App\Controllers\UserController::class . ':gaSet');
        $this->post('/gareset',                 App\Controllers\UserController::class . ':gaReset');
        $this->post('/port',                    App\Controllers\UserController::class . ':resetPort');

        // 节点列表
        $this->get('/node',                     App\Controllers\User\NodeController::class . ':user_node_page');
        $this->get('/server',                   App\Controllers\User\NodeController::class . ':serverList');
        $this->get('/node/{id}/ajax',           App\Controllers\User\NodeController::class . ':user_node_ajax');
        $this->get('/node/{id}',                App\Controllers\User\NodeController::class . ':user_node_info');

        // 其他
        $this->get('/logout',                   App\Controllers\UserController::class . ':logout');
        $this->get('/backtoadmin',              App\Controllers\UserController::class . ':backtoadmin');
        $this->get('/getPcClient',              App\Controllers\UserController::class . ':getPcClient');
        $this->get('/getUserAllURL',            App\Controllers\UserController::class . ':getUserAllURL');
    })->add(new Auth());

    $app->group('/payments', function () {
        $this->get('/notify/{type}',            App\Services\Payment::class . ':notify');
        $this->post('/notify/{type}',           App\Services\Payment::class . ':notify');
    });

    // Auth
    $app->group('/auth', function () {
        $this->get('/login',            App\Controllers\AuthController::class . ':login');
        $this->post('/qrcode_check',    App\Controllers\AuthController::class . ':qrcode_check');
        $this->post('/login',           App\Controllers\AuthController::class . ':loginHandle');
        $this->post('/qrcode_login',    App\Controllers\AuthController::class . ':qrcode_loginHandle');
        $this->get('/register',         App\Controllers\AuthController::class . ':register');
        $this->post('/register',        App\Controllers\AuthController::class . ':registerHandle');
        $this->post('/send',            App\Controllers\AuthController::class . ':sendVerify');
        $this->get('/logout',           App\Controllers\AuthController::class . ':logout');
        $this->get('/telegram_oauth',   App\Controllers\AuthController::class . ':telegram_oauth');
        $this->get('/login_getCaptcha', App\Controllers\AuthController::class . ':getCaptcha');
    })->add(new Guest());

    // Password
    $app->group('/password', function () {
        $this->get('/reset',            App\Controllers\PasswordController::class . ':reset');
        $this->post('/reset',           App\Controllers\PasswordController::class . ':handleReset');
        $this->get('/token/{token}',    App\Controllers\PasswordController::class . ':token');
        $this->post('/token/{token}',   App\Controllers\PasswordController::class . ':handleToken');
    })->add(new Guest());

    // Admin
    $app->group('/admin', function () {
        $this->get('',                          App\Controllers\AdminController::class . ':index');
        $this->get('/',                         App\Controllers\AdminController::class . ':index');

        $this->get('/invite',                   App\Controllers\AdminController::class . ':invite');
        $this->post('/invite',                  App\Controllers\AdminController::class . ':addInvite');
        $this->post('/chginvite',               App\Controllers\AdminController::class . ':chgInvite');
        $this->post('/payback/ajax',            App\Controllers\AdminController::class . ':ajax_payback');

        // Node Mange
        $this->get('/node',                     App\Controllers\Admin\NodeController::class . ':index');
        $this->get('/node/create',              App\Controllers\Admin\NodeController::class . ':create');
        $this->post('/node',                    App\Controllers\Admin\NodeController::class . ':add');
        $this->get('/node/{id}/edit',           App\Controllers\Admin\NodeController::class . ':edit');
        $this->put('/node/{id}',                App\Controllers\Admin\NodeController::class . ':update');
        $this->delete('/node',                  App\Controllers\Admin\NodeController::class . ':delete');
        $this->post('/node/ajax',               App\Controllers\Admin\NodeController::class . ':ajax');

        // Ticket Mange
        $this->get('/ticket',                   App\Controllers\Admin\TicketController::class . ':index');
        $this->get('/ticket/{id}/view',         App\Controllers\Admin\TicketController::class . ':read');
        $this->put('/ticket/{id}',              App\Controllers\Admin\TicketController::class . ':addReply');
        $this->put('/ticket/{id}/close',        App\Controllers\Admin\TicketController::class . ':closeTk');
        $this->post('/ticket/ajax',             App\Controllers\Admin\TicketController::class . ':ajaxQuery');
        $this->delete('/ticket/{id}',           App\Controllers\Admin\TicketController::class . ':delete');

        // Product
        $this->get('/product',                  App\Controllers\Admin\ProductController::class . ':index');
        $this->get('/product/details/{id}',     App\Controllers\Admin\ProductController::class . ':get');
        $this->post('/product',                 App\Controllers\Admin\ProductController::class . ':save');
        $this->put('/product/{id}',             App\Controllers\Admin\ProductController::class . ':update');
        $this->delete('/product/{id}',          App\Controllers\Admin\ProductController::class . ':delete');

        // Order
        $this->get('/order',                    App\Controllers\Admin\OrderController::class . ':index');
        $this->post('/order/ajax',              App\Controllers\Admin\OrderController::class . ':ajaxQuery');

        // Gift Card
        $this->get('/giftcard',                 App\Controllers\Admin\GiftCardController::class . ':index');
        $this->post('/giftcard',                App\Controllers\Admin\GiftCardController::class . ':add');
        $this->post('/giftcard/ajax',           App\Controllers\Admin\GiftCardController::class . ':ajaxQuery');
        $this->delete('/giftcard/{id}',         App\Controllers\Admin\GiftCardController::class . ':delete');

        // Ann Mange
        $this->get('/announcement',             App\Controllers\Admin\AnnController::class . ':index');
        $this->get('/announcement/create',      App\Controllers\Admin\AnnController::class . ':create');
        $this->post('/announcement',            App\Controllers\Admin\AnnController::class . ':add');
        $this->get('/announcement/{id}/edit',   App\Controllers\Admin\AnnController::class . ':edit');
        $this->put('/announcement/{id}',        App\Controllers\Admin\AnnController::class . ':update');
        $this->delete('/announcement',          App\Controllers\Admin\AnnController::class . ':delete');
        $this->post('/announcement/ajax',       App\Controllers\Admin\AnnController::class . ':ajax');

        // Detect Mange
        $this->get('/detect',                   App\Controllers\Admin\DetectController::class . ':index');
        $this->get('/detect/create',            App\Controllers\Admin\DetectController::class . ':create');
        $this->post('/detect',                  App\Controllers\Admin\DetectController::class . ':add');
        $this->get('/detect/{id}/edit',         App\Controllers\Admin\DetectController::class . ':edit');
        $this->put('/detect/{id}',              App\Controllers\Admin\DetectController::class . ':update');
        $this->delete('/detect',                App\Controllers\Admin\DetectController::class . ':delete');
        $this->get('/detect/log',               App\Controllers\Admin\DetectController::class . ':log');
        $this->post('/detect/ajax',             App\Controllers\Admin\DetectController::class . ':ajax_rule');
        $this->post('/detect/log/ajax',         App\Controllers\Admin\DetectController::class . ':ajax_log');

        // IP Mange
        $this->get('/block',                    App\Controllers\Admin\IpController::class . ':block');
        $this->get('/unblock',                  App\Controllers\Admin\IpController::class . ':unblock');
        $this->post('/unblock',                 App\Controllers\Admin\IpController::class . ':doUnblock');
        $this->get('/login',                    App\Controllers\Admin\IpController::class . ':index');
        $this->get('/alive',                    App\Controllers\Admin\IpController::class . ':alive');
        $this->post('/block/ajax',              App\Controllers\Admin\IpController::class . ':ajax_block');
        $this->post('/unblock/ajax',            App\Controllers\Admin\IpController::class . ':ajax_unblock');
        $this->post('/login/ajax',              App\Controllers\Admin\IpController::class . ':ajax_login');
        $this->post('/alive/ajax',              App\Controllers\Admin\IpController::class . ':ajax_alive');

        // User Mange
        $this->get('/user',                     App\Controllers\Admin\UserController::class . ':index');
        $this->get('/user/{id}/edit',           App\Controllers\Admin\UserController::class . ':edit');
        $this->put('/user/{id}',                App\Controllers\Admin\UserController::class . ':update');
        $this->delete('/user',                  App\Controllers\Admin\UserController::class . ':delete');
        $this->post('/user/changetouser',       App\Controllers\Admin\UserController::class . ':changetouser');
        $this->post('/user/ajax',               App\Controllers\Admin\UserController::class . ':ajax');
        $this->post('/user/create',             App\Controllers\Admin\UserController::class . ':createNewUser');

        // Coupon Mange
        $this->get('/coupon',                   App\Controllers\Admin\CouponController::class . ':index');
        $this->get('/coupon/details/{id}',      App\Controllers\Admin\CouponController::class . ':get');
        $this->post('/coupon',                  App\Controllers\Admin\CouponController::class . ':save');
        $this->put('/coupon/{id}',              App\Controllers\Admin\CouponController::class . ':update');
        $this->delete('/coupon/{id}',           App\Controllers\Admin\CouponController::class . ':delete');

        // Subscribe Log Mange
        $this->get('/subscribe',                App\Controllers\Admin\SubscribeLogController::class . ':index');
        $this->post('/subscribe/ajax',          App\Controllers\Admin\SubscribeLogController::class . ':subscribe_ajax');

        // 指定用户订阅记录
        $this->get('/user/{id}/sublog',         App\Controllers\Admin\UserLog\SubLogController::class . ':index');
        $this->post('/user/{id}/sublog/ajax',   App\Controllers\Admin\UserLog\SubLogController::class . ':ajax');

        // 指定用户审计记录
        $this->get('/user/{id}/detect',         App\Controllers\Admin\UserLog\DetectLogController::class . ':index');
        $this->post('/user/{id}/detect/ajax',   App\Controllers\Admin\UserLog\DetectLogController::class . ':ajax');

        // 指定用户登录记录
        $this->get('/user/{id}/login',          App\Controllers\Admin\UserLog\LoginLogController::class . ':index');
        $this->post('/user/{id}/login/ajax',    App\Controllers\Admin\UserLog\LoginLogController::class . ':ajax');

        // 设置中心
        $this->get('/setting',                  App\Controllers\Admin\SettingController::class . ':index');
        $this->post('/setting',                 App\Controllers\Admin\SettingController::class . ':save');
        $this->post('/setting/email',           App\Controllers\Admin\SettingController::class . ':test');
    })->add(new Admin());

    if ($_ENV['enableAdminApi']){
        $app->group('/admin/api', function () {
            $this->get('/nodes',     App\Controllers\Admin\ApiController::class . ':getNodeList');
            $this->get('/node/{id}', App\Controllers\Admin\ApiController::class . ':getNodeInfo');
            $this->get('/ping',      App\Controllers\Admin\ApiController::class . ':ping');

            // Re-bind controller, bypass admin token require
            $this->post('/node',       App\Controllers\Admin\NodeController::class . ':add');
            $this->put('/node/{id}',   App\Controllers\Admin\NodeController::class . ':update');
            $this->delete('/node',     App\Controllers\Admin\NodeController::class . ':delete');
        })->add(new AuthorizationBearer($_ENV['adminApiToken']));
    }

    // mu
    $app->group('/mod_mu', function () {
        // 流媒体检测
        $this->post('/media/saveReport',    App\Controllers\Mod_Mu\NodeController::class . ':saveReport');
        // 其他
        $this->get('/nodes/{id}/info',      App\Controllers\Mod_Mu\NodeController::class . ':get_info');
        $this->post('/nodes/{id}/info',     App\Controllers\Mod_Mu\NodeController::class . ':info');
        $this->get('/nodes',                App\Controllers\Mod_Mu\NodeController::class . ':get_all_info');
        $this->post('/nodes/config',        App\Controllers\Mod_Mu\NodeController::class . ':getConfig');

        $this->get('/users',                App\Controllers\Mod_Mu\UserController::class . ':index');
        $this->get('/users/traffic',        App\Controllers\Mod_Mu\UserController::class . ':getTraffic');
        $this->post('/users/traffic',       App\Controllers\Mod_Mu\UserController::class . ':addTraffic');
        $this->post('/users/aliveip',       App\Controllers\Mod_Mu\UserController::class . ':addAliveIp');
        $this->post('/users/detectlog',     App\Controllers\Mod_Mu\UserController::class . ':addDetectLog');

        $this->get('/func/detect_rules',    App\Controllers\Mod_Mu\FuncController::class . ':get_detect_logs');
        $this->post('/func/block_ip',       App\Controllers\Mod_Mu\FuncController::class . ':addBlockIp');
        $this->get('/func/block_ip',        App\Controllers\Mod_Mu\FuncController::class . ':get_blockip');
        $this->get('/func/unblock_ip',      App\Controllers\Mod_Mu\FuncController::class . ':get_unblockip');
        $this->get('/func/ping',            App\Controllers\Mod_Mu\FuncController::class . ':ping');
        //============================================
    })->add(new Mod_Mu());

    $app->group('/link', function () {
        $this->get('/{token}',              App\Controllers\LinkController::class . ':GetContent');
    });

    //通用訂閲
    $app->group('/sub', function () {
        $this->get('/{token}/{subtype}',    App\Controllers\SubController::class . ':getContent');
    });

    $app->group('/getClient', function () {
        $this->get('/{token}',              App\Controllers\UserController::class . ':getClientfromToken');
    });
};
