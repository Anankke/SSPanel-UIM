<?php
/**
 * ALIPAY API: alipay.zdatafront.common.query request
 *
 * @author auto create
 * @since 1.0, 2016-03-03 17:48:49
 */
class AlipayZdatafrontCommonQueryRequest
{
	/** 
	 * 如果cacheInterval<=0,就直接从外部获取数据；
如果cacheInterval>0,就先判断cache中的数据是否过期，如果没有过期就返回cache中的数据，如果过期再从外部获取数据并刷新cache，然后返回数据。
单位：秒
	 **/
	private $cacheInterval;
	
	/** 
	 * 通用查询的入参
	 **/
	private $queryConditions;
	
	/** 
	 * 服务名称请与相关开发负责人联系
	 **/
	private $serviceName;
	
	/** 
	 * 访问该服务的业务
	 **/
	private $visitBiz;
	
	/** 
	 * 访问该服务的业务线
	 **/
	private $visitBizLine;
	
	/** 
	 * 访问该服务的部门名称
	 **/
	private $visitDomain;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setCacheInterval($cacheInterval)
	{
		$this->cacheInterval = $cacheInterval;
		$this->apiParas["cache_interval"] = $cacheInterval;
	}

	public function getCacheInterval()
	{
		return $this->cacheInterval;
	}

	public function setQueryConditions($queryConditions)
	{
		$this->queryConditions = $queryConditions;
		$this->apiParas["query_conditions"] = $queryConditions;
	}

	public function getQueryConditions()
	{
		return $this->queryConditions;
	}

	public function setServiceName($serviceName)
	{
		$this->serviceName = $serviceName;
		$this->apiParas["service_name"] = $serviceName;
	}

	public function getServiceName()
	{
		return $this->serviceName;
	}

	public function setVisitBiz($visitBiz)
	{
		$this->visitBiz = $visitBiz;
		$this->apiParas["visit_biz"] = $visitBiz;
	}

	public function getVisitBiz()
	{
		return $this->visitBiz;
	}

	public function setVisitBizLine($visitBizLine)
	{
		$this->visitBizLine = $visitBizLine;
		$this->apiParas["visit_biz_line"] = $visitBizLine;
	}

	public function getVisitBizLine()
	{
		return $this->visitBizLine;
	}

	public function setVisitDomain($visitDomain)
	{
		$this->visitDomain = $visitDomain;
		$this->apiParas["visit_domain"] = $visitDomain;
	}

	public function getVisitDomain()
	{
		return $this->visitDomain;
	}

	public function getApiMethodName()
	{
		return "alipay.zdatafront.common.query";
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
