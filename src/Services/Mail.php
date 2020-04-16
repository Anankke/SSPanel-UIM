<?php

namespace App\Services;

/***
 * Mail Service
 */

use App\Services\Mail\Mailgun;
use App\Services\Mail\Ses;
use App\Services\Mail\Smtp;
use App\Services\Mail\SendGrid;
use App\Services\Mail\NullMail;
use Smarty;

class Mail
{
    /**
     * @return Mailgun|NullMail|SendGrid|Ses|Smtp|null
     */
    public static function getClient()
    {
        $driver = $_ENV['mailDriver'];
        switch ($driver) {
            case 'mailgun':
                return new Mailgun();
            case 'ses':
                return new Ses();
            case 'smtp':
                return new Smtp();
            case 'sendgrid':
                return new SendGrid();
            default:
                return new NullMail();
        }
    }

    /**
     * @param $template
     * @param $ary
     * @return mixed
     */
    public static function genHtml($template, $ary)
    {
        $smarty = new smarty();
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
     * @param $to
     * @param $subject
     * @param $template
     * @param $ary
     * @param $files
     * @return bool|void
     */
    public static function send($to, $subject, $template, $ary = [], $files = [])
    {
        $text = self::genHtml($template, $ary);
        return self::getClient()->send($to, $subject, $text, $files);
    }
}
