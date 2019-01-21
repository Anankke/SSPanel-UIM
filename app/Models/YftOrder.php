<?php
/**
 * Created by 傲慢与偏见.
 * OSUser: D-L
 * Date: 2017/10/22
 * Time: 21:09
 */

namespace App\Models;


class YftOrder extends Model
{
    protected $connection = "default";
    protected $table = "yft_order_info";
    protected $primaryKey = 'id';
}