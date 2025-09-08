<?php

namespace app\queue\redis;

use support\Db;
use support\Log;
use app\model\Order;
use app\model\AppleOrder;
use app\model\IAPProduct;
use Webman\RedisQueue\Consumer;
use app\model\AppleNotification;
use app\model\OrderInterfaceConfig;
use app\service\OrderBZLogicService;
use app\controller\api\OrderController;
use Readdle\AppStoreServerAPI\RenewalInfo;
use Readdle\AppStoreServerAPI\ResponseBodyV2;
use Readdle\AppStoreServerAPI\TransactionInfo;


class VerifyAppleNotify implements Consumer
{
    public $queue = 'verify-apple-notify';

    public $connection = 'default';

    public function consume($data)
    {
        $this->verifyAppleNotify($data);
    }

    public function onConsumerFailure(\Throwable $e, $package)
    {
        Log::channel('queue')->error('verify apple notify consumer failure:'.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }

    protected function verifyAppleNotify($data)
    {
        $transactionInfo = $data['transactionInfo'] ? TransactionInfo::createFromRawTransactionInfo($data['transactionInfo']) : null;
        $renewalInfo = $data['renewalInfo'] ? RenewalInfo::createFromRawRenewalInfo($data['renewalInfo']) : null;

        $appkey = $data['appkey'];
        $notificationType = $data['notification_type'];
        $subtype = $data['subtype'];
        $notificationId = $data['notification_id'];
        $transactionId = $data['transaction_id'];

        Log::channel('queue')->info('verify apple notify queue data', [
            'transactionId' => $transactionId,
            'appkey' => $appkey,
            'notificationType' => $notificationType,
            'subtype' => $subtype,
            'notificationId' => $notificationId
        ]);
        
        // 使用事务处理通知，确保原子性
        Db::beginTransaction();
        try{
            // 根据通知类型分发处理
            switch ($notificationType) {
                // 订阅成功（替代INITIAL_BUY用于订阅产品首次购买）
                case ResponseBodyV2::NOTIFICATION_TYPE__SUBSCRIBED:
                    $this->handleSubscribedV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 一次性收费（替代INITIAL_BUY用于一次性商品购买）
                case ResponseBodyV2::NOTIFICATION_TYPE__ONE_TIME_CHARGE:
                    $this->handleOneTimeChargeV2($transactionInfo, $renewalInfo, $appkey);
                    break;
                // 续费成功
                case ResponseBodyV2::NOTIFICATION_TYPE__DID_RENEW:
                    $this->handleDidRenewV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 续费失败
                case ResponseBodyV2::NOTIFICATION_TYPE__DID_FAIL_TO_RENEW:
                    $this->handleDidFailToRenewV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 续费状态变更（包含取消订阅逻辑）
                case ResponseBodyV2::NOTIFICATION_TYPE__DID_CHANGE_RENEWAL_STATUS:
                    $this->handleDidChangeRenewalStatusV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 订阅过期
                case ResponseBodyV2::NOTIFICATION_TYPE__EXPIRED:
                    $this->handleExpiredV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 宽限期过期
                case ResponseBodyV2::NOTIFICATION_TYPE__GRACE_PERIOD_EXPIRED:
                    $this->handleGracePeriodExpiredV2($transactionInfo, $renewalInfo, $appkey);
                    break;
                // 价格上涨
                case ResponseBodyV2::NOTIFICATION_TYPE__PRICE_INCREASE:
                    $this->handlePriceIncreaseV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 续费变更偏好
                case ResponseBodyV2::NOTIFICATION_TYPE__DID_CHANGE_RENEWAL_PREF:
                    $this->handleDidChangeRenewalPrefV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 优惠券兑换
                case ResponseBodyV2::NOTIFICATION_TYPE__OFFER_REDEEMED:
                    $this->handleOfferRedeemedV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 退款
                case ResponseBodyV2::NOTIFICATION_TYPE__REFUND:
                    $this->handleRefundV2($transactionInfo, $renewalInfo, $appkey);
                    break;
                // 撤销
                case ResponseBodyV2::NOTIFICATION_TYPE__REVOKE:
                    $this->handleRevokeV2($transactionInfo, $renewalInfo, $appkey);
                    break;
                // 续费延长
                case ResponseBodyV2::NOTIFICATION_TYPE__RENEWAL_EXTENDED:
                    $this->handleRenewalExtendedV2($transactionInfo, $renewalInfo, $appkey);
                    break;
                // 消耗数据请求
                case ResponseBodyV2::NOTIFICATION_TYPE__CONSUMPTION_REQUEST:
                    $this->handleConsumptionRequestV2($transactionInfo, $renewalInfo, $appkey);
                    break;
                // 退款请求被拒绝
                case ResponseBodyV2::NOTIFICATION_TYPE__REFUND_DECLINED:
                    $this->handleRefundDeclinedV2($transactionInfo, $renewalInfo, $appkey);
                    break;
                // 退款被撤销
                case ResponseBodyV2::NOTIFICATION_TYPE__REFUND_REVERSED:
                    $this->handleRefundReversedV2($transactionInfo, $renewalInfo, $appkey);
                    break;
                // 批量续费延期状态
                case ResponseBodyV2::NOTIFICATION_TYPE__RENEWAL_EXTENSION:
                    $this->handleRenewalExtensionV2($transactionInfo, $renewalInfo, $appkey, $subtype);
                    break;
                // 其他通知类型
                default:
                    Log::channel('order')->info('unhandled apple notification type', [
                        'type' => $notificationType,
                        'subtype' => $subtype,
                        'transactionId' => $transactionId
                    ]);
            }

            // 更新通知处理状态为成功
            if ($notificationId) {
                $apple_notification_model = new AppleNotification();
                $apple_notification_model->updateProcessStatus($notificationId, AppleNotification::PROCESSED_YES, 'success');
            }
            
            Db::commit();
            
        }catch(\Exception $e){
            Db::rollBack();
            
            // 更新通知处理状态为失败
            if ($notificationId) {
                $apple_notification_model = new AppleNotification();
                $apple_notification_model->updateProcessStatus($notificationId, AppleNotification::PROCESSED_YES, 'error: '.$e->getMessage());
            }
            Log::channel('queue')->error('apple notify process failed', [
                'error' => $e->getMessage(),
                'notificationId' => $notificationId,
                'notificationType' => $notificationType,
                'transactionId' => $transactionId
            ]);
            throw $e;
        }
    }


    /**
     * 处理消耗数据请求通知
     * 当Apple请求提供消耗型商品的消耗数据时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     */
    private function handleConsumptionRequestV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey)
    {
        $transactionId = $transactionInfo->getTransactionId();
        
        Log::channel('order')->info('handling consumption request', [
            'transactionId' => $transactionId,
            'appkey' => $appkey
        ]);

        // 消耗数据请求通知通常需要回复消耗状态
        // 这里可以根据业务需求实现具体的消耗数据处理逻辑
        // 例如：记录用户对消耗型商品的使用情况
    }

    /**
     * 处理退款请求被拒绝通知
     * 当Apple拒绝退款请求时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     */
    private function handleRefundDeclinedV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey)
    {
        $transactionId = $transactionInfo->getTransactionId();
        
        Log::channel('order')->info('handling refund declined', [
            'transactionId' => $transactionId,
            'appkey' => $appkey
        ]);

        // 退款被拒绝，订单状态保持不变
        // 可以在这里添加通知用户退款被拒绝的逻辑
    }

    /**
     * 处理退款被撤销通知
     * 当之前的退款被撤销时触发，需要恢复用户权益
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     */
    private function handleRefundReversedV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey)
    {
        $transactionId = $transactionInfo->getTransactionId();
        
        Log::channel('order')->info('handling refund reversed', [
            'transactionId' => $transactionId,
            'appkey' => $appkey
        ]);

        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByTransactionId($transactionId, $appkey);
        
        if (!empty($order)) {
            // 退款被撤销，恢复订单为成功状态
            $update_data = [
                'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS,
                'cancellation_date' => null
            ];
            
            // 如果是订阅产品，恢复订阅状态
            if (AppleOrder::isSubscriptionProduct($order['product_type'])) {
                $update_data['subscription_status'] = AppleOrder::SUBSCRIPTION_STATUS_ACTIVE;
            }
            
            $apple_order_model->updateOrderInfoByOid($order['oid'], $update_data);
            
            // 恢复用户权益 - 重新执行业务逻辑
            if (!empty($order['uid']) && $order['uid'] != 0) {
                try {
                    $iap_product_model = new IAPProduct();
                    $product = $iap_product_model->getProductInfoByPid($order['product_id'], $order['app_key']);
                    
                    if (!empty($product)) {
                        $orderForBZ = array_merge($order, $update_data);
                        $orderBZLogicService = new OrderBZLogicService($orderForBZ, $product);
                        $orderBZLogicService->orderBZLogic();
                        
                        Log::channel('order')->info('refund reversed, user benefits restored', [
                            'oid' => $order['oid'],
                            'uid' => $order['uid']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::channel('order')->error('failed to restore user benefits for refund reversed', [
                        'oid' => $order['oid'],
                        'uid' => $order['uid'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * 处理批量续费延期状态通知
     * 当批量续费延期操作完成时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleRenewalExtensionV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        Log::channel('order')->info('handling renewal extension', [
            'appkey' => $appkey,
            'subtype' => $subtype
        ]);

        // 根据subtype处理不同的批量续费延期状态
        if ($subtype === ResponseBodyV2::SUBTYPE__SUMMARY) {
            // 批量操作完成摘要
            Log::channel('order')->info('renewal extension summary completed', [
                'appkey' => $appkey
            ]);
        } elseif ($subtype === ResponseBodyV2::SUBTYPE__FAILURE) {
            // 特定订阅的延期失败
            $originalTransactionId = $transactionInfo->getOriginalTransactionId();
            Log::channel('order')->warning('renewal extension failed for subscription', [
                'originalTransactionId' => $originalTransactionId,
                'appkey' => $appkey
            ]);
        }
        
        // 批量续费延期通知主要用于监控和统计，通常不需要更新具体订单
    }

     /**
     * 处理宽限期过期通知
     * 当订阅的宽限期结束时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     */
    private function handleGracePeriodExpiredV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey)
    {
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling grace period expired', [
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);

        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByOriginalTransactionId($originalTransactionId, $appkey);
        
        if (!empty($order)) {
            $apple_order_model->updateOrderInfoByOid($order['oid'], [
                'subscription_status' => AppleOrder::SUBSCRIPTION_STATUS_EXPIRED,
                'auto_renew_status' => 0
            ]);
        }
    }

    /**
     * 处理价格上涨通知
     * 当订阅产品价格上涨时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handlePriceIncreaseV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling price increase', [
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);

        // 价格上涨通知通常只需要记录，不需要更新订单状态
        // 可以在这里添加特定的业务逻辑，比如通知用户价格变更
    }

    /**
     * 处理更改续费偏好通知
     * 当用户更改订阅计划或产品时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleDidChangeRenewalPrefV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling renewal preference change', [
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);

        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByOriginalTransactionId($originalTransactionId, $appkey);
        
        if (!empty($order)) {
            // 更新续费产品ID（如果用户更改了订阅计划）
            $update_data = [];
            if (!empty($renewalInfo) && $renewalInfo->getAutoRenewProductId() != null) {
                $update_data['auto_renew_product_id'] = $renewalInfo->getAutoRenewProductId();
            }
            
            if (!empty($update_data)) {
                $apple_order_model->updateOrderInfoByOid($order['oid'], $update_data);
            }
        }
    }

    /**
     * 处理优惠兑换通知
     * 当用户兑换促销优惠码时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleOfferRedeemedV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $transactionId = $transactionInfo->getTransactionId();
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling offer redeemed', [
            'transactionId' => $transactionId,
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);

        // 优惠兑换可能创建新的交易或更新现有订阅
        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByTransactionId($transactionId, $appkey);
        
        if (empty($order)) {
            // 尝试通过原始交易ID获取用户ID
            $uid = 0;
            if ($originalTransactionId && $originalTransactionId !== $transactionId) {
                $originalOrder = $apple_order_model->getOrderByOriginalTransactionId($originalTransactionId, $appkey);
                if (!empty($originalOrder)) {
                    $uid = $originalOrder['uid'] ?? 0;
                }
            }
            
            // 创建新订单
            $this->createOrderFromNotification($transactionInfo, $renewalInfo, $appkey, $uid, $subtype);
        } else {
            // 更新现有订单
            $this->updateOrderFromNotification($order, $transactionInfo, $renewalInfo, $subtype);
        }
    }

    /**
     * 处理撤销通知
     * 当Apple撤销之前的交易时触发（通常用于家庭共享场景）
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleRevokeV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $transactionId = $transactionInfo->getTransactionId();
        
        Log::channel('order')->info('handling revoke', [
            'transactionId' => $transactionId,
            'appkey' => $appkey
        ]);

        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByTransactionId($transactionId, $appkey);
        
        if (!empty($order)) {
            $apple_order_model->updateOrderInfoByOid($order['oid'], [
                'payment_status' => AppleOrder::PAYMENT_STATUS_REFUNDED,
                'subscription_status' => AppleOrder::SUBSCRIPTION_STATUS_CANCELED,
                'cancellation_date' => date('Y-m-d H:i:s')
            ]);
            
            // 撤销相当于退款，需要处理用户VIP状态回退
            OrderController::refundOrderBZLogic($order);
        }
    }

    /**
     * 处理续费延期通知
     * 当Apple延长订阅期限时触发（通常用于服务中断补偿）
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleRenewalExtendedV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling renewal extended', [
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);

        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByOriginalTransactionId($originalTransactionId, $appkey);
        
        if (!empty($order)) {
            $update_data = [
                'subscription_status' => AppleOrder::SUBSCRIPTION_STATUS_ACTIVE
            ];
            
            // 更新过期时间（如果提供）
            if ($transactionInfo->getExpiresDate() != null) {
                $update_data['expires_date'] = date('Y-m-d H:i:s', $transactionInfo->getExpiresDate() / 1000);
            }
            
            $apple_order_model->updateOrderInfoByOid($order['oid'], $update_data);
            
            // 如果有新的过期时间且用户ID不为0，更新用户VIP状态
            if (!empty($update_data['expires_date']) && !empty($order['uid']) && $order['uid'] != 0) {
                // 合并订单数据和更新数据，用于业务逻辑处理
                $orderForBZ = array_merge($order, $update_data);
                
                // 获取产品信息
                $iap_product_model = new IAPProduct();
                $product = $iap_product_model->getProductInfoByPid($order['product_id'], $order['app_key']);
                
                if (!empty($product)) {
                    try {
                        // 执行业务逻辑，使用Apple延长后的过期时间更新用户VIP
                        $orderBZLogicService = new OrderBZLogicService($orderForBZ, $product);
                        $orderBZLogicService->orderBZLogic();
                        
                        Log::channel('order')->info('renewal extended vip updated', [
                            'oid' => $order['oid'],
                            'uid' => $order['uid'],
                            'new_expires_date' => $update_data['expires_date']
                        ]);
                    } catch (\Exception $e) {
                        Log::channel('order')->error('failed to update vip for renewal extended', [
                            'oid' => $order['oid'],
                            'uid' => $order['uid'],
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 处理订阅续费成功通知
     * 当自动续费成功时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleDidRenewV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling did renew', [
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);
        
        // 查找原始订单
        $apple_order_model = new AppleOrder();
        $originalOrder = $apple_order_model->getOrderByTransactionId($originalTransactionId, $appkey);
        
        if (!empty($originalOrder)) {
            // 创建续费订单或更新订阅状态
            $this->handleRenewalOrder($originalOrder, $transactionInfo, $renewalInfo, $appkey, $subtype);
        }else{
            throw new \Exception('did renew order not found: '.$originalTransactionId.' appkey: '.$appkey);
        }
    }

    /**
     * 处理续费订单逻辑
     * 检查是否已存在续费订单，不存在则创建，存在则更新
     * 
     * @param array $originalOrder 原始订单数据
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleRenewalOrder(array $originalOrder, TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $transactionId = $transactionInfo->getTransactionId();
        
        Log::channel('order')->info('handling renewal order', [
            'transactionId' => $transactionId,
            'originalOid' => $originalOrder['oid'],
            'appkey' => $appkey
        ]);
        
        // 检查是否已存在该交易ID的订单
        $apple_order_model = new AppleOrder();
        $existingOrder = $apple_order_model->getOrderByTransactionId($transactionId, $appkey);
        
        if (!empty($existingOrder)) {
            // 如果订单已存在，更新订单信息
            $this->updateOrderFromNotification($existingOrder, $transactionInfo, $renewalInfo, $subtype);
        } else {
            // 创建新的续费订单，传入原始订单的用户ID
            $originalUid = $originalOrder['uid'] ?? 0;
            $this->createOrderFromNotification($transactionInfo, $renewalInfo, $appkey, $originalUid, $subtype);
        }
    }

    /**
     * 处理订阅续费失败通知
     * 当自动续费失败时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleDidFailToRenewV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling did fail to renew', [
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);
        
        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByTransactionId($originalTransactionId, $appkey);
        
        if (!empty($order)) {
            $apple_order_model->updateOrderInfoByOid($order['oid'], [
                'subscription_status' => AppleOrder::SUBSCRIPTION_STATUS_EXPIRED,
                'auto_renew_status' => 0
            ]);
        }
    }

    /**
     * 处理续费状态变更通知
     * 当用户开启或关闭自动续费时触发（包含取消订阅逻辑）
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleDidChangeRenewalStatusV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $transactionId = $transactionInfo->getTransactionId();
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling renewal status change', [
            'transactionId' => $transactionId,
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);

        // 从 renewalInfo 中获取正确的 autoRenewStatus
        $autoRenewStatus = null;
        if (!empty($renewalInfo)) {
            $autoRenewStatus = (int)$renewalInfo->getAutoRenewStatus();
        }
        
        if ($autoRenewStatus === null) {
            Log::channel('order')->warning('renewal status change without auto_renew_status', [
                'transactionId' => $transactionId,
                'originalTransactionId' => $originalTransactionId,
                'appkey' => $appkey
            ]);
            return;
        }
        
        $apple_order_model = new AppleOrder();
        
        // 处理当前交易的订单记录
        $currentOrder = $apple_order_model->getOrderByTransactionId($transactionId, $appkey);
        
        if (empty($currentOrder)) {
            // 如果当前交易没有订单记录，需要创建一个
            // 通过原始交易ID获取用户ID和相关信息
            $originalOrder = $apple_order_model->getOrderByTransactionId($originalTransactionId, $appkey);
            if (!empty($originalOrder)) {
                $uid = $originalOrder['uid'] ?? 0;
                // 创建新订单记录这次状态变更交易
                $this->createOrderFromNotification($transactionInfo, $renewalInfo, $appkey, $uid, $subtype);
                
                Log::channel('order')->info('created order for renewal status change', [
                    'transactionId' => $transactionId,
                    'originalTransactionId' => $originalTransactionId,
                    'autoRenewStatus' => $autoRenewStatus,
                    'uid' => $uid
                ]);
            } else {
                Log::channel('order')->warning('no original order found for renewal status change', [
                    'transactionId' => $transactionId,
                    'originalTransactionId' => $originalTransactionId,
                    'appkey' => $appkey
                ]);
                return;
            }
        } else {
            // 如果当前交易已有订单记录，更新它
            $this->updateOrderFromNotification($currentOrder, $transactionInfo, $renewalInfo, $subtype);
            
            Log::channel('order')->info('updated order for renewal status change', [
                'oid' => $currentOrder['oid'],
                'transactionId' => $transactionId,
                'autoRenewStatus' => $autoRenewStatus
            ]);
        }
    }

    /**
     * 处理订阅过期通知
     * 当订阅到期且未续费时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleExpiredV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling expired', [
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);
        
        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByTransactionId($originalTransactionId, $appkey);
        
        if (!empty($order)) {
            $apple_order_model->updateOrderInfoByOid($order['oid'], [
                'subscription_status' => AppleOrder::SUBSCRIPTION_STATUS_EXPIRED
            ]);
        }else{
            throw new \Exception('expired order not found: '.$originalTransactionId.' appkey: '.$appkey);
        }
    }

    /**
     * 处理退款通知
     * 当Apple处理退款请求时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     */
    private function handleRefundV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey)
    {
        $transactionId = $transactionInfo->getTransactionId();
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling refund', [
            'transactionId' => $transactionId,
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);
        
        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByTransactionId($transactionId, $appkey);
        
        if (!empty($order)) {
            // 检查是否已经是退款状态，避免重复处理
            if ($order['payment_status'] == AppleOrder::PAYMENT_STATUS_REFUNDED) {
                Log::channel('order')->info('order already refunded, skipping', [
                    'oid' => $order['oid'],
                    'transactionId' => $transactionId
                ]);
                return;
            }
            
            // 获取退款日期
            $revocationDate = $transactionInfo->getRevocationDate();
            
            $updateData = [
                'payment_status' => AppleOrder::PAYMENT_STATUS_REFUNDED,
                'cancellation_date' => $revocationDate ? date('Y-m-d H:i:s', $revocationDate / 1000) : date('Y-m-d H:i:s')
            ];
            
            // 基于Apple交易信息判断是否为订阅产品（有过期时间的就是订阅产品）
            $isSubscriptionProduct = ($transactionInfo->getExpiresDate() !== null);
            
            if ($isSubscriptionProduct) {
                // 检查是否为部分退款还是全额退款
                $isPartialRefund = $this->isPartialRefund($transactionInfo, $order);
                
                if ($isPartialRefund) {
                    // 部分退款：订阅继续有效，但可能需要调整过期时间
                    $updateData['subscription_status'] = AppleOrder::SUBSCRIPTION_STATUS_ACTIVE;
                    
                    // 如果有新的过期时间，更新它
                    if ($transactionInfo->getExpiresDate() != null) {
                        $updateData['expires_date'] = date('Y-m-d H:i:s', $transactionInfo->getExpiresDate() / 1000);
                    }
                    
                    Log::channel('order')->info('partial refund for subscription', [
                        'oid' => $order['oid'],
                        'transactionId' => $transactionId,
                        'newExpiresDate' => $updateData['expires_date'] ?? 'unchanged'
                    ]);
                } else {
                    // 全额退款：取消订阅
                    $updateData['subscription_status'] = AppleOrder::SUBSCRIPTION_STATUS_CANCELED;
                    $updateData['auto_renew_status'] = 0;
                    
                    Log::channel('order')->info('full refund for subscription', [
                        'oid' => $order['oid'],
                        'transactionId' => $transactionId
                    ]);
                }
            }
            
            $apple_order_model->updateOrderInfoByOid($order['oid'], $updateData);

            // 执行退款业务逻辑
            try {
                OrderController::refundOrderBZLogic($order);
                
                Log::channel('order')->info('refund business logic executed', [
                    'oid' => $order['oid'],
                    'uid' => $order['uid']
                ]);
            } catch (\Exception $e) {
                Log::channel('order')->error('refund business logic failed', [
                    'oid' => $order['oid'],
                    'error' => $e->getMessage()
                ]);
                // 业务逻辑失败不影响退款状态更新
            }
        } else {
            Log::channel('order')->warning('refund order not found', [
                'transactionId' => $transactionId,
                'appkey' => $appkey
            ]);
        }
    }

    /**
     * 判断是否为部分退款
     * 基于交易信息和订单数据判断退款类型
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param array $order 订单数据
     * @return bool true表示部分退款，false表示全额退款
     */
    private function isPartialRefund(TransactionInfo $transactionInfo, array $order): bool
    {
        // 如果交易信息中有新的过期时间，且该时间晚于当前时间，则认为是部分退款
        $expiresDate = $transactionInfo->getExpiresDate();
        if ($expiresDate != null && $expiresDate > time() * 1000) {
            return true;
        }
        
        // 其他判断逻辑可以根据业务需求添加
        // 例如：检查退款金额是否小于订单金额等
        
        return false;
    }

    /**
     * 处理一次性收费通知
     * 当用户购买消耗型或非消耗型商品时触发
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleOneTimeChargeV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $transactionId = $transactionInfo->getTransactionId();
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling one time charge', [
            'transactionId' => $transactionId,
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);

        // 简单检查：如果订单已存在且已成功，直接返回
        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByTransactionId($transactionId, $appkey);
        
        if (!empty($order) && $order['payment_status'] == AppleOrder::PAYMENT_STATUS_SUCCESS) {
            // 已处理过的成功订单，直接返回
            return;
        }
        
        if (empty($order)) {
            // 如果没有找到订单，可能是用户直接在App Store购买
            // 尝试通过原始交易ID查找是否有关联的订单（用于获取用户ID）
            $uid = 0;
            if ($originalTransactionId && $originalTransactionId !== $transactionId) {
                $originalOrder = $apple_order_model->getOrderByOriginalTransactionId($originalTransactionId, $appkey);
                if (!empty($originalOrder)) {
                    $uid = $originalOrder['uid'] ?? 0;
                }else{
                    throw new \Exception('did one time charge order not found: '.$originalTransactionId.' appkey: '.$appkey);
                }
            }
            
            $this->createOrderFromNotification($transactionInfo, $renewalInfo, $appkey, $uid, $subtype);
        } else {
            // 更新现有订单状态
            $this->updateOrderFromNotification($order, $transactionInfo, $renewalInfo, $subtype);
        }
    }

    /**
     * 处理订阅通知
     * 当用户订阅产品时触发（包括首次订阅和重新订阅）
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param string|null $subtype 通知类型
     */
    private function handleSubscribedV2(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, ?string $subtype = null)
    {
        $transactionId = $transactionInfo->getTransactionId();
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        Log::channel('order')->info('handling subscribed', [
            'transactionId' => $transactionId,
            'originalTransactionId' => $originalTransactionId,
            'appkey' => $appkey
        ]);

        // 查找订单
        $apple_order_model = new AppleOrder();
        $order = $apple_order_model->getOrderByTransactionId($transactionId, $appkey);
        
        if (empty($order)) {
            // 如果没有找到订单，尝试通过原始交易ID获取用户ID
            $uid = 0;
            if ($originalTransactionId && $originalTransactionId !== $transactionId) {
                $originalOrder = $apple_order_model->getOrderByOriginalTransactionId($originalTransactionId, $appkey);
                if (!empty($originalOrder)) {
                    $uid = $originalOrder['uid'] ?? 0;
                }
            }
            
            // 创建新订单
            $this->createOrderFromNotification($transactionInfo, $renewalInfo, $appkey, $uid, $subtype);
        } else {
            // 更新订阅状态
            $update_data = [
                'subscription_status' => AppleOrder::SUBSCRIPTION_STATUS_ACTIVE,
                'auto_renew_status' => 1,
                'is_trial_period' => $this->getIsTrialPeriod($transactionInfo),
                'is_in_intro_offer_period' => $this->getIsInIntroOfferPeriod($transactionInfo),
                'auto_renew_product_id' => $renewalInfo->getAutoRenewProductId() ?? null
            ];
            
            if ($transactionInfo->getExpiresDate() != null) {
                $update_data['expires_date'] = date('Y-m-d H:i:s', $transactionInfo->getExpiresDate() / 1000);
            }
            
            $apple_order_model->updateOrderInfoByOid($order['oid'], $update_data);
        }
    }

    /**
     * 从Apple通知创建新订单
     * 用于处理用户直接在App Store购买的情况
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string $appkey 应用标识
     * @param int $uid 用户ID，续费时从原始订单获取，首次购买时为0
     * @param string|null $subtype 通知类型
     * @throws \Exception 创建失败时抛出异常
     */
    private function createOrderFromNotification(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, string $appkey, int $uid = 0, ?string $subtype = null)
    {
        $productId = $transactionInfo->getProductId();
        $transactionId = $transactionInfo->getTransactionId();
        $originalTransactionId = $transactionInfo->getOriginalTransactionId();
        
        // 获取产品信息
        $iap_product_model = new IAPProduct();
        $product = $iap_product_model->getProductInfoByAppleProductId($productId, $appkey);
        
        if (empty($product)) {
            Log::channel('order')->error('product not found for notification', [
                'productId' => $productId,
                'appkey' => $appkey
            ]);
            throw new \Exception('Product not found, productId: ' . $productId);
        }
        
        // 生成订单号
        $order_interface_config_model = new OrderInterfaceConfig();
        $order_interface_config = $order_interface_config_model->getOrderInterfaceConfigByAppKey($appkey);
        $order_id = generateOrderId(Order::PAY_CHANNEL_APPLE, $order_interface_config['oid_prefix'] ?? '');
        
        // 基于Apple交易信息判断产品类型（有过期时间的就是订阅产品）
        $isSubscriptionProduct = ($transactionInfo->getExpiresDate() !== null);
        
        // 创建订单数据
        $orderData = [
            'oid' => $order_id,
            'app_key' => $appkey,
            'uid' => $uid, // 使用传入的用户ID，续费时从原始订单获取，首次购买时为0
            'product_id' => $product['pid'],
            'apple_product_id' => $productId,
            'product_type' => AppleOrder::getAppleProductType($product),
            'amount' => $product['sale_price'],
            'transaction_id' => $transactionInfo->getTransactionId(),
            'original_transaction_id' => $transactionInfo->getOriginalTransactionId(),
            'purchase_date' => date('Y-m-d H:i:s', ($transactionInfo->getPurchaseDate() ?? time()) / 1000),
            'original_purchase_date' => $transactionInfo->getOriginalPurchaseDate() != null ? date('Y-m-d H:i:s', $transactionInfo->getOriginalPurchaseDate() / 1000) : null,
            'expires_date' => $transactionInfo->getExpiresDate() != null ? date('Y-m-d H:i:s', $transactionInfo->getExpiresDate() / 1000) : null,
            'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS,
            'data_source' => AppleOrder::DATA_SOURCE_S2S,
            'environment' => $transactionInfo->getEnvironment() ?? AppleOrder::ENVIRONMENT_PRODUCTION,
            'subscription_status' => $this->getSubscriptionStatus($transactionInfo),
            'auto_renew_status' => $this->getAutoRenewStatus($transactionInfo, $renewalInfo)
        ];
        
        // 处理订阅产品的特殊字段
        if ($isSubscriptionProduct) {
            // 处理试用期字段
            $orderData['is_trial_period'] = $this->getIsTrialPeriod($transactionInfo);
            // 处理促销期字段
            $orderData['is_in_intro_offer_period'] = $this->getIsInIntroOfferPeriod($transactionInfo);
            
            // 处理下次续费产品ID字段
            if (!empty($renewalInfo) && $renewalInfo->getAutoRenewProductId() != null) {
                $orderData['auto_renew_product_id'] = $renewalInfo->getAutoRenewProductId();
            }
        }
        
        $apple_order_model = new AppleOrder();
        $result = $apple_order_model->createOrder($orderData);
        
        if (!$result) {
            Log::channel('order')->error('failed to create order from notification', [
                'orderData' => $orderData
            ]);
            throw new \Exception('Failed to create order, oid: ' . $order_id);
        }
        
        // 创建订单成功后，如果有用户ID且有过期时间，立即执行业务逻辑
        if (!empty($orderData['uid']) && $orderData['uid'] != 0 && !empty($orderData['expires_date'])) {
            try {
                $orderBZLogicService = new OrderBZLogicService($orderData, $product);
                $orderBZLogicService->orderBZLogic();
            } catch (\Exception $e) {
                Log::channel('order')->error('failed to execute business logic for notification order', [
                    'oid' => $order_id,
                    'error' => $e->getMessage()
                ]);
                // 业务逻辑失败不影响订单创建，只记录日志
            }
        }
    }

    /**
     * 根据交易数据获取订阅状态
     * 基于Apple数据判断：有过期时间的就是订阅产品
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @return int|null 订阅状态，非订阅产品返回null
     */
    public static function getSubscriptionStatus(TransactionInfo $transactionInfo): ?int
    {
        // 基于Apple数据判断：有过期时间的就是订阅产品
        if ($transactionInfo->getExpiresDate() == null) {
            // 没有过期时间，说明不是订阅产品
            return null;
        }
        
        $expiresDate = $transactionInfo->getExpiresDate() / 1000;
        $now = time();
        
        if ($expiresDate > $now) {
            return AppleOrder::SUBSCRIPTION_STATUS_ACTIVE;  
        } else {
            return AppleOrder::SUBSCRIPTION_STATUS_EXPIRED;
        }
    }

    /**
     * 获取自动续费状态
     * 正确区分自动续期订阅和非自动续期订阅
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param array|null $product 本地产品配置（可选）
     * @return int|null 自动续费状态，非自动续期产品返回null
     */
    private function getAutoRenewStatus(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, ?array $product = null): ?int
    {
        // 1. 首先判断是否为订阅产品
        if ($transactionInfo->getExpiresDate() == null) {
            // 没有过期时间，说明不是订阅产品（消耗型/非消耗型）
            return null;
        }
        
        // 2. 有过期时间，需要区分自动续期和非自动续期订阅
        $isAutoRenewableSubscription = $this->isAutoRenewableSubscription($transactionInfo, $renewalInfo, $product);
        
        if (!$isAutoRenewableSubscription) {
            // 非自动续期订阅，不应该有auto_renew_status
            return null;
        }
        
        // 3. 确认是自动续期订阅，获取auto_renew_status
        $autoRenewStatus = null;
        
        // 优先从 renewalInfo 中获取（最准确）
        if (!empty($renewalInfo)) {
            $autoRenewStatus = $renewalInfo->getAutoRenewStatus();
            if ($autoRenewStatus !== null) {
                return (int)$autoRenewStatus;
            }
        }
        
        // 如果没有renewalInfo或renewalInfo中没有数据，根据订阅状态推断
        $expiresDate = $transactionInfo->getExpiresDate() / 1000;
        $now = time();
        
        // 如果订阅未过期，默认认为自动续费开启；已过期则关闭
        return ($expiresDate > $now) ? 1 : 0;
    }

    /**
     * 判断是否为自动续期订阅
     * 通过多种方式区分自动续期和非自动续期订阅
     */
    private function isAutoRenewableSubscription(TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, ?array $product = null): bool
    {
        // 方法1：从本地产品配置判断（最可靠）
        if (!empty($product) && isset($product['apple_product_type'])) {
            return (int)$product['apple_product_type'] === AppleOrder::PRODUCT_TYPE_AUTO_RENEWABLE;
        }
        
        // 方法2：通过RenewalInfo判断
        // 只有自动续期订阅才会有RenewalInfo
        if (!empty($renewalInfo)) {
            // 有renewalInfo说明是自动续期订阅
            return true;
        }
        
        return false;
    }

    /**
     * 获取试用期状态 - 基于Apple官方字段
     */
    private function getIsTrialPeriod(TransactionInfo $transactionInfo): int
    {
        $offerDiscountType = $transactionInfo->getOfferDiscountType();
        
        // 最精确的判断：直接检查是否为免费试用
        if ($offerDiscountType === TransactionInfo::OFFER_DISCOUNT_TYPE__FREE_TRIAL) {
            return 1;
        }
        
        // 备用判断：检查是否为介绍性优惠（但这可能包含付费的介绍性价格）
        $offerType = $transactionInfo->getOfferType();
        if ($offerType === TransactionInfo::OFFER_TYPE__INTRODUCTORY && $offerDiscountType === null) {
            // 如果是介绍性优惠但没有具体的折扣类型，可能是老版本的试用期
            return 1;
        }
        
        return 0;
    }

    /**
     * 获取促销期状态 - 基于Apple官方字段
     */
    private function getIsInIntroOfferPeriod(TransactionInfo $transactionInfo): int
    {
        // 方法1：检查 offerType 字段
        $offerType = $transactionInfo->getOfferType();
        
        // 使用Apple SDK中的常量而不是硬编码数字
        // TransactionInfo::OFFER_TYPE__PROMOTIONAL 表示促销优惠
        if ($offerType === TransactionInfo::OFFER_TYPE__PROMOTIONAL) {
            return 1; // 是促销期
        }
        
        // 方法2：检查 offerDiscountType 字段
        $offerDiscountType = $transactionInfo->getOfferDiscountType();
        // 使用Apple SDK中的常量而不是硬编码字符串
        if (in_array($offerDiscountType, [
            TransactionInfo::OFFER_DISCOUNT_TYPE__PAY_AS_YOU_GO, 
            TransactionInfo::OFFER_DISCOUNT_TYPE__PAY_UP_FRONT
        ])) {
            return 1; // 是促销期
        }
        
        return 0; // 不是促销期
    }

    /**
     * 从Apple通知更新现有订单
     * 更新订单的支付状态和订阅信息，并执行业务逻辑
     * 
     * @param array $order 现有订单数据
     * @param TransactionInfo $transactionInfo 交易信息
     * @param RenewalInfo|null $renewalInfo 续费信息
     * @param string|null $subtype 通知类型
     * @throws \Exception 更新失败时抛出异常
     */
    private function updateOrderFromNotification(array $order, TransactionInfo $transactionInfo, ?RenewalInfo $renewalInfo = null, ?string $subtype = null)
    {
        // 基于Apple交易信息判断产品类型（有过期时间的就是订阅产品）
        $isSubscriptionProduct = ($transactionInfo->getExpiresDate() !== null);
        
        $updateData = [
            'payment_status' => AppleOrder::PAYMENT_STATUS_SUCCESS,
            'subscription_status' => $this->getSubscriptionStatus($transactionInfo),
            'auto_renew_status' => $this->getAutoRenewStatus($transactionInfo, $renewalInfo),
            'original_purchase_date' => $transactionInfo->getOriginalPurchaseDate() != null ? date('Y-m-d H:i:s', $transactionInfo->getOriginalPurchaseDate() / 1000) : null,
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        if ($isSubscriptionProduct) {
            $updateData['expires_date'] = date('Y-m-d H:i:s', $transactionInfo->getExpiresDate() / 1000);
            
            // 处理试用期和促销期字段（仅订阅产品）
            $updateData['is_trial_period'] = $this->getIsTrialPeriod($transactionInfo);
            $updateData['is_in_intro_offer_period'] = $this->getIsInIntroOfferPeriod($transactionInfo);
            
            // 处理下次续费产品ID字段
            if (!empty($renewalInfo) && $renewalInfo->getAutoRenewProductId() != null) {
                $updateData['auto_renew_product_id'] = $renewalInfo->getAutoRenewProductId();
            }
        }
        
        $apple_order_model = new AppleOrder();
        $result = $apple_order_model->updateOrderInfoByOid($order['oid'], $updateData);
        
        if (!$result) {
            Log::channel('order')->error('failed to update order from notification', [
                'oid' => $order['oid'],
                'updateData' => $updateData
            ]);
            throw new \Exception('Failed to update order, oid: ' . $order['oid']);
        }
        
        // 更新订单成功后，如果有用户ID且有过期时间，执行业务逻辑
        if (!empty($order['uid']) && $order['uid'] != 0 && !empty($updateData['expires_date'])) {
            try {
                // 获取产品信息
                $iap_product_model = new IAPProduct();
                $product = $iap_product_model->getProductInfoByPid($order['product_id'], $order['app_key']);
                
                if (!empty($product)) {
                    // 合并订单数据和更新数据，用于业务逻辑处理
                    $orderForBZ = array_merge($order, $updateData);
                    $orderBZLogicService = new OrderBZLogicService($orderForBZ, $product);
                    $rs = $orderBZLogicService->orderBZLogic();
                    if($rs === false){
                        throw new \Exception('order bz logic failed oid:'.$order['oid']);
                    }
                }
            } catch (\Exception $e) {
                Log::channel('order')->error('failed to execute business logic for updated order', [
                    'oid' => $order['oid'],
                    'error' => $e->getMessage()
                ]);
                // 业务逻辑失败不影响订单更新，只记录日志
            }
        }
    }

    /**
     * 检测是否为家庭共享购买
     * 基于交易信息判断是否为家庭共享场景
     * 
     * @param TransactionInfo $transactionInfo 交易信息
     * @return bool true表示家庭共享购买
     */
    private function isSharedPurchase(TransactionInfo $transactionInfo): bool
    {
        // Apple在家庭共享场景下，会在交易信息中包含特定标识
        // 这里需要根据Apple官方文档的具体实现来判断
        
        // 方法1：检查是否有家庭共享相关的字段
        // 注意：这个方法需要根据实际的Apple API响应来调整
        try {
            // 如果有家庭共享标识字段，可以在这里检查
            // 例如：$transactionInfo->getInAppOwnershipType() 等
            
            // 方法2：通过购买者ID和原始购买者ID的差异来判断
            // 在家庭共享中，这两个ID可能不同
            
            return false; // 默认返回false，需要根据实际情况调整
        } catch (\Exception $e) {
            Log::channel('order')->warning('failed to detect family sharing', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

}