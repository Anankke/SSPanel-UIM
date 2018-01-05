<?php

class Paymentwall_OneTimeToken extends Paymentwall_ApiObject
{
	const GATEWAY_TOKENIZATION_URL = 'https://pwgateway.com/api/token';

	public function getToken()
	{
		return $this->token;
	}

	public function isTest()
	{
		return $this->test;
	}

	public function isActive()
	{
		return $this->active;
	}

	public function getExpirationTime()
	{
		return $this->expires_in;
	}

	public function getEndpointName()
	{
		return self::API_OBJECT_ONE_TIME_TOKEN;
	}
}