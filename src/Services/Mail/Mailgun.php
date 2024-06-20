<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use Exception;
use Mailgun\Mailgun as MG;
use Psr\Http\Client\ClientExceptionInterface;

final class Mailgun extends Base
{
    private MG $mg;
    private string $domain;
    private string $sender;

    public function __construct()
    {
        $configs = Config::getClass('email');

        $this->mg = MG::create($configs['mailgun_key']);
        $this->domain = $configs['mailgun_domain'];
        $this->sender = $configs['mailgun_sender_name'] . ' <' . $configs['mailgun_sender'] . '>';
    }

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     */
    public function send($to, $subject, $body): void
    {
        $this->mg->messages()->send($this->domain, [
            'from' => $this->sender,
            'to' => $to,
            'subject' => $subject,
            'html' => $body,
        ]);
    }
}
