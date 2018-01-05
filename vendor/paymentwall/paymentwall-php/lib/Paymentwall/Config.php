<?php

class Paymentwall_Config
{
	const VERSION = '2.0.0';

	const API_BASE_URL = 'https://api.paymentwall.com/api';

	const API_VC	= 1;
	const API_GOODS	= 2;
	const API_CART	= 3;

	protected $apiType = self::API_GOODS;
	protected $publicKey;
	protected $privateKey;
	protected $apiBaseUrl = self::API_BASE_URL;

	private static $instance;

	public function getApiBaseUrl()
	{
		return $this->apiBaseUrl;
	}

	public function setApiBaseUrl($url = '')
	{
		$this->apiBaseUrl = $url;
	}

	public function getLocalApiType()
	{
		return $this->apiType;
	}

	public function setLocalApiType($apiType = 0)
	{
		$this->apiType = $apiType;
	}

	public function getPublicKey()
	{
		return $this->publicKey;
	}

	public function setPublicKey($key = '')
	{
		$this->publicKey = $key;
	}

	public function getPrivateKey()
	{
		return $this->privateKey;
	}

	public function setPrivateKey($key = '')
	{
		$this->privateKey = $key;
	}

	public function getVersion()
	{
		return self::VERSION;
	}

	public function isTest()
	{
		return strpos($this->getPublicKey(), 't_') === 0;
	}

	public function set($config = array())
	{
		if (isset($config['api_base_url'])) {
			$this->setApiBaseUrl($config['api_base_url']);
		}
		if (isset($config['api_type'])) {
			$this->setLocalApiType($config['api_type']);
		}
		if (isset($config['public_key'])) {
			$this->setPublicKey($config['public_key']);
		}
		if (isset($config['private_key'])) {
			$this->setPrivateKey($config['private_key']);
		}
	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	protected function __construct()
	{
	}

	private function __clone()
	{
	}
}
