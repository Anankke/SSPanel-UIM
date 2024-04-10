<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class CaptchaController extends BaseController
{
    private static array $update_field = [
        'captcha_provider',
        'enable_reg_captcha',
        'enable_login_captcha',
        'enable_checkin_captcha',
        'enable_reset_password_captcha',
        // Turnstile
        'turnstile_sitekey',
        'turnstile_secret',
        // Geetest
        'geetest_id',
        'geetest_key',
        // hCaptcha
        'hcaptcha_sitekey',
        'hcaptcha_secret',
    ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $settings = Config::getClass('captcha');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/captcha.tpl')
        );
    }

    public function save(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        foreach (self::$update_field as $item) {
            if (! Config::set($item, $request->getParam($item))) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '保存 ' . $item . ' 时出错',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }
}
