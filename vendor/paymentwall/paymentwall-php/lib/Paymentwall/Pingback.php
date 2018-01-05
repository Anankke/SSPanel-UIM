<?php

class Paymentwall_Pingback extends Paymentwall_Instance
{
	const PINGBACK_TYPE_REGULAR = 0;
	const PINGBACK_TYPE_GOODWILL = 1;
	const PINGBACK_TYPE_NEGATIVE = 2;

	const PINGBACK_TYPE_RISK_UNDER_REVIEW = 200;
	const PINGBACK_TYPE_RISK_REVIEWED_ACCEPTED = 201;
	const PINGBACK_TYPE_RISK_REVIEWED_DECLINED = 202;

	const PINGBACK_TYPE_SUBSCRIPTION_CANCELLATION = 12;
	const PINGBACK_TYPE_SUBSCRIPTION_EXPIRED = 13;
	const PINGBACK_TYPE_SUBSCRIPTION_PAYMENT_FAILED = 14;

	protected $parameters;
	protected $ipAddress;

	public function __construct(array $parameters, $ipAddress)
	{
		$this->parameters = $parameters;
		$this->ipAddress = $ipAddress;
	}

	public function validate($skipIpWhitelistCheck = false)
	{
		$validated = false;

		if ($this->isParametersValid()) {

			if ($this->isIpAddressValid() || $skipIpWhitelistCheck) {

				if ($this->isSignatureValid()) {

					$validated = true;

				} else {
					$this->appendToErrors('Wrong signature');
				}

			} else {
				$this->appendToErrors('IP address is not whitelisted');
			}

		} else {
			$this->appendToErrors('Missing parameters');
		}

		return $validated;
	}

	public function isSignatureValid()
	{
		$signatureParamsToSign = array();

		if ($this->getApiType() == Paymentwall_Config::API_VC) {

			$signatureParams = array('uid', 'currency', 'type', 'ref');

		} else if ($this->getApiType() == Paymentwall_Config::API_GOODS) {

			$signatureParams = array('uid', 'goodsid', 'slength', 'speriod', 'type', 'ref');

		} else { // API_CART

			$signatureParams = array('uid', 'goodsid', 'type', 'ref');

			$this->parameters['sign_version'] = Paymentwall_Signature_Abstract::VERSION_TWO;

		}

		if (empty($this->parameters['sign_version']) || $this->parameters['sign_version'] == Paymentwall_Signature_Abstract::VERSION_ONE) {

			foreach ($signatureParams as $field) {
				$signatureParamsToSign[$field] = isset($this->parameters[$field]) ? $this->parameters[$field] : null;
			}

			$this->parameters['sign_version'] = Paymentwall_Signature_Abstract::VERSION_ONE;

		} else {
			$signatureParamsToSign = $this->parameters;
		}

		$pingbackSignatureModel = new Paymentwall_Signature_Pingback();
		$signatureCalculated = $pingbackSignatureModel->calculate(
			$signatureParamsToSign,
			$this->parameters['sign_version']
		);

		$signature = isset($this->parameters['sig']) ? $this->parameters['sig'] : null;

		return $signature == $signatureCalculated;
	}

	public function isIpAddressValid()
	{
		$ipsWhitelist = array(
			'174.36.92.186',
			'174.36.96.66',
			'174.36.92.187',
			'174.36.92.192',
			'174.37.14.28'
		);

		return in_array($this->ipAddress, $ipsWhitelist);
	}

	public function isParametersValid()
	{
		$errorsNumber = 0;

		if ($this->getApiType() == Paymentwall_Config::API_VC) {
			$requiredParams = array('uid', 'currency', 'type', 'ref', 'sig');
		} else if ($this->getApiType() == Paymentwall_Config::API_GOODS) {
			$requiredParams = array('uid', 'goodsid', 'type', 'ref', 'sig');
		} else { // Cart API
			$requiredParams = array('uid', 'goodsid', 'type', 'ref', 'sig');
		}

		foreach ($requiredParams as $field) {
			if (!isset($this->parameters[$field]) || $this->parameters[$field] === '') {
				$this->appendToErrors('Parameter ' . $field . ' is missing');
				$errorsNumber++;
			}
		}

		return $errorsNumber == 0;
	}

	public function getParameter($param)
	{
		return isset($this->parameters[$param]) ? $this->parameters[$param] : null;
	}

	public function getType()
	{
		return isset($this->parameters['type']) ? intval($this->parameters['type']) : null;
	}

	public function getTypeVerbal() {
		$typeVerbal = '';
		$pingbackTypes = array(
			self::PINGBACK_TYPE_SUBSCRIPTION_CANCELLATION => 'user_subscription_cancellation',
			self::PINGBACK_TYPE_SUBSCRIPTION_EXPIRED => 'user_subscription_expired',
			self::PINGBACK_TYPE_SUBSCRIPTION_PAYMENT_FAILED => 'user_subscription_payment_failed'
		);

		if (!empty($this->parameters['type'])) {
			if (array_key_exists($this->parameters['type'], $pingbackTypes)) {
				$typeVerbal = $pingbackTypes[$this->parameters['type']];
			}
		}

		return $typeVerbal;
	}

	public function getUserId()
	{
		return $this->getParameter('uid');
	}

	public function getVirtualCurrencyAmount()
	{
		return $this->getParameter('currency');
	}

	public function getProductId()
	{
		return $this->getParameter('goodsid');
	}

	public function getProductPeriodLength()
	{
		return $this->getParameter('slength');
	}

	public function getProductPeriodType()
	{
		return $this->getParameter('speriod');
	}

	public function getProduct() {
		return new Paymentwall_Product(
			$this->getProductId(),
			0,
			null,
			null,
			$this->getProductPeriodLength() > 0 ? Paymentwall_Product::TYPE_SUBSCRIPTION : Paymentwall_Product::TYPE_FIXED,
			$this->getProductPeriodLength(),
			$this->getProductPeriodType()
		);
	}

	public function getProducts() {
		$result = array();
		$productIds = $this->getParameter('goodsid');

		if (!empty($productIds) && is_array($productIds)) {
			foreach ($productIds as $Id) {
				$result[] = new Paymentwall_Product($Id);
			}
		}

		return $result;
	}

	public function getReferenceId()
	{
		return $this->getParameter('ref');
	}

	public function getPingbackUniqueId()
	{
		return $this->getReferenceId() . '_' . $this->getType();
	}

	public function isDeliverable()
	{
		return (
			$this->getType() === self::PINGBACK_TYPE_REGULAR ||
			$this->getType() === self::PINGBACK_TYPE_GOODWILL ||
			$this->getType() === self::PINGBACK_TYPE_RISK_REVIEWED_ACCEPTED
		);
	}

	public function isCancelable()
	{
		return (
			$this->getType() === self::PINGBACK_TYPE_NEGATIVE
			|| $this->getType() === self::PINGBACK_TYPE_RISK_REVIEWED_DECLINED
		);
	}

	public function isUnderReview() {
		return $this->getType() === self::PINGBACK_TYPE_RISK_UNDER_REVIEW;
	}
}