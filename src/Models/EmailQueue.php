<?php
namespace App\Models;

class EmailQueue extends Model
{
    protected $connection = 'default';
    protected $table = 'email_queue';
}
