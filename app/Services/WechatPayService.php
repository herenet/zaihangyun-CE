<?php
namespace App\Services;

use Exception;
use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;
use Illuminate\Support\Facades\Storage;

class WechatPayService
{
    const REQUEST_SUCCESS_HTTP_CODE = 200;

    private $instance;

    private $appid;

    private $mchid;

    private $notifyUrl;

    public function __construct($appid, $mchid, $mch_cert_serial, $mch_private_key_path, $mch_platform_cert_path, $notifyUrl)
    {
        $this->appid = $appid;
        $this->mchid = $mchid;
        $this->notifyUrl = $notifyUrl;

        $mch_private_key_file = Storage::disk('SaaSAdmin-mch')->get($mch_private_key_path);
        $platform_cert_file = Storage::disk('SaaSAdmin-mch')->get($mch_platform_cert_path);

        $platform_public_key_instance = Rsa::from($platform_cert_file, Rsa::KEY_TYPE_PUBLIC);
        $platform_cert_serial = PemUtil::parseCertificateSerialNo($platform_cert_file);

        $this->instance = Builder::factory([
            "mchid" => $this->mchid,
            "serial" => $mch_cert_serial,
            "privateKey" => Rsa::from($mch_private_key_file, Rsa::KEY_TYPE_PRIVATE),
            "certs" => [
                $platform_cert_serial => $platform_public_key_instance,
            ]
        ]);
    }

    public function createAppOrder(string $orderId, int $amount, string $desc, array $attach = [])
    {
        $resp = $this->instance
            ->chain('/v3/pay/transactions/app')
            ->post(['json' => [
                'mchid' => $this->mchid,
                'out_trade_no' => $orderId,
                'appid' => $this->appid,
                'description' => $desc,
                'notify_url' => $this->notifyUrl,
                'amount' => ['total' => $amount],
            ]]);
        return $this->_getResponseContent($resp);
    }

    public function applyRefund($transactionId, int $amount, int $refundAmount, $refundId, $refundReason)
    {
        $resp = $this->instance
            ->chain('/v3/refund/domestic/refunds')
            ->post(['json' => [
                'transaction_id' => $transactionId,
                'out_refund_no' => $refundId,
                'reason' => $refundReason ?? '退款',
                'amount' => ['refund' => $refundAmount, 'total' => $amount, 'currency' => 'CNY'],
            ]]);
        return $this->_getResponseContent($resp);
    }

    public function downloadPlatformCert()
    {
        $resp = $this->instance->chain('/v3/certificates')->get();
        return $this->_getResponseContent($resp);
    }

    /**
     * @throws Exception
     */
    private function _getResponseContent(object $response) : array
    {
        $content = '';
        if ($response->getStatusCode() == self::REQUEST_SUCCESS_HTTP_CODE) {
            $content = json_decode($response->getBody()->getContents(), true);
            if (!isset($content['errcode'])) {
                return $content;
            }
        }
        throw new Exception(json_encode($content), $response->getStatusCode());
    }

    public static function generateSign(string $appId, int $timestamp, string $nonceStr, string $prepayId) : string
    {
        $sign_str = $appId."\n".$timestamp."\n".$nonceStr."\n".$prepayId."\n";
        $mch_private_key_file = 'file://'.config('wechat.mch_private_key');
        $mch_private_key_instence = Rsa::from($mch_private_key_file, Rsa::KEY_TYPE_PRIVATE);

        return Rsa::sign($sign_str, $mch_private_key_instence);
    }
}