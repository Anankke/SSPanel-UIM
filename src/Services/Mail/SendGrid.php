<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Setting;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeException;

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

    public function getConfig(): array
    {
        $configs = Setting::getClass('sendgrid');

        return [
            'key' => $configs['sendgrid_key'],
            'sender' => $configs['sendgrid_sender'],
            'name' => $configs['sendgrid_name'],
        ];
    }

    /**
     * @throws TypeException
     */
    public function send($to, $subject, $text, $file): void
    {
        $this->email->setFrom($this->sender, $this->name);

        $this->email->setSubject($subject);

        $this->email->addTo($to);

        $this->email->addContent('text/html', $text);

        foreach ($file as $file_raw) {
            $this->email->addAttachment(
                base64_encode(file_get_contents($file_raw)),
                'application/octet-stream',
                basename($file_raw),
                'attachment',
                'attachment'
            );
        }

        $response = $this->sg->send($this->email);
        echo $response->body();
    }
}
