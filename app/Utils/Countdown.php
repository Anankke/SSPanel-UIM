<?php
/**
 * @author Cian topjohncian@gmail.com
 * date 2019-07-08
 */


namespace App\Utils;


/**
 * Class Countdown
 * 倒计时类
 * @package App\Utils
 */
class Countdown
{
    /**
     * 需要倒计时的时间
     * @var
     */
    private $countDate;

    /**
     * Countdown constructor.
     * 传入需要倒计时的时间
     * @param $countDate
     */
    public function __construct($countDate)
    {
        $this->countDate = $countDate;
    }

    /**
     * @return float|string
     * 剩余几天 例：1天
     */
    public function countdown()
    {
        $stampCountDate = strtotime($this->countDate);
        $nowStampDate = time();
        $differDateStamp = $stampCountDate - $nowStampDate;
        $differDateDays = floor($differDateStamp / (24 * 3600));
        if ($differDateDays < 0 || $differDateDays > 315360000000) {
            return '无限期';
        } else {
            return $differDateDays . '天';
        }
    }
}
