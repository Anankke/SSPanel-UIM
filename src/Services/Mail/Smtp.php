<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class Smtp extends Base
{
    private PHPMailer $mail;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $configs = Config::getClass('email');

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $configs['smtp_host'];
        $mail->Port = $configs['smtp_port'];
        $mail->SMTPAuth = ! ($configs['smtp_username'] === '' && $configs['smtp_password'] === '');
        $mail->CharSet = 'UTF-8';
        $mail->Username = $configs['smtp_username'];
        $mail->Password = $configs['smtp_password'];
        $mail->setFrom($configs['smtp_sender'], $configs['smtp_name']);

        if ($configs['smtp_ssl']) {
            $mail->SMTPSecure = ($configs['smtp_port'] === '587' ? 'tls' : 'ssl');
        }

        if ($configs['smtp_bbc'] !== '') {
            $mail->addBCC($configs['smtp_bbc']);
        }

        $this->mail = $mail;
    }

    /**
     * @throws Exception
     */
    public function send($to, $subject, $body): void
    {
        $mail = $this->mail;
        $mail->addAddress($to);     // Add a recipient
        $mail->isHTML();
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    }
}
