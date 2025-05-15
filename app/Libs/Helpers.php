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
        // 获取当前微秒时间戳，乘以1000并取整
        $timestamp = (int)(microtime(true) * 1000);
        
        // 取时间戳的后7位
        $timestampPart = substr($timestamp, -7);
        
        // 生成1位随机数(1-9)作为首位
        $firstDigit = mt_rand(1, 9);
        
        // 组合成8位ID
        $id = $firstDigit . $timestampPart;
        
        return $id;
    }

    //生成文章ID，9位数字
    public static function generateArticleId()
    {
        // 获取当前微秒时间戳，乘以1000并取整
        $timestamp = (int)(microtime(true) * 1000);
        
        // 取时间戳的后7位
        $timestampPart = substr($timestamp, -7);
        
        // 生成2位随机数(10-99)作为首位
        $firstDigit = mt_rand(10, 99);
        
        // 组合成8位ID
        $id = $firstDigit . $timestampPart;
        
        return $id;
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

    public static function generateOrderId($payChannel = 1)
    {
       $pre_code = date("YmdHis");
       $suffix_code = rand(10000000, 99999999);
   
       return (string) $payChannel.$pre_code.$suffix_code;
    }

    public static function simpleEncode($data)
    {
        // 将数据转换为二进制
        $binary = '';
        for ($i = 0; $i < strlen($data); $i++) {
            // 将每个字符转换为8位二进制
            $binary .= sprintf('%08b', ord($data[$i]));
        }
        
        // 将二进制数据分组并转换为字母数字字符
        $encoded = '';
        // 每5位二进制数据映射为一个字符（2^5=32种可能性）
        $chunks = str_split($binary, 5);
        
        // 字母数字字符集（不含容易混淆的字符如O和0，l和1等）
        $charset = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        
        foreach ($chunks as $chunk) {
            // 如果最后一个块不足5位，填充0
            $chunk = str_pad($chunk, 5, '0');
            // 将5位二进制转换为十进制索引（0-31）
            $index = bindec($chunk) % strlen($charset);
            // 映射到字符集
            $encoded .= $charset[$index];
        }
        
        return $encoded;
    }
}
