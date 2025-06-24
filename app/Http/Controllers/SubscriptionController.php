<?php

namespace App\Http\Controllers;

use App\Libs\Helpers;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Models\SubscriptionOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Auth;
use App\Services\SubscriptionWechatPayService;

class SubscriptionController extends Controller
{
    private $wechatPayService;

    public function __construct(SubscriptionWechatPayService $wechatPayService)
    {
        $this->wechatPayService = $wechatPayService;
    }

    /**
     * 验证套餐购买权限
     */
    private function validateSubscriptionPermission($tenant, $targetProductKey)
    {
        // 定义套餐层级
        $productLevels = [
            'free' => 0,
            'basic' => 1,
            'adv' => 2,
            'pro' => 3,
            'company' => 4,
        ];
        
        $currentProduct = $tenant->product;
        $currentExpiresAt = $tenant->subscription_expires_at;
        
        $currentLevel = $productLevels[$currentProduct] ?? 0;
        $targetLevel = $productLevels[$targetProductKey] ?? 0;
        
        // 如果当前是免费版，允许购买任何套餐
        if ($currentProduct === 'free') {
            return true;
        }
        
        // 如果当前是付费套餐
        if ($currentLevel > 0) {
            // 检查套餐是否有效（未过期或永不过期）
            $isSubscriptionValid = !$currentExpiresAt || $currentExpiresAt > now();
            
            // 如果当前套餐有效，且目标套餐级别更低，则不允许降级
            if ($isSubscriptionValid && $targetLevel < $currentLevel) {
                Log::warning('尝试降级套餐被阻止', [
                    'tenant_id' => $tenant->id,
                    'current_product' => $currentProduct,
                    'target_product' => $targetProductKey,
                    'current_level' => $currentLevel,
                    'target_level' => $targetLevel,
                    'expires_at' => $currentExpiresAt,
                    'is_valid' => $isSubscriptionValid,
                ]);
                return false;
            }
        }
        
        return true;
    }

    /**
     * 套餐购买确认页面
     */
    public function confirm(Request $request)
    {
        // 检查登录状态
        if (!SaaSAdmin::user()) {
            // 使用Laravel的intended机制
            return redirect()->guest(route('admin.login'))->with('message', '请先登录');
        }

        $productKey = $request->input('product');
        $error = null;
        $productConfig = null;
        $priceInfo = null;
        $tenant = SaaSAdmin::user();
        
        // 验证套餐是否存在
        $productConfig = config("product.{$productKey}");
        if (!$productConfig) {
            $error = '套餐不存在';
        }
        
        // 如果套餐存在，继续验证权限
        if (!$error) {
            // 验证是否允许购买此套餐
            if (!$this->validateSubscriptionPermission($tenant, $productKey)) {
                $error = '不支持降级套餐，如需降级请联系客服';
            }
        }
        
        // 如果权限验证通过，计算价格
        if (!$error) {
            try {
                $priceInfo = $this->calculateSubscriptionPrice($tenant->id, $productKey);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        return view('subscription.confirm', [
            'tenant' => $tenant,
            'product_key' => $productKey,
            'product_config' => $productConfig,
            'price_info' => $priceInfo,
            'error' => $error,
        ]);
    }

    /**
     * 创建订单并跳转支付
     */
    public function createOrder(Request $request)
    {
        try {
            // 检查登录状态
            if (!SaaSAdmin::user()) {
                return response()->json(['success' => false, 'message' => '请先登录']);
            }

            $productKey = $request->input('product');
            $tenant = SaaSAdmin::user();

            // 验证套餐
            $productConfig = config("product.{$productKey}");
            if (!$productConfig) {
                return response()->json(['success' => false, 'message' => '套餐不存在']);
            }

            // 计算价格（这里会检查是否允许降级）
            try {
                $priceInfo = $this->calculateSubscriptionPrice($tenant->id, $productKey);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            // 如果是免费套餐，直接返回错误
            if ($priceInfo['price'] <= 0 && $productKey !== 'free') {
                return response()->json(['success' => false, 'message' => '订单金额异常']);
            }

            DB::beginTransaction();

            // 生成订单号
            $orderId = Helpers::generateOrderId(SubscriptionOrder::PAY_CHANNEL_WECHAT);

            // 创建订单
            $order = SubscriptionOrder::create([
                'order_id' => $orderId,
                'tenant_id' => $tenant->id,
                'order_type' => $priceInfo['type'],
                'from_product' => $priceInfo['type'] === 'upgrade' ? $tenant->product : null,
                'to_product' => $productKey,
                'product_name' => $productConfig['name'],
                'original_price' => $productConfig['price'],
                'final_price' => $priceInfo['price'],
                'status' => SubscriptionOrder::STATUS_PENDING,
                'pay_channel' => SubscriptionOrder::PAY_CHANNEL_WECHAT,
                'upgrade_info' => $priceInfo['upgrade_info'] ?? null,
            ]);

            // 如果金额为0（免费套餐或特殊情况），直接标记为已支付
            if ($priceInfo['price'] <= 0) {
                $order->markAsPaid();
                $this->updateTenantSubscription($order);
                
                DB::commit();
                return response()->json([
                    'success' => true, 
                    'message' => '订单创建成功',
                    'redirect' => route('console.dashboard')
                ]);
            }

            // 调用微信支付
            $wechatResult = $this->wechatPayService->createNativeOrder([
                'body' => "在行云BaaS - {$productConfig['name']}",
                'out_trade_no' => $orderId,
                'total_fee' => $priceInfo['price'], // 金额已经是分
            ]);

            // 更新订单的微信支付信息
            $order->update([
                'wechat_prepay_id' => $wechatResult['prepay_id'],
                'wechat_code_url' => $wechatResult['code_url'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '订单创建成功',
                'data' => [
                    'order_id' => $orderId,
                    'code_url' => $wechatResult['code_url'],
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('创建套餐订单失败', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id ?? null,
                'product_key' => $productKey
            ]);

            return response()->json([
                'success' => false,
                'message' => '订单创建失败: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 支付页面
     */
    public function payment($orderId)
    {
        $order = SubscriptionOrder::where('order_id', $orderId)
            ->where('tenant_id', SaaSAdmin::user()->id)
            ->first();

        if (!$order) {
            return redirect()->route('pricing')->with('error', '订单不存在');
        }

        if (!$order->canPay()) {
            return redirect()->route('pricing')->with('error', '订单状态异常');
        }

        return view('subscription.payment', compact('order'));
    }

    /**
     * 查询订单状态（用于前端轮询）
     */
    public function queryOrderStatus($orderId)
    {
        $order = SubscriptionOrder::where('order_id', $orderId)
            ->where('tenant_id', SaaSAdmin::user()->id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => '订单不存在']);
        }

        // 直接返回数据库中的订单状态，不主动查询微信
        return response()->json([
            'success' => true,
            'data' => [
                'status' => $order->status,
                'status_text' => $order->status_text,
                'is_paid' => $order->isPaid(),
            ]
        ]);
    }

    /**
     * 计算套餐价格
     */
    private function calculateSubscriptionPrice($tenantId, $targetProductKey)
    {
        $tenant = Tenant::find($tenantId);
        $currentProduct = $tenant->product;
        $currentExpiresAt = $tenant->subscription_expires_at;
        
        // 定义套餐层级
        $productLevels = [
            'free' => 0,
            'basic' => 1,
            'adv' => 2,
            'pro' => 3,
            'company' => 4,
        ];
        
        $currentLevel = $productLevels[$currentProduct] ?? 0;
        $targetLevel = $productLevels[$targetProductKey] ?? 0;
        
        $targetPrice = config("product.{$targetProductKey}.price"); // 按分计算
        
        // 检查套餐是否有效（未过期或永不过期）
        $isSubscriptionValid = !$currentExpiresAt || $currentExpiresAt > now();
        
        // 检查是否为降级操作
        if ($currentProduct !== 'free' && $isSubscriptionValid && $targetLevel < $currentLevel) {
            throw new \Exception('不支持降级套餐，如需降级请联系客服');
        }
        
        // 如果是免费版或套餐已过期，按全价购买
        if ($currentProduct === 'free' || ($currentExpiresAt && $currentExpiresAt < now())) {
            return [
                'type' => SubscriptionOrder::TYPE_NEW_PURCHASE,
                'price' => $targetPrice,
                'expires_at' => now()->addYear()
            ];
        }
        
        // 同级续费
        if ($currentProduct === $targetProductKey) {
            // 如果当前套餐永不过期（subscription_expires_at为空），续费后也设为永不过期
            $newExpiresAt = $currentExpiresAt ? $currentExpiresAt->addYear() : null;
            
            return [
                'type' => SubscriptionOrder::TYPE_RENEW,
                'price' => $targetPrice,
                'expires_at' => $newExpiresAt
            ];
        }
        
        // 升级：按比例补差价
        $currentPrice = config("product.{$currentProduct}.price");
        
        // 如果当前套餐永不过期，升级后也设为永不过期，不计算剩余价值
        if (!$currentExpiresAt) {
            return [
                'type' => SubscriptionOrder::TYPE_UPGRADE,
                'price' => $targetPrice, // 永不过期的套餐升级按全价
                'expires_at' => null, // 升级后也永不过期
                'upgrade_info' => [
                    'from_product' => $currentProduct,
                    'remaining_days' => '永久',
                    'remaining_value' => 0,
                    'current_price' => $currentPrice,
                    'target_price' => $targetPrice
                ]
            ];
        }
        
        // 有到期时间的套餐升级，按比例计算
        $remainingDays = now()->diffInDays($currentExpiresAt, false);
        $remainingDays = max(0, $remainingDays);
        
        // 统一按分计算，避免小数问题
        $dailyCurrentPrice = intval($currentPrice / 365); // 向下取整
        $remainingValue = $dailyCurrentPrice * $remainingDays;
        $upgradePrice = max(0, $targetPrice - $remainingValue);
        
        return [
            'type' => SubscriptionOrder::TYPE_UPGRADE,
            'price' => $upgradePrice,
            'expires_at' => now()->addYear(),
            'upgrade_info' => [
                'from_product' => $currentProduct,
                'remaining_days' => $remainingDays,
                'remaining_value' => $remainingValue,
                'current_price' => $currentPrice,
                'target_price' => $targetPrice
            ]
        ];
    }

    /**
     * 更新租户套餐信息
     */
    private function updateTenantSubscription(SubscriptionOrder $order)
    {
        $tenant = $order->tenant;
        
        // 计算新的到期时间
        $newExpiresAt = now()->addYear();
        
        // 如果是续费
        if ($order->order_type === SubscriptionOrder::TYPE_RENEW) {
            if (!$tenant->subscription_expires_at) {
                // 如果当前套餐永不过期，续费后也永不过期
                $newExpiresAt = null;
            } elseif ($tenant->subscription_expires_at > now()) {
                // 如果当前套餐未过期，基于现有到期时间延长
                $newExpiresAt = $tenant->subscription_expires_at->addYear();
            }
            // 如果已过期，则从现在开始计算一年
        }
        
        // 如果是升级且原套餐永不过期
        if ($order->order_type === SubscriptionOrder::TYPE_UPGRADE && !$tenant->subscription_expires_at) {
            $newExpiresAt = null; // 升级后也永不过期
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
            'order_type' => $order->order_type,
        ]);
    }
} 