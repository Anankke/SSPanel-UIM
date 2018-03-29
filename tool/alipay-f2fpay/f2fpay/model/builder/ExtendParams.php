<?php
/**
 * Created by PhpStorm.
 * User: airwalk
 * Date: 16/5/19
 * Time: 下午3:56
 */

class ExtendParams
{
    // 系统商编号
    private $sysServiceProviderId;

    //使用花呗分期要进行的分期数,非必填项
    private $hbFqNum;

    //使用花呗分期需要卖家承担的手续费比例的百分值
    private $hbFqSellerPercent;

    private $extendParamsArr = array();

    public function getExtendParams()
    {
        if(!empty($this->extendParamsArr)) {
            return $this->extendParamsArr;
        }
    }

    public function getSysServiceProviderId()
    {
        return $this->sysServiceProviderId;
    }

    public function setSysServiceProviderId($sysServiceProviderId)
    {
        $this->sysServiceProviderId = $sysServiceProviderId;
        $this->extendParamsArr['sys_service_provider_id'] = $sysServiceProviderId;
    }

    public function getHbFqNum()
    {
        return $this->hbFqNum;
    }

    public function setHbFqNum($hbFqNum)
    {
        $this->hbFqNum = $hbFqNum;
        $this->extendParamsArr['hb_fq_num'] = $hbFqNum;
    }

    public function getHbFqSellerPercent()
    {
        return $this->hbFqSellerPercent;
    }

    public function setHbFqSellerPercent($hbFqSellerPercent)
    {
        $this->hbFqSellerPercent = $hbFqSellerPercent;
        $this->extendParamsArr['hb_fq_seller_percent'] = $hbFqSellerPercent;
    }
}