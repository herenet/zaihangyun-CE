<?php

namespace App\Admin\Actions;

use Storage;
use App\Libs\Helpers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\WechatPayService;
use App\Models\WechatPaymentConfig;
use Encore\Admin\Actions\RowAction;
use App\Models\WechatOpenPlatformConfig;
use App\Admin\Extensions\ZhyResponse;

class WechatPayInterfaceCheck extends RowAction
{
    public $name = '验证配置';

    // 这个方法定义在列表中如何显示
    public function display($value)
    {
        if ($value == 1) {
            return '<span class="label label-success">验证通过</span>';
        } else {
            // 显示一个可点击的按钮
            return <<<HTML
            <span class="interface-check-btn" data-id="{$this->getKey()}">
                <button class="btn btn-xs btn-warning">点击验证</button>
            </span>
            <span class="interface-check-loading" style="display:none">
                <i class="fa fa-spinner fa-spin"></i> 验证中...
            </span>
            HTML;
        }
    }

    public function form()
    { 
        $this->select('wechat_appid', '选择微信开放平台APP')
        ->options(WechatOpenPlatformConfig::get()->pluck('app_name', 'wechat_appid'))
        ->required();
    }

    // 这个方法定义点击后的处理逻辑
    public function handle(WechatPaymentConfig $config, Request $request)
    {
        try {
            // 获取选择的微信开放平台APP
            $wechat_appid = $request->get('wechat_appid');
            
            // 调用验证接口
            $result = $this->validateInterface($config, $wechat_appid);
            $this->downloadAndDecryptCert($config);
            
            if ($result['status']) {
                // 验证成功，更新数据库
                $config->interface_check = 1;
                $config->save();
                
                return $this->response()
                    ->success('配置验证成功')
                    ->html('<span class="label label-success">验证通过</span>')
                    ->refresh(); // 刷新列表
            } else {

                return (new ZhyResponse())
                    ->swal()
                    ->show('error', '配置验证失败', '配置验证失败: ' . ($result['message'] ?? '未知错误'))
                    ->refresh(false);
            }
        } catch (\Exception $e) {
                return (new ZhyResponse())
                    ->swal()
                    ->show('error', '验证过程发生错误', '验证过程发生错误: ' . $e->getMessage())
                    ->refresh(false);
        }
    }

    // 实际的接口验证逻辑
    protected function validateInterface($config, $wechatAppid)
    {
        $fileExists = Storage::disk('Admin-mch')->exists($config->mch_private_key_path);
        if (!$fileExists) {
            return ['status' => false, 'message' => '商户私钥文件不存在'];
        }

        $fileExists = Storage::disk('Admin-mch')->exists($config->mch_platform_cert_path);
        if (!$fileExists) {
            return ['status' => false, 'message' => '平台证书文件不存在'];
        }

        // $notify_url = route('wechat.payment.check-callback', ['config_id' => $config->id]);
        $notify_url = 'https://www.zaihangyun.com/api/wechat/payment/check-callback/'.$config->id;
        try {
            $wechatPayService = new WechatPayService(
                $wechatAppid, 
                $config->mch_id, 
                $config->mch_cert_serial, 
                $config->mch_private_key_path, 
                $config->mch_platform_cert_path, 
                $notify_url,
            );
            $oid = Helpers::generateOrderId();
            $wechatPayService->createAppOrder($oid, 1, '测试订单');
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
        
        return ['status' => true];
    }

    protected function downloadAndDecryptCert($config)
    {
        // 准备请求参数
        $mchId = $config->mch_id;
        $serialNo = $config->mch_cert_serial;
        $privateKey = $config->mch_private_key_path;
        $apiV3Key = $config->mch_api_v3_secret;
        
        // 构建请求头
        $url = 'https://api.mch.weixin.qq.com/v3/certificates';
        $timestamp = time();
        $nonce = bin2hex(random_bytes(16));
        
        // 构建签名
        $message = "GET\n/v3/certificates\n{$timestamp}\n{$nonce}\n\n";
        openssl_sign($message, $signature, Storage::disk('Admin-mch')->get($privateKey), 'sha256WithRSAEncryption');
        $signature = base64_encode($signature);
        
        // 发送请求
        $client = new Client();
        $response = $client->get($url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'WECHATPAY2-SHA256-RSA2048 ' . 
                    "mchid=\"{$mchId}\",serial_no=\"{$serialNo}\",nonce_str=\"{$nonce}\"," .
                    "timestamp=\"{$timestamp}\",signature=\"{$signature}\"",
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        // 解析响应
        $responseBody = $response->getBody()->getContents();
        $responseData = json_decode($responseBody, true);
        
        if (!isset($responseData['data']) || empty($responseData['data'])) {
            throw new \Exception('未获取到证书数据');
        }
        
        // 获取最新的证书
        $certData = $responseData['data'][0];
        $encryptCert = $certData['encrypt_certificate'];
        
        // 解密证书
        $decryptedCert = $this->decryptCertificate(
            $encryptCert['associated_data'],
            $encryptCert['nonce'],
            $encryptCert['ciphertext'],
            $apiV3Key
        );
        
        return [
            'response' => $responseData,
            'decrypted' => $decryptedCert
        ];
    }
    
    protected function decryptCertificate($associatedData, $nonce, $ciphertext, $key)
    {
        // Base64解码密文
        $ciphertext = base64_decode($ciphertext);
        
        // 使用AEAD_AES_256_GCM算法解密
        $decrypted = openssl_decrypt(
            substr($ciphertext, 0, -16), // 去除末尾的TAG
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $nonce,
            substr($ciphertext, -16), // TAG
            $associatedData
        );
        
        if ($decrypted === false) {
            throw new \Exception('证书解密失败: ' . openssl_error_string());
        }
        
        return $decrypted;
    }
    
    protected function validateCertificate($certContent)
    {
        // 验证是否为有效的X.509证书
        $cert = openssl_x509_read($certContent);
        return $cert !== false;
    }
    
    protected function getCertificateInfo($certContent)
    {
        // 读取证书信息
        $cert = openssl_x509_read($certContent);
        $certInfo = openssl_x509_parse($cert);
        
        return [
            'serialNumber' => $certInfo['serialNumber'] ?? '未知',
            'validFrom' => date('Y-m-d H:i:s', $certInfo['validFrom_time_t'] ?? 0),
            'validTo' => date('Y-m-d H:i:s', $certInfo['validTo_time_t'] ?? 0),
            'issuer' => $certInfo['issuer']['CN'] ?? '未知',
            'subject' => $certInfo['subject']['CN'] ?? '未知',
        ];
    }

}
