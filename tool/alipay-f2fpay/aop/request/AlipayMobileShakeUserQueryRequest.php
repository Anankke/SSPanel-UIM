<?php
/**
 * ALIPAY API: alipay.mobile.shake.user.query request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:16:13
 */
class AlipayMobileShakeUserQueryRequest
{
	/** 
	 * 动态ID
	 **/
	private $dynamicId;
	
	/** 
	 * 动态ID类型：
wave_code：声波
qr_code：二维码
bar_code：条码
	 **/
	private $dynamicIdType;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setDynamicId($dynamicId)
	{
		$this->dynamicId = $dynamicId;
		$this->apiParas["dynamic_id"] = $dynamicId;
	}

	public function getDynamicId()
	{
		return $this->dynamicId;
	}

	public function setDynamicIdType($dynamicIdType)
	{
		$this->dynamicIdType = $dynamicIdType;
		$this->apiParas["dynamic_id_type"] = $dynamicIdType;
	}

	public function getDynamicIdType()
	{
		return $this->dynamicIdType;
	}

	public function getApiMethodName()
	{
		return "alipay.mobile.shake.user.query";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

     $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
    return $this->needEncrypt;
  }

}
