<?php

declare(strict_types=1);

namespace App\Services\Gateway\Cryptomus;

final class Payment
{
    private RequestBuilder $requestBuilder;
    private string $version = 'v1';

    public function __construct(string $paymentKey, string $merchantUuid)
    {
        $this->requestBuilder = new RequestBuilder($paymentKey, $merchantUuid);
    }

    /**
     * @param array $parameters Additional parameters
     *
     * @return bool|mixed
     *
     * @throws RequestBuilderException
     */
    public function services(array $parameters = [])
    {
        return $this->requestBuilder->sendRequest($this->version . '/payment/services', $parameters);
    }

    /**
     * @param array $data
     * - @var string amount: Amount to pay
     * - @var string currency: Payment currency
     * - @var string network: Payment network
     * - @var string order_id: Order ID in your system
     * - @var string url_return: Redirect link
     * - @var string url_callback: Callback link
     * - @var boolean is_payment_multiple: Allow surcharges on payment *
     * - @var string lifetime: Payment lifetime in seconds
     * - @var string to_currency: Currency to convert amount to
     *
     * @return bool|mixed
     *
     * @throws RequestBuilderException
     */
    public function create(array $data)
    {
        return $this->requestBuilder->sendRequest($this->version . '/payment', $data);
    }

    /**
     * uuid or order_id
     *
     * @param array $data
     * - @var string uuid
     * - @var string order_id
     *
     * @return bool|mixed
     *
     * @throws RequestBuilderException
     */
    public function info($data = [])
    {
        return $this->requestBuilder->sendRequest($this->version . '/payment/info', $data);
    }

    /**
     * @param string|int $page Pagination cursor
     * @param array $parameters Additional parameters
     *
     * @return bool|mixed
     *
     * @throws RequestBuilderException
     */
    public function history($page = 1, array $parameters = [])
    {
        $data = array_merge($parameters, ['cursor' => strval($page)]);
        return $this->requestBuilder->sendRequest($this->version . '/payment/list', $data);
    }

    /**
     * @param array $parameters Additional parameters
     *
     * @return bool|mixed
     *
     * @throws RequestBuilderException
     */
    public function balance(array $parameters = [])
    {
        return $this->requestBuilder->sendRequest($this->version . '/balance', $parameters);
    }

    /**
     * uuid or order_id
     *
     * @param array $data
     * - @var string uuid: Payment's UUID
     * - @var string order_id: Order ID in your system
     *
     * @return bool|mixed
     *
     * @throws RequestBuilderException
     */
    public function reSendNotifications(array $data)
    {
        return $this->requestBuilder->sendRequest($this->version . '/payment/resend', $data);
    }

    /**
     * @param array $data
     * - @var string network: Network
     * - @var string currency: Payment currency
     * - @var string order_id: Order ID in your system
     * - @var string url_callback: Callback url
     *
     * @return bool|mixed
     *
     * @throws RequestBuilderException
     */
    public function createWallet(array $data)
    {
        return $this->requestBuilder->sendRequest($this->version . '/wallet', $data);
    }
}
