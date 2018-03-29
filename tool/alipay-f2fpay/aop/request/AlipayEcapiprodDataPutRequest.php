<?php
/**
 * ALIPAY API: alipay.ecapiprod.data.put request
 *
 * @author auto create
 * @since 1.0, 2015-04-02 16:45:23
 */
class AlipayEcapiprodDataPutRequest
{
	/** 
	 * 数据类型
	 **/
	private $category;
	
	/** 
	 * 数据字符编码，默认UTF-8
	 **/
	private $charSet;
	
	/** 
	 * 数据采集平台生成的采集任务编号
	 **/
	private $collectingTaskId;
	
	/** 
	 * 身份证，工商注册号等
	 **/
	private $entityCode;
	
	/** 
	 * 姓名或公司名等，name和code不能同时为空
	 **/
	private $entityName;
	
	/** 
	 * 人或公司等
	 **/
	private $entityType;
	
	/** 
	 * 渠道商
	 **/
	private $isvCode;
	
	/** 
	 * 数据主体,以json格式传输的数据
	 **/
	private $jsonData;
	
	/** 
	 * 数据合作方
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

	
	public function setCategory($category)
	{
		$this->category = $category;
		$this->apiParas["category"] = $category;
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function setCharSet($charSet)
	{
		$this->charSet = $charSet;
		$this->apiParas["char_set"] = $charSet;
	}

	public function getCharSet()
	{
		return $this->charSet;
	}

	public function setCollectingTaskId($collectingTaskId)
	{
		$this->collectingTaskId = $collectingTaskId;
		$this->apiParas["collecting_task_id"] = $collectingTaskId;
	}

	public function getCollectingTaskId()
	{
		return $this->collectingTaskId;
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

	public function setEntityType($entityType)
	{
		$this->entityType = $entityType;
		$this->apiParas["entity_type"] = $entityType;
	}

	public function getEntityType()
	{
		return $this->entityType;
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

	public function setJsonData($jsonData)
	{
		$this->jsonData = $jsonData;
		$this->apiParas["json_data"] = $jsonData;
	}

	public function getJsonData()
	{
		return $this->jsonData;
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
		return "alipay.ecapiprod.data.put";
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
