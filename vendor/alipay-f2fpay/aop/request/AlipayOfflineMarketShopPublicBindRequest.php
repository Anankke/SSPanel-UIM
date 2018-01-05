<?php
/**
 * ALIPAY API: alipay.offline.market.shop.public.bind request
 *
 * @author auto create
 * @since 1.0, 2016-02-18 20:22:23
 */
class AlipayOfflineMarketShopPublicBindRequest
{
	/** 
	 * 是否绑定所有门店，T表示绑定所有门店，F表示绑定指定shop_ids的门店
	 **/
	private $isAll;
	
	/** 
	 * 门店ID列表，一次最多绑定500个门店，is_all为T时表示绑定本商家下所有门店，即门店列表无需通过本参数shop_ids传入，由系统自动查询;is_all为F时该参数为必填
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
		return "alipay.offline.market.shop.public.bind";
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
