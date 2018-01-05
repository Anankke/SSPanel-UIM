<?php

class Paymentwall_HttpAction extends Paymentwall_Instance
{
	protected $apiObject;
	protected $apiParams = array();
	protected $apiHeaders = array();
	protected $responseLogInformation = array();

	public function __construct($object, $params = array(), $headers = array())
	{
		$this->setApiObject($object);
		$this->setApiParams($params);
		$this->setApiHeaders($headers);
	}

	public function getApiObject()
	{
		return $this->apiObject;
	}

	public function setApiObject(Paymentwall_ApiObject $apiObject)
	{
		$this->apiObject = $apiObject;
	}

	public function getApiParams()
	{
		return $this->apiParams;
	}

	public function setApiParams($params = array())
	{
		$this->apiParams = $params;
	}

	public function getApiHeaders()
	{
		return $this->apiHeaders;
	}

	public function setApiHeaders($headers = array())
	{
		$this->apiHeaders = $headers;
	}

	public function run()
	{
		$result = null;

		if ($this->getApiObject() instanceof Paymentwall_ApiObject) {
			$result = $this->apiObjectPostRequest($this->getApiObject());
		}

		return $result;
	}

	public function apiObjectPostRequest(Paymentwall_ApiObject $object)
	{
		return $this->request('POST', $object->getApiUrl(), $this->getApiParams(), $this->getApiHeaders());
	}

	public function post($url = '')
	{
		return $this->request('POST', $url, $this->getApiParams(), $this->getApiHeaders());
	}

	public function get($url = '')
	{
		return $this->request('GET', $url, $this->getApiParams(), $this->getApiHeaders());
	}

	protected function request($httpVerb = '', $url = '', $params = array(), $customHeaders = array())
	{
		$curl = curl_init();

		$headers = array(
			$this->getLibraryDefaultRequestHeader()
		);

		if (!empty($customHeaders)) {
			$headers = array_merge($headers, $customHeaders);
		}

		if (!empty($params)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		}

        // CURL_SSLVERSION_TLSv1_2 is defined in libcurl version 7.34 or later
        // but unless PHP has been compiled with the correct libcurl headers it
        // won't be defined in your PHP instance.  PHP > 5.5.19 or > 5.6.3
        if (! defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }

		curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $httpVerb);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HEADER, true);

		$response = curl_exec($curl);

		$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $headerSize);
		$body = substr($response, $headerSize);

		$this->responseLogInformation = array(
			'header' => $header,
			'body' => $body,
			'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE)
		);
		
		curl_close($curl);

		return $this->prepareResponse($body);
	}

	protected function getLibraryDefaultRequestHeader()
	{
		return 'User-Agent: Paymentwall PHP Library v. ' . $this->getConfig()->getVersion();
	}

	protected function prepareResponse($string = '')
	{
		return preg_replace('/\x{FEFF}/u', '', $string);
	}

	public function getResponseLogInformation() {
		return $this->responseLogInformation;
	}
}