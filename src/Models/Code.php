<?php

namespace App\Models;

/**
 * Code Model
 */
class Code extends Model
{
    protected $connection = 'default';
    protected $table = 'code';

    public function user()
    {
        return User::where('id', $this->attributes['userid'])->first();
    }
}
