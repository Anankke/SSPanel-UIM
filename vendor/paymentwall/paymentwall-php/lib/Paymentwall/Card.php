<?php

class Paymentwall_Card
{
	protected $fields = array();

	public function __construct($details = array())
	{
		$this->fields = (array) $details;
	}

	public function __get($property)
	{
		return isset($this->fields[$property]) ? $this->fields[$property] : null;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getAlias()
	{
		return $this->last4;
	}

	public function getMonthExpirationDate()
	{
		return $this->exp_month;
	}

	public function getYearExpirationDate()
	{
		return $this->exp_year;
	}
}