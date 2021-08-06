<?php


namespace App\Services\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use App\Services\Config;

class Smtp extends Base
{
    private $mail;
    private $config;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $mail = new PHPMailer();
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->config['host'];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $this->config['username'];                 // SMTP username
        $mail->Password = $this->config['passsword'];                    // SMTP password
        if ($_ENV['smtp_ssl']) {
            $mail->SMTPSecure = ($_ENV['smtp_port'] == 587 ? 'tls' : 'ssl');                            // Enable TLS encryption, `ssl` also accepted
        }
        $mail->Port = $this->config['port'];                                    // TCP port to connect to
        $mail->setFrom($this->config['sender'], $this->config['name']);
        $mail->addReplyTo($this->config['reply_to'], $this->config['reply_to_name']);
        if ($_ENV['smtp_bbc'] != '') {
            $mail->addBCC($this->config['smtp_bbc']);
        }
        $mail->CharSet = 'UTF-8';
        $this->mail = $mail;
    }

    public function getConfig()
    {
        return [
            'host' => $_ENV['smtp_host'],
            'username' => $_ENV['smtp_username'],
            'port' => $_ENV['smtp_port'],
            'sender' => $_ENV['smtp_sender'],
            'name' => $_ENV['smtp_name'],
            'passsword' => $_ENV['smtp_password'],
            'reply_to' => $_ENV['smtp_reply_to'],
            'reply_to_name' => $_ENV['smtp_reply_to_name'],
            'smtp_bbc' => $_ENV['smtp_bbc']
        ];
    }

    public function save_mail($mail)
    {
        $path = $_ENV['smtp_save_path'];
        $imapStream = imap_open($path, $mail->Username, $mail->Password);
        $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage(), '\\Seen');
        imap_close($imapStream);
        return $result;
    }

    public function send($to, $subject, $text, $files)
    {
        $mail = $this->mail;
        $mail->addAddress($to);     // Add a recipient
        $mail->isHTML();
        $mail->Subject = $subject;
        $mail->Body = $text;
        foreach ($files as $file) {
            $mail->addAttachment($file);
        }

        if (!$mail->send()) {
            throw new \Exception($mail->ErrorInfo);
        }
        if ($_ENV['smtp_save_sent']) {
            $this->save_mail($mail);
        }
    }
}
