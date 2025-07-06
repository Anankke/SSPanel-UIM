<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Config;
use App\Services\Mail\AlibabaCloud;
use App\Services\Mail\Mailchimp;
use App\Services\Mail\Mailgun;
use App\Services\Mail\NullMail;
use App\Services\Mail\Postal;
use App\Services\Mail\Postmark;
use App\Services\Mail\Resend;
use App\Services\Mail\SendGrid;
use App\Services\Mail\Ses;
use App\Services\Mail\Smtp;
use Psr\Http\Client\ClientExceptionInterface;
use SendGrid\Mail\TypeException;
use Smarty\Exception;
use Smarty\Smarty;

/*
 * Mail Service
 */
final class Mail
{
    public static function getClient(): AlibabaCloud|Mailchimp|Mailgun|NullMail|Postal|Postmark|Resend|SendGrid|Ses|Smtp
    {
        return match (Config::obtain('email_driver')) {
            'alibabacloud' => new AlibabaCloud(),
            'mailchimp' => new Mailchimp(),
            'mailgun' => new Mailgun(),
            'postal' => new Postal(),
            'resend' => new Resend(),
            'sendgrid' => new SendGrid(),
            'ses' => new Ses(),
            'smtp' => new Smtp(),
            'postmark' => new Postmark(),
            default => new NullMail(),
        };
    }

    /**
     * @throws Exception
     */
    public static function genHtml($template, $ary): false|string
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir(BASE_PATH . '/resources/email/');
        $smarty->setCompileDir(BASE_PATH . '/storage/framework/smarty/compile/');
        $smarty->setCacheDir(BASE_PATH . '/storage/framework/smarty/cache/');
        $smarty->assign('config', View::getConfig());

        foreach ($ary as $key => $value) {
            $smarty->assign($key, $value);
        }

        return $smarty->fetch($template);
    }

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     * @throws TypeException
     */
    public static function send($to, $subject, $template, $array = []): void
    {
        $body = self::genHtml($template, $array);

        self::getClient()->send($to, $subject, $body);
    }
}
