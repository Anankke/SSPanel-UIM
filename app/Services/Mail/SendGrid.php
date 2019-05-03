<?php

namespace App\Services\Mail;

use App\Services\Config;

class SendGrid extends Base
{
    private $config;
    private $sg;
    private $sender;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->sg = new \SendGrid($this->config["key"]);
        $this->sender = $this->config["sender"];
    }

    public function getConfig()
    {
        return [
            "key" => Config::get('sendgrid_key'),
            "sender" => Config::get('sendgrid_sender')
        ];
    }

    public function send($to_address, $subject_raw, $text, $files)
    {
        $from = new \SendGrid\Email(null, $this->sender);
        $subject = $subject_raw;
        $to = new \SendGrid\Email(null, $to_address);
        $content = new \SendGrid\Content("text/html", $text);
        $mail = new \SendGrid\Mail($from, $subject, $to, $content);

        foreach ($files as $file) {
            $attachment = new \SendGrid\Attachment();
            $attachment->setContent(base64_encode(file_get_contents($file)));
            $attachment->setType("application/octet-stream");
            $attachment->setFilename(basename($file));
            $attachment->setDisposition("attachment");
            $attachment->setContentId("backup");
            $mail->addAttachment($attachment);
        }

        $response = $this->sg->client->mail()->send()->post($mail);
        echo $response->body();
    }
}
