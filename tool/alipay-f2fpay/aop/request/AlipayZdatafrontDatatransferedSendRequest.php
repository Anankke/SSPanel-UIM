<?php
/**
 * ALIPAY API: alipay.zdatafront.datatransfered.send request
 *
 * @author auto create
 * @since 1.0, 2015-02-10 09:58:54
 */
class AlipayZdatafrontDatatransferedSendRequest
{
	/** 
	 * 数据字段，identity对应的其他数据字段。使用json格式组织，且仅支持字符串类型，其他类型请转为字符串。
	 **/
	private $data;
	
	/** 
	 * 合作伙伴的主键数据，同一合作伙伴要保证该字段唯一，若出现重复，后入数据会覆盖先入数据。使用json格式组织，且仅支持字符串类型，其他类型请转为字符串。
	 **/
	private $identity;
	
	/** 
	 * 合作伙伴标识字段，用来区分数据来源。建议使用公司域名或公司名。
	 **/
	private $typeId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setData($data)
	{
		$this->data = $data;
		$this->apiParas["data"] = $data;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setIdentity($identity)
	{
		$this->identity = $identity;
		$this->apiParas["identity"] = $identity;
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function setTypeId($typeId)
	{
		$this->typeId = $typeId;
		$this->apiParas["type_id"] = $typeId;
	}

	public function getTypeId()
	{
		return $this->typeId;
	}

	public function getApiMethodName()
	{
		return "alipay.zdatafront.datatransfered.send";
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
