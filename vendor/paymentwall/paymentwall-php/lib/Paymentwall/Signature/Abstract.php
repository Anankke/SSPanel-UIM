<?php

abstract class Paymentwall_Signature_Abstract extends Paymentwall_Instance
{
	const VERSION_ONE = 1;
	const VERSION_TWO = 2;
	const VERSION_THREE	= 3;
	const DEFAULT_VERSION = 3;

	abstract function process($params = array(), $version = 0);

	abstract function prepareParams($params = array(), $baseString = '');

	public final function calculate($params = array(), $version = 0)
	{
		return $this->process($params, $version);
	}

	protected function ksortMultiDimensional(&$params = array())
	{
		if (is_array($params)) {
			ksort($params);
			foreach ($params as &$p) {
				if (is_array($p)) {
					ksort($p);
				}
			}
		}
	}
}