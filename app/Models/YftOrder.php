<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/10
 * Time: 16:53
 */

namespace App\Models;


class YftOrder extends Model
{
    protected $connection = "default";
    protected $table = "yft_order_info";
    protected $primaryKey = 'id';
}