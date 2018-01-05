<?php

namespace TelegramBot\Api;

use TelegramBot\Api\Types\Message;

class Botan
{

    /**
     * @var string Tracker url
     */
    const BASE_URL = 'https://api.botan.io/track';

    /**
     * CURL object
     *
     * @var
     */
    protected $curl;


    /**
     * Yandex AppMetrica application api_key
     *
     * @var string
     */
    protected $token;

    /**
     * Botan constructor
     *
     * @param string $token
     *
     * @throws \Exception
     */
    public function __construct($token)
    {
        if (!function_exists('curl_version')) {
            throw new Exception('CURL not installed');
        }

        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Token should be a string');
        }

        $this->token = $token;
        $this->curl = curl_init();
    }

    /**
     * Event tracking
     *
     * @param \TelegramBot\Api\Types\Message $message
     * @param string $eventName
     *
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\HttpException
     */
    public function track(Message $message, $eventName = 'Message')
    {
        $uid = $message->getFrom()->getId();

        $options = [
            CURLOPT_URL => self::BASE_URL . "?token={$this->token}&uid={$uid}&name={$eventName}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => $message->toJson()
        ];

        curl_setopt_array($this->curl, $options);
        $result = BotApi::jsonValidate(curl_exec($this->curl), true);

        BotApi::curlValidate($this->curl);

        if ($result['status'] !== 'accepted') {
            throw new Exception('Error Processing Request');
        }
    }

    /**
     * Destructor. Close curl
     */
    public function __destruct()
    {
        $this->curl && curl_close($this->curl);
    }
}
