<?php


namespace App\Services\Mail;

use PHPMailer;
use App\Services\Config;

class Smtp extends Base
{
    private $mail;
    private $config;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $mail = new PHPMailer;
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->config['host'];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $this->config['username'];                 // SMTP username
        $mail->Password = $this->config['passsword'];                    // SMTP password
    if (Config::get('smtp_ssl') == 'true') {
        $mail->SMTPSecure = (Config::get('smtp_port') =='587'?'tls':'ssl');                            // Enable TLS encryption, `ssl` also accepted
    }
        $mail->Port = $this->config['port'];                                    // TCP port to connect to
        $mail->setFrom($this->config['sender'], $this->config['name']);
        $mail->CharSet = 'UTF-8';
        $this->mail = $mail;
    }

    public function getConfig()
    {
        return [
            "host" => Config::get('smtp_host'),
            "username" => Config::get('smtp_username'),
            "port" => Config::get('smtp_port'),
            "sender" => Config::get('smtp_sender'),
            "name" => Config::get('smtp_name'),
            "passsword" => Config::get('smtp_passsword')
        ];
    }

    public function send($to, $subject, $text, $files)
    {
        $mail = $this->mail;
        $mail->addAddress($to);     // Add a recipient
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $text;
        foreach ($files as $file) {
            $mail->addAttachment($file);
        }
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        if (!$mail->send()) {
            return true;
        }
        return false;
    }
}
