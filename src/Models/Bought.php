<?php

namespace App\Models;

/**
 * Bought Model
 *
 * @property-read   int     $id         id
 * @property        int     $userid     User id
 * @property        int     $shopid     Shop id
 * @property        string  $datetime   Bought complete datetime
 * @property        int     $renew      Time to renew this bought
 * @property        string  $coupon     Coupon applied to this bought
 * @property        float   $price      Price after coupon applied
 * @property        bool    $is_notified If this bought is notified for renew
 */
class Bought extends Model
{
    protected $connection = 'default';
    protected $table = 'bought';

    public function renew_date()
    {
        return date('Y-m-d H:i:s', $this->attributes['renew']);
    }

    public function datetime()
    {
        return date('Y-m-d H:i:s', $this->attributes['datetime']);
    }

    public function user()
    {
        $user = User::where('id', $this->attributes['userid'])->first();
        if ($user == null) {
            self::where('id', '=', $this->attributes['id'])->delete();
            return null;
        }

        return $user;
    }

    public function shop()
    {
        return Shop::where('id', $this->attributes['shopid'])->first();
    }
}
