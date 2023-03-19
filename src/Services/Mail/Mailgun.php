<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Setting;
use Exception;
use Mailgun\Mailgun as MailgunService;
use Psr\Http\Client\ClientExceptionInterface;
use function count;

final class Mailgun extends Base
{
    private array $config;
    private MailgunService $mg;
    private mixed $domain;
    private mixed $sender;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->mg = MailgunService::create($this->config['key']);
        $this->domain = $this->config['domain'];
        $this->sender = $this->config['sender_name'] . ' <' . $this->config['sender'] . '>';
    }

    public function getConfig(): array
    {
        $configs = Setting::getClass('mailgun');

        return [
            'key' => $configs['mailgun_key'],
            'domain' => $configs['mailgun_domain'],
            'sender' => $configs['mailgun_sender'],
            'sender_name' => $configs['mailgun_sender_name'],
        ];
    }

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     */
    public function send($to, $subject, $text, $file): void
    {
        $inline = [];

        foreach ($file as $file_raw) {
            $inline[] = ['filePath' => $file_raw, 'filename' => basename($file_raw)];
        }

        if (count($inline) === 0) {
            $this->mg->messages()->send($this->domain, [
                'from' => $this->sender,
                'to' => $to,
                'subject' => $subject,
                'html' => $text,
            ]);
        } else {
            $this->mg->messages()->send($this->domain, [
                'from' => $this->sender,
                'to' => $to,
                'subject' => $subject,
                'html' => $text,
                'inline' => $inline,
            ]);
        }
    }
}
