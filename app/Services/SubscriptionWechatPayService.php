<?php

namespace App\Services;

use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubscriptionWechatPayService
{
    private $instance;
    private $config;

    public function __construct()
    {
        $this->config = config('wechat_pay');
        $this->initWechatPay();
    }

    /**
     * 初始化微信支付实例
     */
    private function initWechatPay()
    {
        try {
            // 检查证书文件是否存在
            $privateKeyPath = $this->config['private_key_path'];
            $platformCertPath = $this->config['platform_cert_path'];
            
            if (!file_exists($privateKeyPath)) {
                throw new \Exception("私钥文件不存在: {$privateKeyPath}");
            }
            
            if (!file_exists($platformCertPath)) {
                throw new \Exception("平台证书文件不存在: {$platformCertPath}");
            }

            // 读取证书内容
            $merchantPrivateKeyContent = file_get_contents($privateKeyPath);
            $platformCertContent = file_get_contents($platformCertPath);

            if (!$merchantPrivateKeyContent) {
                throw new \Exception("无法读取私钥文件内容");
            }
            
            if (!$platformCertContent) {
                throw new \Exception("无法读取平台证书文件内容");
            }

            // 创建RSA实例
            $merchantPrivateKeyInstance = Rsa::from($merchantPrivateKeyContent, Rsa::KEY_TYPE_PRIVATE);
            $platformPublicKeyInstance = Rsa::from($platformCertContent, Rsa::KEY_TYPE_PUBLIC);

            // 商户证书序列号
            $merchantCertificateSerial = $this->config['cert_serial_no'];

            // 获取平台证书序列号 - 这是关键！
            $platformCertificateSerial = PemUtil::parseCertificateSerialNo($platformCertContent);

            // 构造一个 APIv3 实例
            $this->instance = Builder::factory([
                'mchid' => $this->config['mch_id'],
                'serial' => $merchantCertificateSerial,
                'privateKey' => $merchantPrivateKeyInstance,
                'certs' => [
                    $platformCertificateSerial => $platformPublicKeyInstance,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('微信支付初始化失败', ['error' => $e->getMessage()]);
            throw new \Exception('微信支付配置错误: ' . $e->getMessage());
        }
    }

    /**
     * 创建Native支付订单（使用v3接口）
     */
    public function createNativeOrder($orderData)
    {
        try {
            $params = [
                'appid' => $this->config['app_id'],
                'mchid' => $this->config['mch_id'],
                'description' => $orderData['body'],
                'out_trade_no' => $orderData['out_trade_no'],
                'time_expire' => $this->getTimeExpireV3(),
                'notify_url' => url($this->config['notify_url']),
                'amount' => [
                    'total' => $orderData['total_fee'],
                    'currency' => 'CNY'
                ]
            ];

            Log::info('微信支付v3下单请求', ['params' => $params]);

            // 调用统一下单接口
            $response = $this->instance
                ->v3->pay->transactions->native
                ->post(['json' => $params]);

            $result = json_decode($response->getBody()->getContents(), true);

            Log::info('微信支付v3下单响应', ['result' => $result]);

            if (!isset($result['code_url'])) {
                throw new \Exception('微信支付下单失败: 未返回二维码链接');
            }

            return [
                'prepay_id' => $result['prepay_id'] ?? '',
                'code_url' => $result['code_url'],
            ];

        } catch (\Exception $e) {
            Log::error('微信支付v3下单异常', [
                'error' => $e->getMessage(),
                'order_data' => $orderData
            ]);
            throw $e;
        }
    }

    /**
     * 查询订单状态
     */
    public function queryOrder($outTradeNo)
    {
        try {
            $response = $this->instance
                ->v3->pay->transactions->outTradeNo->{$outTradeNo}
                ->get([
                    'query' => [
                        'mchid' => $this->config['mch_id']
                    ]
                ]);

            $result = json_decode($response->getBody()->getContents(), true);

            Log::info('微信支付v3查询订单响应', ['result' => $result]);

            return $result;

        } catch (\Exception $e) {
            Log::error('查询微信支付v3订单异常', [
                'error' => $e->getMessage(),
                'out_trade_no' => $outTradeNo
            ]);
            throw $e;
        }
    }

    /**
     * 验证回调签名（v3版本）
     */
    public function verifyCallback($headers, $body)
    {
        try {
            // 获取签名相关头部信息 - 注意Laravel返回的头部键名是小写的
            $signature = $headers['wechatpay-signature'][0] ?? $headers['wechatpay-signature'] ?? '';
            $timestamp = $headers['wechatpay-timestamp'][0] ?? $headers['wechatpay-timestamp'] ?? '';
            $nonce = $headers['wechatpay-nonce'][0] ?? $headers['wechatpay-nonce'] ?? '';
            $serial = $headers['wechatpay-serial'][0] ?? $headers['wechatpay-serial'] ?? '';

            // 如果是数组，取第一个元素
            if (is_array($signature)) $signature = $signature[0];
            if (is_array($timestamp)) $timestamp = $timestamp[0];
            if (is_array($nonce)) $nonce = $nonce[0];
            if (is_array($serial)) $serial = $serial[0];

            if (empty($signature) || empty($timestamp) || empty($nonce) || empty($serial)) {
                Log::error('微信支付回调缺少必要的头部信息', [
                    'headers' => $headers,
                    'signature' => $signature,
                    'timestamp' => $timestamp,
                    'nonce' => $nonce,
                    'serial' => $serial
                ]);
                return false;
            }

            Log::info('微信支付回调验签参数', [
                'signature' => $signature,
                'timestamp' => $timestamp,
                'nonce' => $nonce,
                'serial' => $serial
            ]);

            // 构造验签名串
            $message = $timestamp . "\n" . $nonce . "\n" . $body . "\n";
            
            Log::info('微信支付回调验签字符串', ['message' => $message]);

            // 验证签名
            $platformCertContent = file_get_contents($this->config['platform_cert_path']);
            $platformPublicKeyInstance = Rsa::from($platformCertContent, Rsa::KEY_TYPE_PUBLIC);
            
            $isValid = Rsa::verify($message, $signature, $platformPublicKeyInstance);
            
            Log::info('微信支付回调签名验证结果', ['is_valid' => $isValid]);
            
            return $isValid;

        } catch (\Exception $e) {
            Log::error('微信支付回调签名验证异常', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * 获取订单过期时间（v3格式）
     */
    private function getTimeExpireV3()
    {
        return date('c', strtotime('+2 hours')); // RFC3339格式
    }

    /**
     * 生成回调响应（v3版本）
     */
    public function generateCallbackResponse($code = 'SUCCESS', $message = '成功')
    {
        return json_encode([
            'code' => $code,
            'message' => $message
        ]);
    }
} 