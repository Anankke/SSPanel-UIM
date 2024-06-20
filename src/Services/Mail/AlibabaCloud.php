<?php

declare(strict_types=1);

namespace App\Services\Mail;

use AlibabaCloud\SDK\Dm\V20170622\Dm;
use AlibabaCloud\SDK\Dm\V20170622\Models\SingleSendMailRequest;
use App\Models\Config;
use Darabonba\OpenApi\Models\Config as OpenApiConfig;

final class AlibabaCloud extends Base
{
    private Dm $client;
    private string $account_name;
    private string $from_alias;

    public function __construct()
    {
        $configs = Config::getClass('email');

        $this->account_name = $configs['alibabacloud_dm_account_name'];
        $this->from_alias = $configs['alibabacloud_dm_from_alias'];

        $this->client = new Dm(new OpenApiConfig([
            'accessKeyId' => $configs['alibabacloud_dm_access_key_id'],
            'accessKeySecret' => $configs['alibabacloud_dm_access_key_secret'],
            'endpoint' => $configs['alibabacloud_dm_endpoint'],
        ]));
    }

    public function send($to, $subject, $body): void
    {
        $request = new SingleSendMailRequest([
            'accountName' => $this->account_name,
            'addressType' => 1,
            'replyToAddress' => false,
            'toAddress' => $to,
            'subject' => $subject,
            'htmlBody' => $body,
            'fromAlias' => $this->from_alias,
        ]);

        $this->client->singleSendMail($request);
    }
}
