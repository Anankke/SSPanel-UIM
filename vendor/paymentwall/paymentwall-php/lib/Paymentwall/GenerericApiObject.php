<?php

class Paymentwall_GenerericApiObject extends Paymentwall_ApiObject
{
	/**
	 * API type
	 *
	 * @var string
	 */
	protected $api;

	/**
	 * Paymentwall_HttpAction object
	 *
	 * @var \Paymentwall_HttpAction
	 */
	protected $httpAction;

	/**
	 * @see \Paymentwall_ApiObject
	 */
	public function getEndpointName()
	{
		return $this->api;
	}

	public function __construct($type)
	{
		$this->api = $type;
		$this->httpAction = new Paymentwall_HttpAction($this);
	}

	/**
	 * Make post request
	 *
	 * @param array $params
	 * @param array $headers
	 *
	 * @return array
	 */
	public function post($params = array(), $headers = array())
	{
		if (empty($params)) {
			return null;
		}

		$this->httpAction->setApiParams($params);

		$this->httpAction->setApiHeaders(array_merge(array($this->getApiBaseHeader()), $headers));

		return (array) $this->preparePropertiesFromResponse(
			$this->httpAction->post(
				$this->getApiUrl()
			)
		);
	}
}