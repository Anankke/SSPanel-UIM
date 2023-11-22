<?php

declare(strict_types=1);

namespace App\Services\Gateway\MeowPay;

use function json_decode;

final class MeowPayment
{
    private $url = 'https://api.meowpay.org/json_rpc/';
    private $app_id;
    private $trade_no;
    private $amount;
    private $currency_type;
    private $return_url;
    private $notify_url;

    public function __construct(
        string $app_id,
        string $trade_no,
        string $currency_type,
        int $amount,
        string|null $return_url = null,
        string|null $notify_url = null,
    ) {
        $this->app_id = $app_id;
        $this->trade_no = $trade_no;
        $this->amount = $amount;
        $this->currency_type = $currency_type;
        $this->return_url = $return_url;
        $this->notify_url = $notify_url;
    }

    public function getPayLink($url = null, $method = 'create_payment'): string
    {
        if ($url === null) {
            $url = $this->url;
        }
        $js_rq_data = [];
        $js_rq_data['jsonrpc'] = '2.0';
        $js_rq_data['id'] = '0';
        $js_rq_data['method'] = $method;
        $js_rq_data['params']['app_id'] = $this->app_id;
        $js_rq_data['params']['trade_no'] = $this->trade_no;
        $js_rq_data['params']['amount'] = $this->amount;
        $js_rq_data['params']['currency_type'] = $this->currency_type;
        $js_rq_data['params']['return_url'] = $this->return_url;
        $js_rq_data['params']['notify_url'] = $this->notify_url;
        $rq = json_encode($js_rq_data, JSON_PARTIAL_OUTPUT_ON_ERROR);
        $response = postRequest($url, $rq);
        return $response['result']['payment_info']['pay_link'];
    }
}
function postRequest($url, $data): array
{
    $headerArray = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Accept-Charset: utf-8',
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYSTATUS, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}
