<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\Config;
use Aws\Ses\SesClient;

final class Ses extends Base
{
    private SesClient $ses;

    public function __construct()
    {
        $configs = Config::getClass('email');

        $ses = new SesClient([
            'credentials' => [
                'key' => $configs['aws_ses_access_key_id'],
                'secret' => $configs['aws_ses_access_key_secret'],
            ],
            'region' => $configs['aws_ses_region'],
            'version' => 'latest',
        ]);

        $this->ses = $ses;
    }

    public function send($to, $subject, $body): void
    {
        $ses = $this->ses;
        $char_set = 'UTF-8';

        $ses->sendEmail([
            'Destination' => [
                'ToAddresses' => [$to],
            ],
            'Source' => Config::obtain('aws_ses_sender'),
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => $char_set,
                        'Data' => $body,
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
