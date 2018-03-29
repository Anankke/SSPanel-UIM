<?php
/**
 * ALIPAY API: alipay.mobile.deviceinfo.dynamicinfo.add request
 *
 * @author auto create
 * @since 1.0, 2015-06-18 14:48:53
 */
class AlipayMobileDeviceinfoDynamicinfoAddRequest
{
	/** 
	 * 蚂蚁金服集团生成的设备id
	 **/
	private $apdid;
	
	/** 
	 * 客户端采集的设备动态信息，格式为json串
	 **/
	private $dynamicinfo;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setApdid($apdid)
	{
		$this->apdid = $apdid;
		$this->apiParas["apdid"] = $apdid;
	}

	public function getApdid()
	{
		return $this->apdid;
	}

	public function setDynamicinfo($dynamicinfo)
	{
		$this->dynamicinfo = $dynamicinfo;
		$this->apiParas["dynamicinfo"] = $dynamicinfo;
	}

	public function getDynamicinfo()
	{
		return $this->dynamicinfo;
	}

	public function getApiMethodName()
	{
		return "alipay.mobile.deviceinfo.dynamicinfo.add";
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
