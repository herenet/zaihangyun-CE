<?php

namespace app\model;

use support\Model;

class AppleNotification extends Model
{
    protected $table = 'apple_notifications';
    
    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = false;

    // 处理状态
    const PROCESSED_NO = 0;     // 未处理
    const PROCESSED_YES = 1;    // 已处理

    // 常见的通知类型
    const NOTIFICATION_TYPE_DID_RENEW = 'DID_RENEW';                        // 订阅续费
    const NOTIFICATION_TYPE_DID_FAIL_TO_RENEW = 'DID_FAIL_TO_RENEW';        // 续费失败
    const NOTIFICATION_TYPE_REFUND = 'REFUND';                              // 退款
    const NOTIFICATION_TYPE_PRICE_INCREASE = 'PRICE_INCREASE';              // 价格上涨
    const NOTIFICATION_TYPE_INTERACTIVE_RENEWAL = 'INTERACTIVE_RENEWAL';    // 交互式续费
    const NOTIFICATION_TYPE_DID_CHANGE_RENEWAL_PREF = 'DID_CHANGE_RENEWAL_PREF'; // 更改续费偏好
    const NOTIFICATION_TYPE_DID_CHANGE_RENEWAL_STATUS = 'DID_CHANGE_RENEWAL_STATUS'; // 更改续费状态
    const NOTIFICATION_TYPE_OFFER_REDEEMED = 'OFFER_REDEEMED';              // 优惠兑换
    const NOTIFICATION_TYPE_SUBSCRIBED = 'SUBSCRIBED';                      // 订阅
    const NOTIFICATION_TYPE_EXPIRED = 'EXPIRED';                            // 过期
    const NOTIFICATION_TYPE_GRACE_PERIOD_EXPIRED = 'GRACE_PERIOD_EXPIRED';  // 宽限期过期
    const NOTIFICATION_TYPE_REVOKE = 'REVOKE';                              // 撤销
    const NOTIFICATION_TYPE_RENEWAL_EXTENDED = 'RENEWAL_EXTENDED';           // 续费延期
    const NOTIFICATION_TYPE_ONE_TIME_CHARGE = 'ONE_TIME_CHARGE';             // 一次性收费
    const NOTIFICATION_TYPE_TEST = 'TEST';                                   // 测试通知

    // 处理状态映射
    public static $processedMap = [
        self::PROCESSED_NO => '未处理',
        self::PROCESSED_YES => '已处理',
    ];

    // 通知类型映射
    public static $notificationTypeMap = [
        self::NOTIFICATION_TYPE_DID_RENEW => '订阅续费',
        self::NOTIFICATION_TYPE_DID_FAIL_TO_RENEW => '续费失败',
        self::NOTIFICATION_TYPE_REFUND => '退款',
        self::NOTIFICATION_TYPE_PRICE_INCREASE => '价格上涨',
        self::NOTIFICATION_TYPE_INTERACTIVE_RENEWAL => '交互式续费',
        self::NOTIFICATION_TYPE_DID_CHANGE_RENEWAL_PREF => '更改续费偏好',
        self::NOTIFICATION_TYPE_DID_CHANGE_RENEWAL_STATUS => '更改续费状态',
        self::NOTIFICATION_TYPE_OFFER_REDEEMED => '优惠兑换',
        self::NOTIFICATION_TYPE_SUBSCRIBED => '订阅',
        self::NOTIFICATION_TYPE_EXPIRED => '过期',
        self::NOTIFICATION_TYPE_GRACE_PERIOD_EXPIRED => '宽限期过期',
        self::NOTIFICATION_TYPE_REVOKE => '撤销',
        self::NOTIFICATION_TYPE_RENEWAL_EXTENDED => '续费延期',
        self::NOTIFICATION_TYPE_ONE_TIME_CHARGE => '一次性收费',
        self::NOTIFICATION_TYPE_TEST => '测试通知',
    ];

    protected $fillable = [
        'app_key',
        'notification_uuid',
        'notification_type',
        'subtype',
        'transaction_id',
        'original_transaction_id',
        'environment',
        'notification_data',
        'processed',
        'process_result'
    ];

    /**
     * 创建通知记录
     * @param array $data
     * @return mixed
     */
    public function createNotification(array $data)
    {
        $params = [
            'app_key' => $data['app_key'],
            'notification_uuid' => $data['notification_uuid'],
            'notification_type' => $data['notification_type'],
            'environment' => $data['environment'],
            'notification_data' => is_array($data['notification_data']) ? json_encode($data['notification_data']) : $data['notification_data'],
            'processed' => $data['processed'] ?? self::PROCESSED_NO,
        ];

        // 可选字段
        $optionalFields = [
            'subtype',
            'transaction_id',
            'original_transaction_id',
            'process_result'
        ];

        foreach ($optionalFields as $field) {
            if (isset($data[$field])) {
                $params[$field] = $data[$field];
            }
        }

        return $this->create($params);
    }

    /**
     * 根据ID获取通知信息
     * @param int $id
     * @return array
     */
    public function getNotificationById(int $id)
    {
        $rs = $this->where('id', $id)->first();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 更新通知处理状态
     * @param int $id
     * @param int $processed
     * @param string|null $processResult
     * @return mixed
     */
    public function updateProcessStatus(int $id, int $processed, string $processResult = null)
    {
        $data = ['processed' => $processed];
        if (!is_null($processResult)) {
            $data['process_result'] = $processResult;
        }
        return $this->where('id', $id)->update($data);
    }

    /**
     * 获取未处理的通知列表
     * @param string|null $appKey
     * @param int $limit
     * @return array
     */
    public function getUnprocessedNotifications(string $appKey = null, int $limit = 100)
    {
        $query = $this->where('processed', self::PROCESSED_NO);
        
        if (!is_null($appKey)) {
            $query->where('app_key', $appKey);
        }
        
        $rs = $query->orderBy('created_at', 'asc')->limit($limit)->get();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 根据交易ID获取通知列表
     * @param string $transactionId
     * @param string $appKey
     * @return array
     */
    public function getNotificationsByTransactionId(string $transactionId, string $appKey)
    {
        $rs = $this->where([
            'transaction_id' => $transactionId,
            'app_key' => $appKey
        ])->orderBy('created_at', 'desc')->get();
        
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 根据原始交易ID获取通知列表
     * @param string $originalTransactionId
     * @param string $appKey
     * @return array
     */
    public function getNotificationsByOriginalTransactionId(string $originalTransactionId, string $appKey)
    {
        $rs = $this->where([
            'original_transaction_id' => $originalTransactionId,
            'app_key' => $appKey
        ])->orderBy('created_at', 'desc')->get();
        
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 根据通知类型获取通知列表
     * @param string $notificationType
     * @param string $appKey
     * @param int $limit
     * @return array
     */
    public function getNotificationsByType(string $notificationType, string $appKey, int $limit = 100)
    {
        $rs = $this->where([
            'notification_type' => $notificationType,
            'app_key' => $appKey
        ])->orderBy('created_at', 'desc')->limit($limit)->get();
        
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 解析通知数据
     * @param string $notificationData
     * @return array
     */
    public static function parseNotificationData(string $notificationData): array
    {
        $data = json_decode($notificationData, true);
        return is_array($data) ? $data : [];
    }

    /**
     * 获取处理状态文本
     * @param int $processed
     * @return string
     */
    public static function getProcessedText(int $processed): string
    {
        return self::$processedMap[$processed] ?? '未知状态';
    }

    /**
     * 获取通知类型文本
     * @param string $notificationType
     * @return string
     */
    public static function getNotificationTypeText(string $notificationType): string
    {
        return self::$notificationTypeMap[$notificationType] ?? $notificationType;
    }

    /**
     * 检查是否为订阅相关通知
     * @param string $notificationType
     * @return bool
     */
    public static function isSubscriptionNotification(string $notificationType): bool
    {
        $subscriptionTypes = [
            self::NOTIFICATION_TYPE_DID_RENEW,
            self::NOTIFICATION_TYPE_DID_FAIL_TO_RENEW,
            self::NOTIFICATION_TYPE_DID_CHANGE_RENEWAL_PREF,
            self::NOTIFICATION_TYPE_DID_CHANGE_RENEWAL_STATUS,
            self::NOTIFICATION_TYPE_SUBSCRIBED,
            self::NOTIFICATION_TYPE_EXPIRED,
            self::NOTIFICATION_TYPE_GRACE_PERIOD_EXPIRED,
            self::NOTIFICATION_TYPE_RENEWAL_EXTENDED,
        ];
        
        return in_array($notificationType, $subscriptionTypes);
    }
}