<?php

declare(strict_types=1);

namespace App\Models;

use function time;

/**
 * Bought Model
 *
 * @property-read   int     $id         id
 *
 * @property        int     $userid     User id
 * @property        int     $shopid     Shop id
 * @property        string  $datetime   Bought complete datetime
 * @property        int     $renew      Time to renew this bought
 * @property        string  $coupon     Coupon applied to this bought
 * @property        float   $price      Price after coupon applied
 * @property        bool    $is_notified If this bought is notified for renew
 */
final class Bought extends Model
{
    protected $connection = 'default';
    protected $table = 'bought';

    /**
     * 购买用户
     */
    public function user(): ?User
    {
        return User::find($this->userid);
    }

    /**
     * 商品
     */
    public function shop(): ?Shop
    {
        return Shop::find($this->shopid);
    }

    /*
     * 套餐已使用的天数
     */
    public function usedDays(): int
    {
        return (int) ((time() - $this->datetime) / 86400);
    }

    /*
     * 是否有效期内
     */
    public function valid(): bool
    {
        $shop = $this->shop();
        if ($shop->useLoop()) {
            return time() - $shop->resetExp() * 86400 < $this->datetime;
        }
        return false;
    }
}
