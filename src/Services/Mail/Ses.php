<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Setting;
use Aws\Ses\SesClient;

final class Ses extends Base
{
    private SesClient $ses;

    public function __construct()
    {
        $configs = Setting::getClass('aws_ses');

        $ses = new SesClient([
            'credentials' => [
                'key' => $configs['aws_access_key_id'],
                'secret' => $configs['aws_secret_access_key'],
            ],
            'region' => $configs['aws_region'],
            'version' => 'latest',
        ]);

        $this->ses = $ses;
    }

    public function send($to, $subject, $text, $file): void
    {
        $ses = $this->ses;
        $configs = Setting::getClass('aws_ses');
        $char_set = 'UTF-8';

        $ses->sendEmail([
            'Destination' => [
                'ToAddresses' => [$to],
            ],
            'Source' => $configs['aws_ses_sender'],
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => $char_set,
                        'Data' => $text,
                    ],
                    'Text' => [
                        'Charset' => $char_set,
                        'Data' => $text,
                    ],
                ],
                'Subject' => [
                    'Charset' => $char_set,
                    'Data' => $subject,
                ],
            ],

        ]);
    }
}
