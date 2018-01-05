<?php
/**
 * ALIPAY API: alipay.ecapiprod.drawndn.lendingrecord.query request
 *
 * @author auto create
 * @since 1.0, 2016-03-29 11:34:36
 */
class AlipayEcapiprodDrawndnLendingrecordQueryRequest
{
	/** 
	 * 支用编号
	 **/
	private $drawndnNo;
	
	/** 
	 * 还款记录的终止时间，终止时间与起始时间的范围不能超过31天
	 **/
	private $end;
	
	/** 
	 * 身份证号码
	 **/
	private $entityCode;
	
	/** 
	 * 客户的姓名
	 **/
	private $entityName;
	
	/** 
	 * 融资平台分配给ISV的编码
	 **/
	private $isvCode;
	
	/** 
	 * 融资平台分配给小贷公司的机构编码
	 **/
	private $orgCode;
	
	/** 
	 * 还款记录的起始时间（距离当前时间不能大于183天，只能在【0-183】之间）
	 **/
	private $start;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setDrawndnNo($drawndnNo)
	{
		$this->drawndnNo = $drawndnNo;
		$this->apiParas["drawndn_no"] = $drawndnNo;
	}

	public function getDrawndnNo()
	{
		return $this->drawndnNo;
	}

	public function setEnd($end)
	{
		$this->end = $end;
		$this->apiParas["end"] = $end;
	}

	public function getEnd()
	{
		return $this->end;
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

	public function setStart($start)
	{
		$this->start = $start;
		$this->apiParas["start"] = $start;
	}

	public function getStart()
	{
		return $this->start;
	}

	public function getApiMethodName()
	{
		return "alipay.ecapiprod.drawndn.lendingrecord.query";
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
