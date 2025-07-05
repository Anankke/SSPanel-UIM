<?php

declare(strict_types=1);

namespace App\Services\Gateway\Cryptomus;

final class RequestBuilder
{
    private const API_URL = 'https://api.cryptomus.com/';

    private string $secretKey;
    private string $merchantUuid;

    public function __construct(string $secretKey, string $merchantUuid)
    {
        $this->secretKey = $secretKey;
        $this->merchantUuid = $merchantUuid;
    }

    /**
     * @param string $uri
     * @param array $data
     *
     * @return bool|mixed
     *
     * @throws RequestBuilderException
     */
    public function sendRequest(string $uri, array $data = [])
    {
        $curl = curl_init();
        $url = self::API_URL . $uri;
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json;charset=UTF-8',
            'Content-Length: ' . strlen($body),
            'merchant: ' . $this->merchantUuid,
            'sign: ' . md5(base64_encode($body) . $this->secretKey),
        ];

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => 1,
            ],
        );

        $response = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            throw new RequestBuilderException(curl_error($curl), $responseCode, $uri);
        }

        if ($response !== '') {
            $json = json_decode($response, true);
            if ($json === null) {
                throw new RequestBuilderException(json_last_error_msg(), $responseCode, $uri);
            }

            if ($responseCode !== 200 || ($json['state'] !== null && $json['state'] !== 0)) {
                if (isset($json['message']) && $json['message'] !== '') {
                    throw new RequestBuilderException($json['message'], $responseCode, $uri);
                }

                if (isset($json['errors']) && $json['errors'] !== []) {
                    throw new RequestBuilderException('Validation error', $responseCode, $uri, $json['errors']);
                }
            }

            if (isset($json['result']) && $json['state'] !== null && $json['state'] === 0) {
                return $json['result'];
            }
        }

        return true;
    }
}
