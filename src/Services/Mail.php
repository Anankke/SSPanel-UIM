<?php

declare(strict_types=1);

namespace App\Services;

/*
 * Mail Service
 */

use App\Models\Setting;
use App\Services\Mail\Mailgun;
use App\Services\Mail\NullMail;
use App\Services\Mail\Postal;
use App\Services\Mail\SendGrid;
use App\Services\Mail\Ses;
use App\Services\Mail\Smtp;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Smarty;

final class Mail
{
    public static function getClient(): Mailgun|Smtp|SendGrid|NullMail|Ses|Postal
    {
        $driver = Setting::obtain('email_driver');
        return match ($driver) {
            'mailgun' => new Mailgun(),
            'ses' => new Ses(),
            'smtp' => new Smtp(),
            'sendgrid' => new SendGrid(),
            'postal' => new Postal(),
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
        // add config
        $smarty->assign('config', Config::getPublicConfig());
        foreach ($ary as $key => $value) {
            $smarty->assign($key, $value);
        }

        return $smarty->fetch($template);
    }

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     */
    public static function send($to, $subject, $template, $ary = [], $files = []): void
    {
        $text = self::genHtml($template, $ary);

        self::getClient()->send($to, $subject, $text, $files);
    }
}
