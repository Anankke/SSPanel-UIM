<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Config;
use App\Services\Mail\Mailchimp;
use App\Services\Mail\Mailgun;
use App\Services\Mail\NullMail;
use App\Services\Mail\Postal;
use App\Services\Mail\SendGrid;
use App\Services\Mail\Ses;
use App\Services\Mail\Smtp;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Smarty;

/*
 * Mail Service
 */
final class Mail
{
    public static function getClient(): Mailchimp|Mailgun|NullMail|Postal|SendGrid|Ses|Smtp
    {
        $driver = Config::obtain('email_driver');
        return match ($driver) {
            'mailchimp' => new Mailchimp(),
            'mailgun' => new Mailgun(),
            'postal' => new Postal(),
            'sendgrid' => new SendGrid(),
            'ses' => new Ses(),
            'smtp' => new Smtp(),
            default => new NullMail(),
        };
    }

    /**
     * @throws Exception
     */
    public static function genHtml($template, $ary): false|string
    {
        $smarty = new Smarty();
        $smarty->settemplatedir(BASE_PATH . '/resources/email/');
        $smarty->setcompiledir(BASE_PATH . '/storage/framework/smarty/compile/');
        $smarty->setcachedir(BASE_PATH . '/storage/framework/smarty/cache/');
        $smarty->assign('config', View::getConfig());

        foreach ($ary as $key => $value) {
            $smarty->assign($key, $value);
        }

        return $smarty->fetch($template);
    }

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     */
    public static function send($to, $subject, $template, $array = []): void
    {
        $body = self::genHtml($template, $array);

        self::getClient()->send($to, $subject, $body);
    }
}
