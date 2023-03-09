<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Setting;
use Postal\Client;
use Postal\SendMessage;

final class Postal extends Base
{
    private array $config;
    private Client $client;
    private SendMessage $message;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->client = new Client($this->config['host'], $this->config['key']);
        $this->message = new SendMessage($this->client);
        $this->message->sender($this->config['sender']); # 发件邮箱
        $this->message->from($this->config['name']. ' <' . $this->config['sender'] . '>'); # 发件人
        $this->message->replyTo($this->config['sender']);
    }

    public function getConfig()
    {
        $configs = Setting::getClass('postal');
        return [
            'host' => $configs['postal_host'],
            'key' => $configs['postal_key'],
            'sender' => $configs['postal_sender'],
            'name' => $configs['postal_name'],
        ];
    }

    public function send($to, $subject, $text, $files): void
    {
        $this->message->subject($subject);
        $this->message->to($to);
        $this->message->plainBody($text);
        $this->message->htmlBody($text);
        foreach ($files as $file) {
            $this->message->attach(basename($file), 'text/plain', $file);
        }
        $this->message->send();
    }
}
