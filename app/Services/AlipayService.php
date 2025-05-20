<?php

namespace App\Services;

use App\Libs\Helpers;
use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;

class AlipayService
{
    private $_client;

    private $_gatewayHost = 'openapi.alipay.com';

    public function __construct(array $config)
    {
        Factory::setOptions($this->_getOptions($config));
        $this->_client = Factory::payment();
    }

    public function createAppOrder(string $subject, string $orderId, int $amount, string $appkey, int $tenantId)
    {
        $amount = sprintf("%.2f", $amount / 100);

        $callback_params = Helpers::simpleEncode($appkey.'-'.$tenantId);
        $notify_url = getenv("API_URL")."/v1/order/callback/alipay/".$callback_params;
        
        $result = $this->_client
            ->app()
            ->asyncNotify($notify_url)
            ->pay($subject, $orderId, $amount);
        if (!empty($result->body)) {
            return $result->body;
        } else {
            throw new \Exception('alipay return: '.$result->body);
        }
    }

    public function applyRefund(string $orderId, string $refundAmount, $refundReason)
    {
       
        $result = $this->_client
            ->common()
            ->optional('refund_reason', $refundReason ?? '退款')
            ->refund($orderId, $refundAmount);
        if (!empty($result->code)) {
            return $result->toMap();
        } else {
            throw new \Exception('alipay return: '.$result->msg);
        }
    }

    public function verifyNotify(array $params)
    {
        $result = $this->_client
            ->common()
            ->verifyNotify($params);
        return $result;
    }

    private function _getOptions(array $config)
    {
        $options = new Config();
        $options->protocol = "https";
        $options->gatewayHost = $this->_gatewayHost;
        $options->signType = "RSA2";
        $options->appId = $config["alipay_app_id"];
        $options->merchantPrivateKey = $config["app_private_cert"];
        $options->alipayPublicKey = $config["alipay_public_cert"];

        return $options;
    }
}