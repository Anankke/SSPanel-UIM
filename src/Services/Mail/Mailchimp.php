<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use MailchimpTransactional\ApiClient;

final class Mailchimp extends Base
{
    private ApiClient $mc;
    private string $from_email;
    private string $from_name;

    public function __construct()
    {
        $configs = Config::getClass('email');

        $this->mc = new ApiClient();
        $this->mc->setApiKey($configs['mailchimp_key']);
        $this->from_email = $configs['mailchimp_from_email'];
        $this->from_name = $configs['mailchimp_from_name'];
    }

    public function send($to, $subject, $body): void
    {
        $this->mc->messages->send([
            'message' => [
                'html' => $body,
                'subject' => $subject,
                'from_email' => $this->from_email,
                'from_name' => $this->from_name,
                'to' => [['email' => $to]],
            ],
        ]);
    }
}
