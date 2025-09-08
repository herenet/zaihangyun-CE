<?php

use support\Cache;
use Illuminate\Support\Str;
use Readdle\AppStoreServerAPI\Util\Helper;
use WebmanTech\LaravelFilesystem\Facades\Storage;
/**
 * Here is your custom functions.
 */

 //生成用户ID，8位数字
 function generateUserId()
 {
    return hexdec(substr(md5((string)Str::uuid()), 0, 8));
 }

 function getAvatarUrl($path)
 {
    if(empty($path)){
        return '';
    }
    if(strpos($path, 'http') !== false){
        return $path;
    }
    return Storage::disk('local_avatar')->url($path);
 }

 function getArticleUrl($appKey, $id)
 {
    return config('app.doc_url').'/article/'.$appKey.'/'.$id;
 }

 function generateOrderId($payChannel, $oidPrefix = '')
 {
    $pre_code = date("YmdHis");
    $suffix_code = rand(10000000, 99999999);

    $oid = (string) $payChannel.$pre_code.$suffix_code;
    if(!empty($oidPrefix)){
        $oid = $oidPrefix.'-'.$oid;
    }
    return $oid;
 }

 /**
 * 简单编码函数 - 将任意字符串转换为只包含字母和数字的编码
 * 
 * @param string $data 需要编码的数据
 * @return string 编码后的数据
 */
function simpleEncode($data)
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

/**
 * 简单解码函数 - 将编码后的字符串还原
 * 
 * @param string $encoded 编码后的数据
 * @return string 解码后的原始数据
 */
function simpleDecode($encoded)
{
    // 字母数字字符集（需与编码时使用的完全一致）
    $charset = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    
    // 将编码字符还原为二进制
    $binary = '';
    for ($i = 0; $i < strlen($encoded); $i++) {
        $char = $encoded[$i];
        $position = strpos($charset, $char);
        if ($position === false) {
            continue; // 忽略不在字符集中的字符
        }
        // 将位置转换回5位二进制
        $binary .= sprintf('%05b', $position);
    }
    
    // 将二进制数据转换回原始字符
    $result = '';
    // 每8位二进制转换为一个字符
    $bytes = str_split($binary, 8);
    foreach ($bytes as $byte) {
        // 忽略不完整的字节
        if (strlen($byte) == 8) {
            $result .= chr(bindec($byte));
        }
    }
    
    return $result;
}

function isInGray($clientId, $grayPercent) {
    if (!$clientId || $grayPercent >= 100) return true;
    $hash = crc32($clientId);
    return ($hash % 100) < $grayPercent;
}

function getApplePublicCertificates()
{
    $cache_key = 'apple_public_certificates';
    $ttl = 60 * 60 * 24 * 30; // 30天
    $cert = Cache::get($cache_key);
    if (!$cert) {
        $cert = Helper::toPEM(file_get_contents('https://www.apple.com/certificateauthority/AppleRootCA-G3.cer'));
        Cache::set($cache_key, $cert, $ttl);
    }
    return $cert;
}
