<?php

namespace App\Models;

/**
 * Node Model
 */

use App\Utils\Tools;

class Speedtest extends Model
{
    protected $connection = "default";
    protected $table = "speedtest";
    
    public function node()
    {
        return Node::find($this->attributes['nodeid']);
    }

    
    public function getTelecomPing()
    {
        $load = $this->attributes['telecomping'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
    
    public function getUnicomPing()
    {
        $load = $this->attributes['unicomping'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
    
    public function getCmccPing()
    {
        $load = $this->attributes['cmccping'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
    

    public function getTelecomUpload()
    {
        $load = $this->attributes['telecomeupload'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
    
    public function getTelecomDownload()
    {
        $load = $this->attributes['telecomedownload'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
    
    public function getUnicomUpload()
    {
        $load = $this->attributes['unicomupload'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
    
    public function getUnicomDownload()
    {
        $load = $this->attributes['unicomdownload'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
    
    public function getCmccDownload()
    {
        $load = $this->attributes['cmccdownload'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
    
    public function getCmccUpload()
    {
        $load = $this->attributes['cmccupload'];
        $exp = explode(" ", $load);
        return $exp[0];
    }
}
