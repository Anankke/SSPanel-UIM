<?php

class Paymentwall_Response_Factory
{
	const CLASS_NAME_PREFIX = 'Paymentwall_Response_';

	const RESPONSE_SUCCESS = 'success';
	const RESPONSE_ERROR = 'error';

	public static function get($response = array())
	{
		$responseModel = null;

		$responseModel = self::getClassName($response);

		return new $responseModel($response);
	}

	public static function getClassName($response = array()) {
		$responseType = (isset($response['type']) && $response['type'] == 'Error') ? self::RESPONSE_ERROR : self::RESPONSE_SUCCESS;
		return self::CLASS_NAME_PREFIX . ucfirst($responseType);
	}
}