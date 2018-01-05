<?php
/**
 * ALIPAY API: alipay.mobile.code.create request
 *
 * @author auto create
 * @since 1.0, 2015-10-22 19:26:43
 */
class AlipayMobileCodeCreateRequest
{
	/** 
	 * 业务关联ID。比如订单号,userId，业务连接等
	 **/
	private $bizLinkedId;
	
	/** 
	 * 类似产品名称，根据该值决定码存储类型；新接业务需要找码平台技术配置
	 **/
	private $bizType;
	
	/** 
	 * 业务自定义,码平台不用理解。一定要传json字符串。
	 **/
	private $contextStr;
	
	/** 
	 * 如果是true，则扫一扫下发跳转地址直接取自bizLinkedId
否则，从路由信息里取跳转地址
	 **/
	private $isDirect;
	
	/** 
	 * 备注信息字段
	 **/
	private $memo;
	
	/** 
	 * 发码来源，业务自定
	 **/
	private $sourceId;
	
	/** 
	 * 编码启动时间（yyy-MM-dd hh:mm:ss），为空表示立即启用
	 **/
	private $startDate;
	
	/** 
	 * 超时时间,单位秒；若不传则为永久。发码超时时间需要找码平台技术评估
	 **/
	private $timeout;
	
	/** 
	 * 支付宝用户id
	 **/
	private $userId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setBizLinkedId($bizLinkedId)
	{
		$this->bizLinkedId = $bizLinkedId;
		$this->apiParas["biz_linked_id"] = $bizLinkedId;
	}

	public function getBizLinkedId()
	{
		return $this->bizLinkedId;
	}

	public function setBizType($bizType)
	{
		$this->bizType = $bizType;
		$this->apiParas["biz_type"] = $bizType;
	}

	public function getBizType()
	{
		return $this->bizType;
	}

	public function setContextStr($contextStr)
	{
		$this->contextStr = $contextStr;
		$this->apiParas["context_str"] = $contextStr;
	}

	public function getContextStr()
	{
		return $this->contextStr;
	}

	public function setIsDirect($isDirect)
	{
		$this->isDirect = $isDirect;
		$this->apiParas["is_direct"] = $isDirect;
	}

	public function getIsDirect()
	{
		return $this->isDirect;
	}

	public function setMemo($memo)
	{
		$this->memo = $memo;
		$this->apiParas["memo"] = $memo;
	}

	public function getMemo()
	{
		return $this->memo;
	}

	public function setSourceId($sourceId)
	{
		$this->sourceId = $sourceId;
		$this->apiParas["source_id"] = $sourceId;
	}

	public function getSourceId()
	{
		return $this->sourceId;
	}

	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
		$this->apiParas["start_date"] = $startDate;
	}

	public function getStartDate()
	{
		return $this->startDate;
	}

	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
		$this->apiParas["timeout"] = $timeout;
	}

	public function getTimeout()
	{
		return $this->timeout;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
		$this->apiParas["user_id"] = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getApiMethodName()
	{
		return "alipay.mobile.code.create";
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
