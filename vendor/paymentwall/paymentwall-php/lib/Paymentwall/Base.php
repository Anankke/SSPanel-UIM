<?php
/**
 * Class Paymentwall_Base
 * @deprecated
 */
class Paymentwall_Base extends Paymentwall_Config
{
	public static function setApiType($apiType = 0)
	{
		return self::getInstance()->setLocalApiType($apiType);
	}

	public static function setAppKey($appKey = '')
	{
		return self::getInstance()->setPublicKey($appKey);
	}

	public static function setSecretKey($secretKey = '')
	{
		return self::getInstance()->setPrivateKey($secretKey);
	}
}