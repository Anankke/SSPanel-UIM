<?php

namespace App\Utils;

use App\Models\User;
use App\Models\WecenterUser;
use App\Services\Config;
use App\Utils;

class Wecenter
{

    /**
     * 添加或者更新密码信息
     */
    public static function Add($user, $pwd)
    {
        if (Config::get('enable_wecenter')=='true') {
            $email=$user->email;
            $exists=WecenterUser::where("email", $email)->first();
            
            if ($exists==null) {
                $exists=new WecenterUser();
                $exists->password=md5(md5($pwd).Config::get('salt'));
                $exists->user_name=$user->user_name;
                $exists->email=$email;
                $exists->salt=Config::get('salt');
                $exists->group_id=5;
                $exists->save();
            } else {
                $exists->password=md5(md5($pwd).Config::get('salt'));
                $exists->salt=Config::get('salt');
                $exists->save();
            }
        }
    }
    
    
    public static function Delete($email)
    {
        if (Config::get('enable_wecenter')=='true') {
            WecenterUser::where("email", $email)->delete();
        }
    }
    
    public static function ChangeUserName($email1, $email2, $pwd, $username)
    {
        if (Config::get('enable_wecenter')=='true') {
            $exists=WecenterUser::where("email", $email1)->first();
            
            if ($exists!=null) {
                $exists->password=md5(md5($pwd).Config::get('salt'));
                $exists->user_name=$username;
                $exists->email=$email2;
                $exists->salt=Config::get('salt');
                $exists->save();
            } else {
                $exists=new WecenterUser();
                $exists->password=md5(md5($pwd).Config::get('salt'));
                $exists->user_name=$username;
                $exists->email=$email2;
                $exists->salt=Config::get('salt');
                $exists->group_id=5;
                $exists->save();
            }
        }
    }
    
    public static function Login($user, $pwd, $time)
    {
        if (Config::get('enable_wecenter')=='true') {
            $email=$user->email;
            $exists=WecenterUser::where("email", $email)->first();
            
            $expire_in = $time+time();
            
            Utils\Cookie::setwithdomain([Config::get('wecenter_cookie_prefix')."_user_login"=>Wecenter::encode_hash(json_encode(array(
                                'uid' => $exists->uid,
                                'user_name' => $user->email,
                                'password' => md5(md5($pwd).Config::get('salt'))
                            )), md5(Config::get('wecenter_cookie_key') . $_SERVER['HTTP_USER_AGENT']))], $expire_in, Config::get('wecenter_system_main_domain'));
        }
    }
    
    
    public static function Loginout()
    {
        if (Config::get('enable_wecenter')=='true') {
            Utils\Cookie::setwithdomain([Config::get('wecenter_cookie_prefix')."_user_login"=>"loginout"], time()-86400, Config::get('wecenter_system_main_domain'));
        }
    }
    
    public static function encode_hash($hash_data, $hash_key = null)
    {
        $mcrypt = mcrypt_module_open(Wecenter::get_algorithms(), '', MCRYPT_MODE_ECB, '');

        mcrypt_generic_init($mcrypt, Wecenter::get_key($mcrypt, $hash_key), mcrypt_create_iv(mcrypt_enc_get_iv_size($mcrypt), MCRYPT_RAND));

        $result = mcrypt_generic($mcrypt, gzcompress($hash_data));

        mcrypt_generic_deinit($mcrypt);
        mcrypt_module_close($mcrypt);

        return Wecenter::get_algorithms() . '|' . Wecenter::str_to_hex($result);
    }
    
    
    private static function get_key($mcrypt, $key = null)
    {
        if (!$key) {
            $key = Config::get('wecenter_cookie_key');
        }

        return substr($key, 0, mcrypt_enc_get_key_size($mcrypt));
    }

    private static function get_algorithms()
    {
        $algorithms = mcrypt_list_algorithms();

        foreach ($algorithms as $algorithm) {
            if (strstr($algorithm, '-256')) {
                return $algorithm;
            }
        }

        foreach ($algorithms as $algorithm) {
            if (strstr($algorithm, '-128')) {
                return $algorithm;
            }
        }

        return end($algorithms);
    }

    private static function str_to_hex($string)
    {
        $hex = null;
        for ($i = 0; $i < strlen($string); $i++) {
            $ord = ord($string[$i]);
            $hexCode = dechex($ord);
            $hex .= substr('0' . $hexCode, -2);
        }

        return strtoupper($hex);
    }

    private static function hex_to_str($hex)
    {
        $string = null;
        for ($i = 0; $i < strlen($hex)-1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return $string;
    }
}
