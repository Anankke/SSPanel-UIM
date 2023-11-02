<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use Postal\Client;
use Postal\Send\Message;
use function basename;
use function mime_content_type;

final class Postal extends Base
{
    private Client $client;
    private Message $message;

    public function __construct()
    {
        $configs = Config::getClass('email');

        $this->client = new Client($configs['postal_host'], $configs['postal_key']);
        $this->message = new Message();
        $this->message->sender($configs['postal_sender']); # 发件邮箱
        $this->message->from($configs['postal_name'] . ' <' . $configs['postal_sender'] . '>'); # 发件人
        $this->message->replyTo($configs['postal_sender']);
    }

    public function send($to, $subject, $text, $files): void
    {
        $this->message->subject($subject);
        $this->message->to($to);
        $this->message->plainBody($text);
        $this->message->htmlBody($text);

        if ($files !== []) {
            foreach ($files as $file_raw) {
                $this->message->attach(basename($file_raw), mime_content_type($file_raw), $file_raw);
            }
        }

        $this->client->send->message($this->message);
    }
}
