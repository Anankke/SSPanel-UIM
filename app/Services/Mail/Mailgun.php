<?php

namespace App\Services\Mail;

use App\Services\Config;
use Mailgun\Mailgun as MailgunService;

class Mailgun extends Base
{
    private $config;
    private $mg;
    private $domain;
    private $sender;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->mg = MailgunService::create($this->config["key"]);
        $this->domain = $this->config["domain"];
        $this->sender = $this->config["sender"];
    }

    public function getConfig()
    {
        return [
            "key" => Config::get('mailgun_key'),
            "domain" => Config::get('mailgun_domain'),
            "sender" => Config::get('mailgun_sender')
        ];
    }

    public function send($to, $subject, $text, $files)
    {
        $inline = array();
        foreach ($files as $file) {
            array_push($inline, array('filePath' => $file, 'filename' => basename($file)));
        }
        if (count($inline) == 0) {
            $this->mg->messages()->send($this->domain, [
                    'from' => $this->sender,
                    'to' => $to,
                    'subject' => $subject,
                    'html' => $text
                ]
            );
        } else {
            $this->mg->messages()->send($this->domain, [
                    'from' => $this->sender,
                    'to' => $to,
                    'subject' => $subject,
                    'html' => $text,
                    'inline' => $inline
                ]
            );
        }
    }
}
