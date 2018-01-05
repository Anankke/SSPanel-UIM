<?php

abstract class Paymentwall_ApiObject extends Paymentwall_Instance
{
	const API_BRICK_SUBPATH			= 'brick';
	const API_OBJECT_CHARGE 		= 'charge';
	const API_OBJECT_SUBSCRIPTION 	= 'subscription';
	const API_OBJECT_ONE_TIME_TOKEN = 'token';

	protected $properties = array();
	protected $_id;
	protected $_rawResponse = '';
	protected $_responseLogInformation = array();
	protected $brickSubEndpoints = array(
		self::API_OBJECT_CHARGE, self::API_OBJECT_SUBSCRIPTION, self::API_OBJECT_ONE_TIME_TOKEN
	);

	abstract function getEndpointName();

	public function __construct($id = '')
	{
		if (!empty($id)) {
			$this->_id = $id;
		}
	}

	public final function create($params = array())
	{
		$httpAction = new Paymentwall_HttpAction($this, $params, array(
			$this->getApiBaseHeader()
		));
		$this->setPropertiesFromResponse($httpAction->run());
		return $this;
	}

	public function __get($property)
	{
		return isset($this->properties[$property]) ? $this->properties[$property] : null;
	}

	public function getApiUrl()
	{
		if ($this->getEndpointName() === self::API_OBJECT_ONE_TIME_TOKEN && !$this->getConfig()->isTest()) {
			return Paymentwall_OneTimeToken::GATEWAY_TOKENIZATION_URL;
		} else {
			return $this->getApiBaseUrl() . $this->getSubPath() . '/' . $this->getEndpointName();
		}
	}

	/**
	 * Returns raw data about the response that can be presented to the end-user: 
	 * 	success => 0 or 1
	 *	error => 
	 *		message 	- human-readable error message
	 *		code 		- error code, see https://www.paymentwall.com/us/documentation/Brick/2968#error
	 * 	secure => 
	 *		formHTML 	- needed to complete 3D Secure step, HTML of the form to be submitted to the user to redirect him to the bank page
	 *
	 * @return array 
	 *				
	 */
	public function _getPublicData()
	{
		/*$responseModel = Paymentwall_Response_Factory::get($this->getPropertiesFromResponse());
		return $responseModel instanceof Paymentwall_Response_Interface ? $responseModel->process() : '';*/

		/**
		 * @todo encapsulate this into Paymentwall_Response_Factory better; right now it returns success=1 for 3ds case
		 */
		$response = $this->getPropertiesFromResponse();
		$result = array();
		if (isset($response['type']) && $response['type'] == 'Error') {
			$result = array(
				'success' => 0,
				'error' => array(
					'message' => $response['error'],
					'code' => $response['code']
				)
			);
		}
		elseif (!empty($response['secure'])) {
			$result = array(
				'success' => 0,
				'secure' => $response['secure']
			);
		}
		elseif ($this->isSuccessful()) {
			$result['success'] = 1;
		}
		else {
			$result = array(
				'success' => 0,
				'error' => array(
					'message' => 'Internal error'
				)
			);
		}
		return $result;
	}

	/**
	 * @return string json encoded result of ApiObject::getPublicData()
	 */
	public function getPublicData() 
	{
		return json_encode($this->_getPublicData());
	}

	public function getProperties() {
		return $this->properties;
	}

	public function getRawResponseData()
	{
		return $this->_rawResponse;
	}

	protected function setPropertiesFromResponse($response = '')
	{
		if (!empty($response)) {
			$this->_rawResponse = $response;
			$this->properties = (array) $this->preparePropertiesFromResponse($response);
		} else {
			throw new Exception('Empty response');
		}
	}

	protected function getSubPath()
	{
		return (in_array($this->getEndpointName(), $this->brickSubEndpoints))
				? '/' . self::API_BRICK_SUBPATH
				: '';
	}

	protected function getPropertiesFromResponse()
	{
		return $this->properties;
	}

	protected function preparePropertiesFromResponse($string = '')
	{
		return json_decode($string, false);
	}

	protected function getApiBaseHeader()
	{
		return 'X-ApiKey: ' . $this->getPrivateKey();
	}

	protected function doApiAction($action = '', $method = 'post')
	{
		$actionUrl = $this->getApiUrl() . '/' . $this->_id . '/' . $action;
		$httpAction = new Paymentwall_HttpAction($this, array('id' => $this->_id), array(
			$this->getApiBaseHeader()
		));
		$this->_responseLogInformation = $httpAction->getResponseLogInformation();
		$this->setPropertiesFromResponse(
			$method == 'get' ? $httpAction->get($actionUrl) : $httpAction->post($actionUrl)
		);

		return $this;
	}

	public function getResponseLogInformation()
	{
		return $this->_responseLogInformation;
	}


}