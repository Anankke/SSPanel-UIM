<?php
/**
 * ALIPAY API: alipay.trust.user.zminfo.pair.get request
 *
 * @author auto create
 * @since 1.0, 2015-02-06 13:09:24
 */
class AlipayTrustUserZminfoPairGetRequest
{
	/** 
	 * 描述申请者的用户信息JSON串，身份证号，姓名等
	 **/
	private $applyUserInfo;
	
	/** 
	 * 被申请人的用户信息JSON串
	 **/
	private $ownerUserInfo;
	
	/** 
	 * 请求的芝麻信用信息类型，目前仅支持芝麻分
	 **/
	private $zmInfoType;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setApplyUserInfo($applyUserInfo)
	{
		$this->applyUserInfo = $applyUserInfo;
		$this->apiParas["apply_user_info"] = $applyUserInfo;
	}

	public function getApplyUserInfo()
	{
		return $this->applyUserInfo;
	}

	public function setOwnerUserInfo($ownerUserInfo)
	{
		$this->ownerUserInfo = $ownerUserInfo;
		$this->apiParas["owner_user_info"] = $ownerUserInfo;
	}

	public function getOwnerUserInfo()
	{
		return $this->ownerUserInfo;
	}

	public function setZmInfoType($zmInfoType)
	{
		$this->zmInfoType = $zmInfoType;
		$this->apiParas["zm_info_type"] = $zmInfoType;
	}

	public function getZmInfoType()
	{
		return $this->zmInfoType;
	}

	public function getApiMethodName()
	{
		return "alipay.trust.user.zminfo.pair.get";
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
