<?php

namespace SendGrid\Test;

use SendGrid\Client;

class MockClient extends Client
{
    protected $requestBody;
    protected $requestHeaders;
    protected $url;

    public function makeRequest($method, $url, $requestBody = null, $requestHeaders = null)
    {
        $this->requestBody = $requestBody;
        $this->requestHeaders = $requestHeaders;
        $this->url = $url;
        return $this;
    }
}