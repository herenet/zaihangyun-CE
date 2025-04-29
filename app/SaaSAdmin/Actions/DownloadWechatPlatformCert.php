<?php

namespace App\SaaSAdmin\Actions;

use Illuminate\Http\Request;
use App\Services\WechatPayService;
use App\Models\WechatPaymentConfig;
use Encore\Admin\Actions\RowAction;

class DownloadWechatPlatformCert extends RowAction
{
    public $name = '下载平台证书';

    private $aesKey;
    const KEY_LENGTH_BYTE = 32;
    const AUTH_TAG_LENGTH_BYTE = 16;
    
    public function display($value)
    {
        return <<<HTML
        <span class="download-cert-btn" data-id="{$this->getKey()}">
                <button class="btn btn-xs btn-warning">点击下载</button>
            </span>
        HTML;
    }

    public function handle(WechatPaymentConfig $config, Request $request)
    {
        $wechat_appid = $request->get('wechat_appid');
        try {
            $wechatPayService = new WechatPayService(
                $wechat_appid, 
                $config->mch_id, 
                $config->mch_cert_serial, 
                $config->mch_private_key_path, 
                $config->mch_platform_cert_path,
                $config->notify_url
            );
            $this->aesKey = $config->mch_api_v3_secret;
            $content = $wechatPayService->downloadPlatformCert();
            $cert_content = $content['data'][0]['encrypt_certificate'];
            $decryptedContent = $this->decryptToString($cert_content['associated_data'], $cert_content['nonce'], $cert_content['ciphertext']);
            // $this->response()->success('下载成功');
            dd($decryptedContent);
            // return $this->response()
            //         ->success('下载成功')
            //         ->($decryptedContent, 'platform_cert.pem');
        } catch (\Exception $e) {
            return $this->response()
                    ->error('下载失败: ' . ($e->getMessage() ?? '未知错误'))
                    ->refresh(false); 
        }
    }

    public function decryptToString($associatedData, $nonceStr, $ciphertext) {
        $ciphertext = \base64_decode($ciphertext);
        if (strlen($ciphertext) <= self::AUTH_TAG_LENGTH_BYTE) {
            return false;
        }
        // ext-sodium (default installed on >= PHP 7.2)
        if (function_exists('\sodium_crypto_aead_aes256gcm_is_available') && \sodium_crypto_aead_aes256gcm_is_available()) {
            return \sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $this->aesKey);
        }
        // ext-libsodium (need install libsodium-php 1.x via pecl)
        if (function_exists('\Sodium\crypto_aead_aes256gcm_is_available') && \Sodium\crypto_aead_aes256gcm_is_available()) {
            return \Sodium\crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $this->aesKey);
        }
        // openssl (PHP >= 7.1 support AEAD)
        if (PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())) {
            $ctext = substr($ciphertext, 0, -self::AUTH_TAG_LENGTH_BYTE);
            $authTag = substr($ciphertext, -self::AUTH_TAG_LENGTH_BYTE);
            return \openssl_decrypt(
                $ctext, 
                'aes-256-gcm', 
                $this->aesKey, 
                \OPENSSL_RAW_DATA, 
                $nonceStr,
                $authTag, 
                $associatedData
            );
        }
        throw new \RuntimeException('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
    }
}
