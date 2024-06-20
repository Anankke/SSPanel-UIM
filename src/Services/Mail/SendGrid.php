<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use SendGrid as SG;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeException;

final class SendGrid extends Base
{
    private SG $sg;
    private Mail $email;

    /**
     * @throws TypeException
     */
    public function __construct()
    {
        $configs = Config::getClass('email');

        $this->sg = new SG($configs['sendgrid_key']);
        $this->email = new Mail();
        $this->email->setFrom($configs['sendgrid_sender'], $configs['sendgrid_name']);
    }

    /**
     * @throws TypeException
     */
    public function send($to, $subject, $body): void
    {
        $this->email->setSubject($subject);
        $this->email->addTo($to);
        $this->email->addContent('text/html', $body);

        $this->sg->send($this->email);
    }
}
