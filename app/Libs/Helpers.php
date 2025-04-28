<?php

namespace App\Libs;
use Jdenticon\Identicon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

    //生成文章ID，16位数字
    public static function generateArticleId()
    {
        return hexdec(substr(md5(Str::uuid()), 0, 9));
    }

    /**
     * 生成并保存用户头像
     * @param string $text 用于生成头像的文本（通常是用户ID或用户名）
     * @param string $path 保存路径（相对public目录）
     * @param int $size 头像尺寸，默认200px
     * @return bool|string 成功返回头像URL，失败返回false
     */
    public static function generateAndSaveAvatar($text, $path, $size = 200)
    {
        try {
            // 创建 Identicon 实例
            $icon = new Identicon();
            
            // 设置参数
            $icon->setValue($text);
            $icon->setSize($size);
            
            // 生成图片数据
            $imageData = $icon->getImageData('png');
            
            // 确保目录存在
            return Storage::disk('SaaSAdmin-mch')->put($path, $imageData);
        } catch (\Exception $e) {
            \Log::error('生成头像失败: ' . $e->getMessage());
            return false;
        }
    }
}
