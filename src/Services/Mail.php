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
use Smarty;

final class Mail
{
    public static function getClient()
    {
        $driver = Setting::obtain('mail_driver');
        switch ($driver) {
            case 'mailgun':
                return new Mailgun();
            case 'ses':
                return new Ses();
            case 'smtp':
                return new Smtp();
            case 'sendgrid':
                return new SendGrid();
            case 'postal':
                return new Postal();
            default:
                return new NullMail();
        }
    }

    public static function genHtml($template, $ary)
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

    public static function send($to, $subject, $template, $ary = [], $files = [])
    {
        $text = self::genHtml($template, $ary);
        return self::getClient()->send($to, $subject, $text, $files);
    }
}
