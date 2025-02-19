<?php

namespace App\Services\Gateway\Cryptomus;

final class RequestBuilder
{
    const API_URL = "https://api.cryptomus.com/";

    /**
     * @var string
     */
    private $secretKey;
    /**
     * @var string
     */
    private $merchantUuid;


    /**
     * @param string $secretKey
     * @param string $merchantUuid
     */
    public function __construct($secretKey, $merchantUuid)
    {
        $this->secretKey = $secretKey;
        $this->merchantUuid = $merchantUuid;
    }

    /**
     * @param $uri
     * @param array $data
     * @return bool|mixed
     * @throws RequestBuilderException
     */
    public function sendRequest($uri, array $data = [])
    {
        $curl = curl_init();
        $url = self::API_URL . $uri;
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json;charset=UTF-8',
            'Content-Length: ' . strlen($body),
            'merchant: ' . $this->merchantUuid,
            'sign: ' . md5(base64_encode($body) . $this->secretKey)
        ];

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => 1,
            ]
        );

        $response = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            throw new RequestBuilderException(curl_error($curl), $responseCode, $uri);
        }

        if (false === empty($response)) {
            $json = json_decode($response, true);
            if (is_null($json)) {
                throw new RequestBuilderException(json_last_error_msg(), $responseCode, $uri);
            }

            if ($responseCode !== 200 || (!is_null($json['state']) && $json['state'] != 0)) {
                if (!empty($json['message'])) {
                    throw new RequestBuilderException($json['message'], $responseCode, $uri);
                }

                if (!empty($json['errors'])) {
                    throw new RequestBuilderException('Validation error', $responseCode, $uri, $json['errors']);
                }
            }

            if (!empty($json['result']) && !is_null($json['state']) && $json['state'] == 0) {
                return $json['result'];
            }
        }

        return true;
    }
}
