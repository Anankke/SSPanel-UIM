<?php

namespace App\Services\Mail;

use App\Services\Config;



use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

// Download：https://github.com/aliyun/openapi-sdk-php
// Usage：https://github.com/aliyun/openapi-sdk-php/blob/master/README.md




class Aliyun extends Base
{
    private $config;
    private $sender;
    private $name;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->name = $this->config['name'];
        $this->sender = $this->config['sender'];
    }

    public function getConfig()
    {
        return [
            'keyid' => $_ENV['aliyun_accesskeyid'],
            'keysecret' => $_ENV['aliyun_accesskeysecret'],
			'region' => $_ENV['aliyun_regionid'],
            'sender' => $_ENV['aliyun_sender'],
            'name' => $_ENV['aliyun_name'],
        ];
    }

    public function send($to, $subject, $text, $files)
    
    {
    	AlibabaCloud::accessKeyClient($this->config['keyid'], $this->config['keysecret'])
                        ->regionId($this->config['region'])
                        ->asDefaultClient();
    	

		    AlibabaCloud::rpc()
		                          ->product('Dm')
		                          // ->scheme('https') // https | http
		                          ->version('2015-11-23')
		                          ->action('SingleSendMail')
		                          ->method('POST')
		                          ->host('dm.aliyuncs.com')
		                          ->options([
		                                        'query' => [
		                                          'RegionId' => $this->config['region'],
		                                          'AccountName' => $this->sender,
		                                          'AddressType' => "1",
		                                          'ReplyToAddress' => "false",
		                                          'ToAddress' => $to,
		                                          'Subject' => $subject,
		                                          'HtmlBody' => $text,
		                                          'FromAlias' => $this->name,
		                                        ],
		                                    ])
		                          ->request();

    }
}




