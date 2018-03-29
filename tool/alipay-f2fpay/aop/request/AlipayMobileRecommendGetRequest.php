<?php
/**
 * ALIPAY API: alipay.mobile.recommend.get request
 *
 * @author auto create
 * @since 1.0, 2015-03-11 15:19:54
 */
class AlipayMobileRecommendGetRequest
{
	/** 
	 * 请求上下文扩展信息，需要与接口负责人约定。格式为json对象。
	 **/
	private $extInfo;
	
	/** 
	 * 期望获取的最多推荐数量, 默认获取一个推荐内容, 0表示获取所有推荐内容
	 **/
	private $limit;
	
	/** 
	 * 所使用的场景id，请向接口负责人申请
	 **/
	private $sceneId;
	
	/** 
	 * 获取推荐信息的开始位置, 默认从0开始
	 **/
	private $startIdx;
	
	/** 
	 * 用户openid
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

	
	public function setExtInfo($extInfo)
	{
		$this->extInfo = $extInfo;
		$this->apiParas["ext_info"] = $extInfo;
	}

	public function getExtInfo()
	{
		return $this->extInfo;
	}

	public function setLimit($limit)
	{
		$this->limit = $limit;
		$this->apiParas["limit"] = $limit;
	}

	public function getLimit()
	{
		return $this->limit;
	}

	public function setSceneId($sceneId)
	{
		$this->sceneId = $sceneId;
		$this->apiParas["scene_id"] = $sceneId;
	}

	public function getSceneId()
	{
		return $this->sceneId;
	}

	public function setStartIdx($startIdx)
	{
		$this->startIdx = $startIdx;
		$this->apiParas["start_idx"] = $startIdx;
	}

	public function getStartIdx()
	{
		return $this->startIdx;
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
		return "alipay.mobile.recommend.get";
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
