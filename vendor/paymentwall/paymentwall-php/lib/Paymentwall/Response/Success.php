<?php

class Paymentwall_Response_Success extends Paymentwall_Response_Abstract implements Paymentwall_Response_Interface
{
	public function process()
	{
		if (!isset($this->response)) {
			return $this->wrapInternalError();
		}

		$response = array(
			'success' => 1
		);

		return json_encode($response);
	}
}