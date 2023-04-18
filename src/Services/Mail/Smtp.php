<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Setting;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class Smtp extends Base
{
    private PHPMailer $mail;

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function __construct()
    {
        $configs = Setting::getClass('smtp');

        $mail = new PHPMailer();
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $configs['smtp_host'];                  // Specify main and backup SMTP servers
        $mail->Port = $configs['smtp_port'];                  // TCP port to connect to
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->CharSet = 'UTF-8';                             // Set utf-8 character set
        $mail->Username = $configs['smtp_username'];          // SMTP username
        $mail->Password = $configs['smtp_password'];          // SMTP password
        $mail->setFrom($configs['smtp_sender'], $configs['smtp_name']);

        if ($configs['smtp_ssl']) {
            // Enable TLS encryption, `ssl` also accepted
            $mail->SMTPSecure = ($configs['smtp_port'] === '587' ? 'tls' : 'ssl');
        }

        if ($configs['smtp_bbc'] !== '') {
            $mail->addBCC($configs['smtp_bbc']);
        }

        $this->mail = $mail;
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws Exception
     */
    public function send($to, $subject, $text, $file): void
    {
        $mail = $this->mail;
        $mail->addAddress($to);     // Add a recipient
        $mail->isHTML();
        $mail->Subject = $subject;
        $mail->Body = $text;
        foreach ($file as $file_raw) {
            $mail->addAttachment($file_raw);
        }

        if (! $mail->send()) {
            throw new Exception($mail->ErrorInfo);
        }
    }
}
