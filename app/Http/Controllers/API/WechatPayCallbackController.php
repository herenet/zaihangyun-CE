<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionOrder;
use App\Services\SubscriptionWechatPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WechatPayCallbackController extends Controller
{
    private $wechatPayService;

    public function __construct(SubscriptionWechatPayService $wechatPayService)
    {
        $this->wechatPayService = $wechatPayService;
    }

    /**
     * 处理微信支付回调（v3版本）
     */
    public function handle(Request $request)
    {
        try {
            // 获取回调数据
            $body = $request->getContent();
            $headers = $request->headers->all();
            
            Log::info('微信支付v3回调原始数据', [
                'body' => $body,
                'headers' => $headers
            ]);

            // 验证签名
            if (!$this->wechatPayService->verifyCallback($headers, $body)) {
                Log::error('微信支付v3回调签名验证失败');
                return response($this->wechatPayService->generateCallbackResponse('FAIL', '签名验证失败'), 400)
                    ->header('Content-Type', 'application/json');
            }

            // 解析JSON数据
            $data = json_decode($body, true);
            if (!$data) {
                Log::error('微信支付v3回调数据解析失败', ['body' => $body]);
                return response($this->wechatPayService->generateCallbackResponse('FAIL', '数据格式错误'), 400)
                    ->header('Content-Type', 'application/json');
            }

            Log::info('微信支付v3回调解析数据', ['data' => $data]);

            // 检查事件类型
            if ($data['event_type'] !== 'TRANSACTION.SUCCESS') {
                Log::info('微信支付v3回调非成功事件', ['event_type' => $data['event_type']]);
                return response($this->wechatPayService->generateCallbackResponse('SUCCESS', '已接收'), 200)
                    ->header('Content-Type', 'application/json');
            }

            // 解密资源数据
            $resource = $this->decryptResource($data['resource']);
            if (!$resource) {
                Log::error('微信支付v3回调资源解密失败');
                return response($this->wechatPayService->generateCallbackResponse('FAIL', '解密失败'), 400)
                    ->header('Content-Type', 'application/json');
            }

            Log::info('微信支付v3回调解密数据', ['resource' => $resource]);

            // 检查支付状态
            if ($resource['trade_state'] !== 'SUCCESS') {
                Log::error('微信支付v3回调支付未成功', ['trade_state' => $resource['trade_state']]);
                return response($this->wechatPayService->generateCallbackResponse('SUCCESS', '已接收'), 200)
                    ->header('Content-Type', 'application/json');
            }

            // 获取订单信息
            $outTradeNo = $resource['out_trade_no'];
            $transactionId = $resource['transaction_id'];
            $totalAmount = $resource['amount']['total'];

            // 查找订单
            $order = SubscriptionOrder::where('order_id', $outTradeNo)->first();
            if (!$order) {
                Log::error('微信支付v3回调订单不存在', ['out_trade_no' => $outTradeNo]);
                return response($this->wechatPayService->generateCallbackResponse('FAIL', '订单不存在'), 400)
                    ->header('Content-Type', 'application/json');
            }

            // 检查订单状态
            if ($order->isPaid()) {
                Log::info('微信支付v3回调订单已支付', ['order_id' => $outTradeNo]);
                return response($this->wechatPayService->generateCallbackResponse('SUCCESS', '已处理'), 200)
                    ->header('Content-Type', 'application/json');
            }

            // 验证金额
            if ($order->final_price != $totalAmount) {
                Log::error('微信支付v3回调金额不匹配', [
                    'order_id' => $outTradeNo,
                    'order_amount' => $order->final_price,
                    'paid_amount' => $totalAmount
                ]);
                return response($this->wechatPayService->generateCallbackResponse('FAIL', '金额不匹配'), 400)
                    ->header('Content-Type', 'application/json');
            }

            // 开始事务处理
            DB::beginTransaction();

            try {
                // 更新订单状态
                $order->update([
                    'status' => SubscriptionOrder::STATUS_PAID,
                    'third_party_transaction_id' => $transactionId,
                    'paid_at' => now(),
                ]);

                // 更新租户套餐信息
                $this->updateTenantSubscription($order);

                DB::commit();

                Log::info('微信支付v3回调处理成功', [
                    'order_id' => $outTradeNo,
                    'transaction_id' => $transactionId,
                    'tenant_id' => $order->tenant_id,
                    'product' => $order->to_product
                ]);

                return response($this->wechatPayService->generateCallbackResponse('SUCCESS', '成功'), 200)
                    ->header('Content-Type', 'application/json');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('微信支付v3回调处理失败', [
                    'order_id' => $outTradeNo,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response($this->wechatPayService->generateCallbackResponse('FAIL', '处理失败'), 500)
                    ->header('Content-Type', 'application/json');
            }

        } catch (\Exception $e) {
            Log::error('微信支付v3回调异常', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response($this->wechatPayService->generateCallbackResponse('FAIL', '系统异常'), 500)
                ->header('Content-Type', 'application/json');
        }
    }

    /**
     * 解密回调资源数据
     */
    private function decryptResource($resource)
    {
        try {
            $config = config('wechat_pay');
            
            // 获取加密数据
            $ciphertext = base64_decode($resource['ciphertext']);
            $nonce = $resource['nonce'];
            $associatedData = $resource['associated_data'];
            
            // 使用APIv3密钥解密（v3使用32位API密钥）
            $apiKey = $config['api_v3_key'] ?? '';
            if (strlen($apiKey) !== 32) {
                throw new \Exception('APIv3密钥长度不正确，需要32位密钥');
            }
            
            // AES-256-GCM解密
            $decrypted = openssl_decrypt(
                substr($ciphertext, 0, -16),
                'aes-256-gcm',
                $apiKey,
                OPENSSL_RAW_DATA,
                $nonce,
                substr($ciphertext, -16),
                $associatedData
            );
            
            if ($decrypted === false) {
                throw new \Exception('解密失败');
            }
            
            return json_decode($decrypted, true);
            
        } catch (\Exception $e) {
            Log::error('微信支付v3回调解密异常', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * 更新租户套餐信息
     */
    private function updateTenantSubscription(SubscriptionOrder $order)
    {
        $tenant = $order->tenant;
        
        // 计算新的到期时间
        $newExpiresAt = now()->addYear();
        
        // 如果是续费，基于现有到期时间延长
        if ($order->order_type === SubscriptionOrder::TYPE_RENEW && 
            $tenant->subscription_expires_at && 
            $tenant->subscription_expires_at > now()) {
            $newExpiresAt = $tenant->subscription_expires_at->addYear();
        }
        
        $tenant->update([
            'product' => $order->to_product,
            'subscription_expires_at' => $newExpiresAt,
        ]);
        
        Log::info('租户套餐更新成功', [
            'tenant_id' => $tenant->id,
            'from_product' => $order->from_product,
            'to_product' => $order->to_product,
            'expires_at' => $newExpiresAt,
            'order_type' => $order->order_type
        ]);
    }
}