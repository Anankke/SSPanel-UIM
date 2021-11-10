<?php

namespace App\Services\Mail;

use App\Models\Setting;
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
        $this->mg = MailgunService::create($this->config['key']);
        $this->domain = $this->config['domain'];
        $this->sender = $this->config['sender'];
    }

    public function getConfig()
    {
        $configs = Setting::getClass('mailgun');
        
        return [
            'key' => $configs['mailgun_key'],
            'domain' => $configs['mailgun_domain'],
            'sender' => $configs['mailgun_sender']
        ];
    }

    public function send($to, $subject, $text, $files)
    {
        $inline = array();
        foreach ($files as $file) {
            $inline[] = array('filePath' => $file, 'filename' => basename($file));
        }
        if (count($inline) == 0) {
            $this->mg->messages()->send($this->domain, [
                    'from' => $this->sender,
                    'to' => $to,
                    'subject' => $subject,
                    'html' => $text
                ]);
        } else {
            $this->mg->messages()->send($this->domain, [
                    'from' => $this->sender,
                    'to' => $to,
                    'subject' => $subject,
                    'html' => $text,
                    'inline' => $inline
                ]);
        }
    }
}
