<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use GuzzleHttp\Client;

final class Postmark extends Base
{
    public function send($to, $subject, $body): void
    {
        $configs = Config::getClass('email');
        $client = new Client();
        $res = $client->post('https://api.postmarkapp.com/email', [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Postmark-Server-Token' => $configs['postmark_key'],
            ],
            'json' => [
                'From' => $configs['postmark_sender'],
                'To' => $to,
                'Subject' => $subject,
                'HtmlBody' => $body,
                'MessageStream' => $configs['postmark_stream'],
            ],
        ]);

        if ($res->getStatusCode() !== 200) {
            throw new Exception($msg_response->getBody()->getContents());
        }
    }
}
