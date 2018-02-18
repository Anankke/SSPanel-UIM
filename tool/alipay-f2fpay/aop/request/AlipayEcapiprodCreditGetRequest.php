<?php
/**
 * ALIPAY API: alipay.ecapiprod.credit.get request
 *
 * @author auto create
 * @since 1.0, 2015-04-02 16:44:25
 */
class AlipayEcapiprodCreditGetRequest
{
	/** 
	 * 授信编号
	 **/
	private $creditNo;
	
	/** 
	 * 身份证号码
	 **/
	private $entityCode;
	
	/** 
	 * 客户的姓名
	 **/
	private $entityName;
	
	/** 
	 * 每一个对接融资平台的系统提供商都有一个机构号
	 **/
	private $isvCode;
	
	/** 
	 * 融资平台分配给小贷公司的机构编码
	 **/
	private $orgCode;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setCreditNo($creditNo)
	{
		$this->creditNo = $creditNo;
		$this->apiParas["credit_no"] = $creditNo;
	}

	public function getCreditNo()
	{
		return $this->creditNo;
	}

	public function setEntityCode($entityCode)
	{
		$this->entityCode = $entityCode;
		$this->apiParas["entity_code"] = $entityCode;
	}

	public function getEntityCode()
	{
		return $this->entityCode;
	}

	public function setEntityName($entityName)
	{
		$this->entityName = $entityName;
		$this->apiParas["entity_name"] = $entityName;
	}

	public function getEntityName()
	{
		return $this->entityName;
	}

	public function setIsvCode($isvCode)
	{
		$this->isvCode = $isvCode;
		$this->apiParas["isv_code"] = $isvCode;
	}

	public function getIsvCode()
	{
		return $this->isvCode;
	}

	public function setOrgCode($orgCode)
	{
		$this->orgCode = $orgCode;
		$this->apiParas["org_code"] = $orgCode;
	}

	public function getOrgCode()
	{
		return $this->orgCode;
	}

	public function getApiMethodName()
	{
		return "alipay.ecapiprod.credit.get";
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
