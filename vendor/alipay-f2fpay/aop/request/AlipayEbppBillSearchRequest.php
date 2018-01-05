<?php
/**
 * ALIPAY API: alipay.ebpp.bill.search request
 *
 * @author auto create
 * @since 1.0, 2015-02-13 17:30:09
 */
class AlipayEbppBillSearchRequest
{
	/** 
	 * 账单流水
	 **/
	private $billKey;
	
	/** 
	 * 出账机构
	 **/
	private $chargeInst;
	
	/** 
	 * 销账机构
	 **/
	private $chargeoffInst;
	
	/** 
	 * 销账机构给出账机构分配的id
	 **/
	private $companyId;
	
	/** 
	 * 必须以key value形式定义，转为json为格式：{"key1":"value1","key2":"value2","key3":"value3","key4":"value4"}
 后端会直接转换为MAP对象，转换异常会报参数格式错误
	 **/
	private $extend;
	
	/** 
	 * 业务类型
	 **/
	private $orderType;
	
	/** 
	 * 子业务类型
	 **/
	private $subOrderType;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setBillKey($billKey)
	{
		$this->billKey = $billKey;
		$this->apiParas["bill_key"] = $billKey;
	}

	public function getBillKey()
	{
		return $this->billKey;
	}

	public function setChargeInst($chargeInst)
	{
		$this->chargeInst = $chargeInst;
		$this->apiParas["charge_inst"] = $chargeInst;
	}

	public function getChargeInst()
	{
		return $this->chargeInst;
	}

	public function setChargeoffInst($chargeoffInst)
	{
		$this->chargeoffInst = $chargeoffInst;
		$this->apiParas["chargeoff_inst"] = $chargeoffInst;
	}

	public function getChargeoffInst()
	{
		return $this->chargeoffInst;
	}

	public function setCompanyId($companyId)
	{
		$this->companyId = $companyId;
		$this->apiParas["company_id"] = $companyId;
	}

	public function getCompanyId()
	{
		return $this->companyId;
	}

	public function setExtend($extend)
	{
		$this->extend = $extend;
		$this->apiParas["extend"] = $extend;
	}

	public function getExtend()
	{
		return $this->extend;
	}

	public function setOrderType($orderType)
	{
		$this->orderType = $orderType;
		$this->apiParas["order_type"] = $orderType;
	}

	public function getOrderType()
	{
		return $this->orderType;
	}

	public function setSubOrderType($subOrderType)
	{
		$this->subOrderType = $subOrderType;
		$this->apiParas["sub_order_type"] = $subOrderType;
	}

	public function getSubOrderType()
	{
		return $this->subOrderType;
	}

	public function getApiMethodName()
	{
		return "alipay.ebpp.bill.search";
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
