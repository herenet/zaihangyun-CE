<?php

namespace App\Libs;
use Illuminate\Support\Str;

class Helpers
{
    // 生成应用ID，32位字符串
    public static function generateAppKey()
    {
        return Str::random(16);
    }

    // 生成应用密钥，64位字符串
    public static function generateAppSecret()
    {
        return Str::random(64);
     }

    //生成用户ID，8位数字
    public static function generateUserId()
    {
        return hexdec(substr(md5(Str::uuid()), 0, 8));
    }

    //生成产品ID，8位数字
    public static function generateProductId()
    {
        return hexdec(substr(md5(Str::uuid()), 0, 6));
    }
}
