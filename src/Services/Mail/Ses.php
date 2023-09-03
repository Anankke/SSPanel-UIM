<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Setting;
use Aws\SesV2\SesV2Client;

final class Ses extends Base
{
    private SesV2Client $ses;

    public function __construct()
    {
        $configs = Setting::getClass('aws_ses');

        $ses = new SesV2Client([
            'credentials' => [
                'key' => $configs['aws_access_key_id'],
                'secret' => $configs['aws_secret_access_key'],
            ],
            'region' => $configs['aws_region'],
            'version' => 'latest',
        ]);

        $this->ses = $ses;
    }

    public function send($to, $subject, $text, $files): void
    {
        $ses = $this->ses;
        $char_set = 'UTF-8';

        $ses->sendEmail([
            'Destination' => [
                'ToAddresses' => [$to],
            ],
            'FromEmailAddress' => Setting::obtain('aws_ses_sender'),
            'Content' => [                        
                'Simple' => [
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
            ],
        ]);
    }
}
