<?php

namespace App\Models;

class WecenterUser extends Model
{
    protected $connection = "wecenter";
    protected $table = "aws_users";
    protected $primaryKey = 'uid';
}
