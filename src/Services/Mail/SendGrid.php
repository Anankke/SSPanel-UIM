<?php

namespace App\Services\Mail;

use App\Services\Config;
use SendGrid\Attachment;
use SendGrid\Content;
use SendGrid\Email;
use SendGrid\Mail;

class SendGrid extends Base
{
    private $config;
    private $sg;
    private $sender;
    private $name;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->sg = new \SendGrid($this->config['key']);
        $this->sender = $this->config['sender'];
        $this->name = $this->config['name'];
    }

    public function getConfig()
    {
        return [
            'key' => Config::get('sendgrid_key'),
            'sender' => Config::get('sendgrid_sender'),
            'name' => Config::get('sendgrid_name')
        ];
    }

    public function send($to_address, $subject_raw, $text, $files)
    {
        $from = new From($this->sender, $this->name);
        $subject = $subject_raw;
        $to = new To($to_address, null);
        $content = new Content('text/html', $text);
        $mail = new Mail($from, $to, $subject, $content);

        //$reply_to = new ReplyTo("test@example.com", "Optional Name");
        //$mail->setReplyTo($reply_to);		
		
        foreach ($files as $file) {
            $attachment = new Attachment();
            $attachment->setContent(base64_encode(file_get_contents($file)));
            $attachment->setType('application/octet-stream');
            $attachment->setFilename(basename($file));
            $attachment->setDisposition('attachment');
            $attachment->setContentId('backup');
            $mail->addAttachment($attachment);
        }

        $response = $this->sg->client->mail()->send()->post($mail);
        echo $response->body();
    }
}
