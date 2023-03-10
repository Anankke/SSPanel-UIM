<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Setting;
use SendGrid\Mail\Mail;

final class SendGrid extends Base
{
    private array $config;
    private \SendGrid $sg;
    private mixed $sender;
    private mixed $name;
    private Mail $email;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->sg = new \SendGrid($this->config['key']);
        $this->sender = $this->config['sender'];
        $this->name = $this->config['name'];
        $this->email = new Mail();
    }

    public function getConfig()
    {
        $configs = Setting::getClass('sendgrid');

        return [
            'key' => $configs['sendgrid_key'],
            'sender' => $configs['sendgrid_sender'],
            'name' => $configs['sendgrid_name'],
        ];
    }

    public function send($to, $subject, $text, $files): void
    {
        $this->email->setFrom($this->sender, $this->name);
        $this->email->setSubject($subject);
        $this->email->addTo($to, null);
        $this->email->addContent('text/html', $text);

        foreach ($files as $file) {
            $this->email->addAttachment(
                base64_encode(file_get_contents($file)),
                'application/octet-stream',
                basename($file),
                'attachment',
                'attachment'
            );
        }

        $response = $this->sg->send($this->email);
        echo $response->body();
    }
}
