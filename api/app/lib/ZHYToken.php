<?php

namespace app\lib;

use Illuminate\Support\Str;

class ZHYToken
{
    public static function generateToken($uid, $appkey)
    {
        $current_time = time();
        $str_random = Str::random(10);
        $token_suffix = base64_encode($appkey .'.'. $uid);
        $token_prefix = base64_encode($current_time);
        $token_str = md5($token_suffix . $str_random . $token_prefix);
        $token = $token_prefix .'.'. $token_str .'.'. $token_suffix;
        return $token;
    }
    
    public static function parseToken($token)
    {
        try {
            $token_parts = explode('.', $token);
            $token_suffix = $token_parts[2];
            $token_suffix_decode = base64_decode($token_suffix);
            $token_suffix_parts = explode('.', $token_suffix_decode);
            $appkey = $token_suffix_parts[0];
            $uid = $token_suffix_parts[1];
            return ['appkey' => $appkey, 'uid' => $uid];
        } catch (\Exception $e) {
            return null;
        }
    }
}