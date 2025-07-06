<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use Resend as RS;

final class Resend extends Base
{
    private RS\Client $resend;
    private string $from;

    public function __construct()
    {
        $configs = Config::getClass('email');
        $this->resend = RS::client($configs['resend_api_key']);
        $this->from = $configs['resend_from'];
    }

    public function send($to, $subject, $body): void
    {
        $this->resend->emails->send([
            'from' => $this->from,
            'to' => [$to],
            'subject' => $subject,
            'html' => $body,
        ]);
    }
}
