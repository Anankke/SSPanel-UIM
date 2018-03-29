<?php
/**
 * ALIPAY API: alipay.data.bill.downloadurl.get request
 *
 * @author auto create
 * @since 1.0, 2016-04-29 18:05:29
 */
class AlipayDataBillDownloadurlGetRequest
{
	/** 
	 * 账单时间：日账单格式为yyyy-MM-dd,月账单格式为yyyy-MM
	 **/
	private $billDate;
	
	/** 
	 * 账单类型，目前支持的类型由：trade、air、air_b2b；trade指商户通过接口所获取的账单，或商户经开放平台授权后其所属服务商通过接口所获取的账单；air、air_b2b是航旅行业定制的账单，一般商户没有此账单；
	 **/
	private $billType;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setBillDate($billDate)
	{
		$this->billDate = $billDate;
		$this->apiParas["bill_date"] = $billDate;
	}

	public function getBillDate()
	{
		return $this->billDate;
	}

	public function setBillType($billType)
	{
		$this->billType = $billType;
		$this->apiParas["bill_type"] = $billType;
	}

	public function getBillType()
	{
		return $this->billType;
	}

	public function getApiMethodName()
	{
		return "alipay.data.bill.downloadurl.get";
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
