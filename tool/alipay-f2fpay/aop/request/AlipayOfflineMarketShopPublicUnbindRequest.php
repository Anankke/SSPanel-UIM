<?php
/**
 * ALIPAY API: alipay.offline.market.shop.public.unbind request
 *
 * @author auto create
 * @since 1.0, 2016-02-18 20:01:14
 */
class AlipayOfflineMarketShopPublicUnbindRequest
{
	/** 
	 * 是否解绑所有门店，T表示解绑所有门店，F表示解绑指定shop_ids的门店列表
	 **/
	private $isAll;
	
	/** 
	 * 解除绑定门店的ID列表，一次最多解绑100个门店，is_all为T时表示解除绑定本商家下所有门店，即门店列表无需通过本参数shop_ids传入，由系统自动查询;is_all为F时该参数必填
	 **/
	private $shopIds;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setIsAll($isAll)
	{
		$this->isAll = $isAll;
		$this->apiParas["is_all"] = $isAll;
	}

	public function getIsAll()
	{
		return $this->isAll;
	}

	public function setShopIds($shopIds)
	{
		$this->shopIds = $shopIds;
		$this->apiParas["shop_ids"] = $shopIds;
	}

	public function getShopIds()
	{
		return $this->shopIds;
	}

	public function getApiMethodName()
	{
		return "alipay.offline.market.shop.public.unbind";
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
