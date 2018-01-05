<?php

class Paymentwall_Response_Error extends Paymentwall_Response_Abstract implements Paymentwall_Response_Interface
{

	public function process()
	{
		if (!isset($this->response)) {
			return $this->wrapInternalError();
		}

		$response = array(
			'success' => 0,
			'error' => $this->getErrorMessageAndCode($this->response)
		);

		return json_encode($response);
	}

	public function getErrorMessageAndCode($response)
	{
		return array(
			'message' => $response['error'],
			'code' => $response['code']
		);
	}
}