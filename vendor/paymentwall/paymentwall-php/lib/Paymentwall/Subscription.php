<?php

class Paymentwall_Subscription extends Paymentwall_ApiObject
{
	public function getId()
	{
		return $this->id;
	}

	public function isTrial()
	{
		return $this->is_trial;
	}

	public function isActive()
	{
		return $this->active;
	}

	public function isSuccessful()
	{
		return $this->object == self::API_OBJECT_SUBSCRIPTION;
	}

	public function isExpired()
	{
		return $this->expired;
	}

	public function getEndpointName()
	{
		return self::API_OBJECT_SUBSCRIPTION;
	}

	public function get()
	{
		return $this->doApiAction('', 'get');
	}

	public function cancel()
	{
		return $this->doApiAction('cancel');
	}
}