<?php
/**
 * ALIPAY API: alipay.mappprod.account.binding.sync request
 *
 * @author auto create
 * @since 1.0, 2014-09-11 09:38:13
 */
class AlipayMappprodAccountBindingSyncRequest
{
	/** 
	 * 扩展数据，格式：JSON字符串。预留，暂时没用
	 **/
	private $extInfo;
	
	/** 
	 * 用户的商户会员号
	 **/
	private $userAccountNo;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setExtInfo($extInfo)
	{
		$this->extInfo = $extInfo;
		$this->apiParas["ext_info"] = $extInfo;
	}

	public function getExtInfo()
	{
		return $this->extInfo;
	}

	public function setUserAccountNo($userAccountNo)
	{
		$this->userAccountNo = $userAccountNo;
		$this->apiParas["user_account_no"] = $userAccountNo;
	}

	public function getUserAccountNo()
	{
		return $this->userAccountNo;
	}

	public function getApiMethodName()
	{
		return "alipay.mappprod.account.binding.sync";
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
