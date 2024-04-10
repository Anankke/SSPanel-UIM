<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Services\Mail;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Throwable;

final class EmailController extends BaseController
{
    private static array $update_field = [
        'email_driver',
        'email_verify_code_ttl',
        'email_password_reset_ttl',
        'email_request_ip_limit',
        'email_request_address_limit',
        // SMTP
        'smtp_host',
        'smtp_username',
        'smtp_password',
        'smtp_port',
        'smtp_name',
        'smtp_sender',
        'smtp_ssl',
        'smtp_bbc',
        // Mailgun
        'mailgun_key',
        'mailgun_domain',
        'mailgun_sender',
        'mailgun_sender_name',
        // Sendgrid
        'sendgrid_key',
        'sendgrid_sender',
        'sendgrid_name',
        // AWS SES
        'aws_ses_access_key_id',
        'aws_ses_access_key_secret',
        'aws_ses_region',
        'aws_ses_sender',
        // Postal
        'postal_host',
        'postal_key',
        'postal_sender',
        'postal_name',
        // Mailchimp
        'mailchimp_key',
        'mailchimp_from_email',
        'mailchimp_from_name',
        // Alibaba Cloud
        'alibabacloud_dm_access_key_id',
        'alibabacloud_dm_access_key_secret',
        'alibabacloud_dm_endpoint',
        'alibabacloud_dm_account_name',
        'alibabacloud_dm_from_alias',
    ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $settings = Config::getClass('email');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/email.tpl')
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

    public function testEmail(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $to = $request->getParam('recipient');

        try {
            Mail::send(
                $to,
                '测试邮件',
                'test.tpl'
            );
        } catch (Throwable $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '测试邮件发送失败 ' . $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '测试邮件发送成功',
        ]);
    }
}
