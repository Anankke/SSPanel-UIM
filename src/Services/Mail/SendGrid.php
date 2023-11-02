<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use SendGrid as SG;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeException;
use function base64_encode;
use function basename;
use function file_get_contents;

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
    public function send($to, $subject, $text, $files): void
    {
        $this->email->setSubject($subject);
        $this->email->addTo($to);
        $this->email->addContent('text/html', $text);

        if ($files !== []) {
            foreach ($files as $file_raw) {
                $this->email->addAttachment(
                    base64_encode(file_get_contents($file_raw)),
                    'application/octet-stream',
                    basename($file_raw),
                    'attachment',
                    'attachment'
                );
            }
        }

        $response = $this->sg->send($this->email);
        echo $response->body();
    }
}
