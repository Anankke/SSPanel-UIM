<?php

abstract class Paymentwall_Response_Abstract
{
	protected $response;

	public function __construct($response = array())
	{
		$this->response = $response;
	}

	protected function wrapInternalError()
	{
		$response = array(
			'success' => 0,
			'error' => array(
				'message' => 'Internal error'
			)
		);
		return json_encode($response);
	}
}