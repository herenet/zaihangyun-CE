<?php

namespace app\model;

use support\Model;
use Readdle\AppStoreServerAPI\Environment;
use Readdle\AppStoreServerAPI\TransactionInfo;

/**
 * CREATE TABLE `apple_orders` (
 *  `oid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内部订单号',
 *  `app_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用标识',
 *  `uid` bigint unsigned NOT NULL COMMENT '用户ID',
 *  `product_id` int unsigned NOT NULL COMMENT '内部产品ID',
 *  `apple_product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '苹果产品标识符',
 *  `product_type` tinyint unsigned NOT NULL COMMENT '产品类型：1=消耗型(consumable)，2=非消耗型(non_consumable)，3=自动续期订阅(auto_renewable_subscription)，4=非续期订阅(non_renewing_subscription)',
 *  `amount` int unsigned NOT NULL COMMENT '订单金额(分)',
 *  `payment_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '支付状态：1=待验证，2=支付成功，3=支付失败，4=已退款',
 *  `subscription_status` tinyint unsigned DEFAULT NULL COMMENT '订阅状态：1=活跃，2=已过期，3=已取消，4=宽限期，5=计费重试',
 *  `transaction_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果交易ID',
 *  `original_transaction_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '原始交易ID(订阅关联标识)',
 *  `environment` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '环境：sandbox或production',
 *  `is_trial_period` tinyint(1) DEFAULT NULL COMMENT '是否试用期：0=否，1=是',
 *  `is_in_intro_offer_period` tinyint(1) DEFAULT NULL COMMENT '是否促销期：0=否，1=是',
 *  `expires_date` timestamp NULL DEFAULT NULL COMMENT '订阅过期时间',
 *  `grace_period_expires_date` timestamp NULL DEFAULT NULL COMMENT '宽限期过期时间',
 *  `auto_renew_status` tinyint(1) DEFAULT NULL COMMENT '自动续订状态：0=关闭，1=开启',
 *  `auto_renew_product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '下一周期续订的产品ID',
 *  `purchase_date` timestamp NULL DEFAULT NULL COMMENT '购买时间',
 *  `original_purchase_date` timestamp NULL DEFAULT NULL COMMENT '原始购买时间',
 *  `cancellation_date` timestamp NULL DEFAULT NULL COMMENT '取消时间(退款时苹果返回)',
 *  `data_source` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '数据来源：1=Receipt验证，2=S2S通知',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
 *  PRIMARY KEY (`oid`),
 *  UNIQUE KEY `uk_app_transaction` (`app_key`,`transaction_id`),
 *  KEY `idx_app_original_transaction` (`app_key`,`original_transaction_id`),
 *  KEY `idx_uid` (`uid`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果支付订单表';
 */
class AppleOrder extends Model
{
    protected $table = 'apple_orders';
    
    protected $primaryKey = 'oid';

    public $incrementing = false;

    public $timestamps = false;

    // 支付状态
    const PAYMENT_STATUS_PENDING = 1;        // 待验证
    const PAYMENT_STATUS_SUCCESS = 2;        // 支付成功
    const PAYMENT_STATUS_FAILED = 3;         // 支付失败
    const PAYMENT_STATUS_REFUNDED = 4;       // 已退款

    // 订阅状态（仅订阅类型产品有效）
    const SUBSCRIPTION_STATUS_ACTIVE = 1;           // 活跃
    const SUBSCRIPTION_STATUS_EXPIRED = 2;          // 已过期
    const SUBSCRIPTION_STATUS_CANCELED = 3;         // 已取消
    const SUBSCRIPTION_STATUS_GRACE_PERIOD = 4;     // 宽限期
    const SUBSCRIPTION_STATUS_BILLING_RETRY = 5;    // 计费重试

    // 产品类型
    const PRODUCT_TYPE_CONSUMABLE = IAPProduct::PRODUCT_TYPE_CONSUMABLE;               // 消耗型
    const PRODUCT_TYPE_NON_CONSUMABLE = IAPProduct::PRODUCT_TYPE_NON_CONSUMABLE;           // 非消耗型
    const PRODUCT_TYPE_AUTO_RENEWABLE = IAPProduct::PRODUCT_TYPE_AUTO_RENEWABLE;           // 自动续期订阅
    const PRODUCT_TYPE_NON_RENEWING = IAPProduct::PRODUCT_TYPE_NON_RENEWING;             // 非续期订阅

    // 数据来源
    const DATA_SOURCE_RECEIPT = 1;           // Receipt验证
    const DATA_SOURCE_S2S = 2;               // S2S通知

    // 环境
    const ENVIRONMENT_SANDBOX = Environment::SANDBOX;     // 沙盒环境
    const ENVIRONMENT_PRODUCTION = Environment::PRODUCTION; // 生产环境

    // 支付状态映射
    public static $paymentStatusMap = [
        self::PAYMENT_STATUS_PENDING => '待验证',
        self::PAYMENT_STATUS_SUCCESS => '支付成功',
        self::PAYMENT_STATUS_FAILED => '支付失败',
        self::PAYMENT_STATUS_REFUNDED => '已退款',
    ];

    // 订阅状态映射
    public static $subscriptionStatusMap = [
        self::SUBSCRIPTION_STATUS_ACTIVE => '活跃',
        self::SUBSCRIPTION_STATUS_EXPIRED => '已过期',
        self::SUBSCRIPTION_STATUS_CANCELED => '已取消',
        self::SUBSCRIPTION_STATUS_GRACE_PERIOD => '宽限期',
        self::SUBSCRIPTION_STATUS_BILLING_RETRY => '计费重试',
    ];

    // 产品类型映射
    public static $productTypeMap = [
        self::PRODUCT_TYPE_CONSUMABLE => '消耗型',
        self::PRODUCT_TYPE_NON_CONSUMABLE => '非消耗型',
        self::PRODUCT_TYPE_AUTO_RENEWABLE => '自动续期订阅',
        self::PRODUCT_TYPE_NON_RENEWING => '非续期订阅',
    ];

    // 数据来源映射
    public static $dataSourceMap = [
        self::DATA_SOURCE_RECEIPT => 'Receipt验证',
        self::DATA_SOURCE_S2S => 'S2S通知',
    ];

    // 环境映射
    public static $environmentMap = [
        self::ENVIRONMENT_SANDBOX => '沙盒环境',
        self::ENVIRONMENT_PRODUCTION => '生产环境',
    ];

    // Apple通知类型映射
    public static $notificationTypeMap = [
        'CONSUMPTION_REQUEST' => '消耗数据请求',
        'DID_CHANGE_RENEWAL_PREF' => '续费偏好变更',
        'DID_CHANGE_RENEWAL_STATUS' => '续费状态变更',
        'DID_FAIL_TO_RENEW' => '续费失败',
        'DID_RENEW' => '续费成功',
        'EXPIRED' => '订阅过期',
        'GRACE_PERIOD_EXPIRED' => '宽限期过期',
        'OFFER_REDEEMED' => '优惠兑换',
        'PRICE_INCREASE' => '价格上涨',
        'REFUND' => '退款',
        'REFUND_DECLINED' => '退款被拒绝',
        'REFUND_REVERSED' => '退款被撤销',
        'RENEWAL_EXTENDED' => '续费延期',
        'RENEWAL_EXTENSION' => '批量续费延期',
        'REVOKE' => '撤销',
        'SUBSCRIBED' => '订阅成功',
        'ONE_TIME_CHARGE' => '一次性收费',
        'TEST' => '测试通知',
    ];

    // Apple通知子类型映射
    public static $notificationSubtypeMap = [
        'ACCEPTED' => '已接受',
        'AUTO_RENEW_DISABLED' => '自动续费已关闭',
        'AUTO_RENEW_ENABLED' => '自动续费已开启',
        'BILLING_RECOVERY' => '计费恢复',
        'BILLING_RETRY' => '计费重试',
        'DOWNGRADE' => '降级',
        'FAILURE' => '失败',
        'GRACE_PERIOD' => '宽限期',
        'INITIAL_BUY' => '首次购买',
        'PENDING' => '待处理',
        'PRICE_INCREASE' => '价格上涨',
        'PRODUCT_NOT_FOR_SALE' => '产品不可售',
        'RESUBSCRIBE' => '重新订阅',
        'SUMMARY' => '摘要',
        'UPGRADE' => '升级',
        'VOLUNTARY' => '主动取消',
    ];

    // 通知类型详细描述映射
    public static $notificationTypeDescriptionMap = [
        'CONSUMPTION_REQUEST' => '苹果请求提供消耗型商品的消耗数据',
        'DID_CHANGE_RENEWAL_PREF' => '用户更改了订阅计划或产品',
        'DID_CHANGE_RENEWAL_STATUS' => '用户更改了订阅续费状态（开启/关闭自动续费）',
        'DID_FAIL_TO_RENEW' => '订阅续费失败，可能进入宽限期或计费重试',
        'DID_RENEW' => '订阅续费成功，用户可继续享受服务',
        'EXPIRED' => '订阅已过期，用户失去服务权限',
        'GRACE_PERIOD_EXPIRED' => '宽限期已结束，订阅彻底过期',
        'OFFER_REDEEMED' => '用户兑换了促销优惠码或优惠',
        'PRICE_INCREASE' => '订阅产品价格上涨通知',
        'REFUND' => '苹果已处理退款，用户权益需要撤销',
        'REFUND_DECLINED' => '退款请求被苹果拒绝',
        'REFUND_REVERSED' => '之前的退款被撤销，需要恢复用户权益',
        'RENEWAL_EXTENDED' => '特定订阅的续费期限被延长',
        'RENEWAL_EXTENSION' => '批量续费延期操作的状态通知',
        'REVOKE' => '通过家庭共享获得的权益被撤销',
        'SUBSCRIBED' => '用户成功订阅产品（首次或重新订阅）',
        'ONE_TIME_CHARGE' => '用户购买了一次性商品（消耗型/非消耗型/非续期订阅）',
        'TEST' => '苹果发送的测试通知，用于验证回调接口',
    ];

    // 子类型详细描述映射
    public static $notificationSubtypeDescriptionMap = [
        'ACCEPTED' => '用户已接受价格上涨',
        'AUTO_RENEW_DISABLED' => '用户关闭了自动续费或苹果因退款关闭了自动续费',
        'AUTO_RENEW_ENABLED' => '用户重新开启了自动续费',
        'BILLING_RECOVERY' => '之前续费失败的订阅现在续费成功了',
        'BILLING_RETRY' => '订阅因计费重试期结束而过期',
        'DOWNGRADE' => '用户降级了订阅或跨级到不同时长的订阅',
        'FAILURE' => '批量续费延期中特定订阅操作失败',
        'GRACE_PERIOD' => '订阅续费失败但仍在宽限期内',
        'INITIAL_BUY' => '用户首次购买或通过家庭共享首次获得权限',
        'PENDING' => '价格上涨通知已发送但用户尚未响应',
        'PRICE_INCREASE' => '订阅因用户未同意价格上涨而过期',
        'PRODUCT_NOT_FOR_SALE' => '订阅因产品在续费时不可售而过期',
        'RESUBSCRIBE' => '用户重新订阅或通过家庭共享重新获得权限',
        'SUMMARY' => '批量续费延期操作完成的摘要',
        'UPGRADE' => '用户升级了订阅或跨级到相同时长的订阅',
        'VOLUNTARY' => '用户主动关闭自动续费导致订阅过期',
    ];

    protected $fillable = [
        'oid',
        'app_key',
        'uid',
        'product_id',
        'apple_product_id',
        'product_type',
        'amount',
        'payment_status',
        'subscription_status',
        'transaction_id',
        'original_transaction_id',
        'environment',
        'is_trial_period',
        'is_in_intro_offer_period',
        'expires_date',
        'auto_renew_status',
        'auto_renew_product_id',
        'purchase_date',
        'original_purchase_date',
        'cancellation_date',
        'data_source'
    ];

    /**
     * 创建订单
     * @param array $data
     * @return mixed
     */
    public function createOrder(array $data)
    {
        $params = [
            'oid' => $data['oid'],
            'app_key' => $data['app_key'],
            'uid' => $data['uid'],
            'product_id' => $data['product_id'],
            'apple_product_id' => $data['apple_product_id'],
            'product_type' => $data['product_type'],
            'amount' => $data['amount'],
            'payment_status' => $data['payment_status'] ?? self::PAYMENT_STATUS_PENDING,
            'environment' => $data['environment'],
            'data_source' => $data['data_source'] ?? self::DATA_SOURCE_RECEIPT,
        ];
        
        // 可选字段
        $optionalFields = [
            'subscription_status',
            'transaction_id',
            'original_transaction_id',
            'is_trial_period',
            'is_in_intro_offer_period',
            'expires_date',
            'auto_renew_status',
            'auto_renew_product_id',
            'purchase_date',
            'original_purchase_date',
            'cancellation_date'
        ];
        
        foreach ($optionalFields as $field) {
            if (isset($data[$field])) {
                $params[$field] = $data[$field];
            }
        }
        
        return $this->create($params);
    }

    /**
     * 根据订单号获取订单信息
     * @param string $oid
     * @return array
     */
    public function getOrderInfoByOid(string $oid)
    {
        $rs = $this->where('oid', $oid)->first();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 根据订单号和用户ID获取订单信息
     * @param string $oid
     * @param string $uid
     * @return array
     */
    public function getOrderInfoByOidAndUid(string $oid, string $uid)
    {
        $rs = $this->where(['oid' => $oid, 'uid' => $uid])->first();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    public function deleteOrder(string $oid, string $appKey)
    {
        return $this->where(['oid' => $oid, 'app_key' => $appKey])->delete();
    }

    /**
     * 更新订单信息
     * @param string $oid
     * @param array $data
     * @return mixed
     */
    public function updateOrderInfoByOid(string $oid, array $data)
    {
        return $this->where('oid', $oid)->update($data);
    }

    /**
     * 获取用户订单列表
     * @param string $uid
     * @param int|null $paymentStatus
     * @param int|null $subscriptionStatus
     * @param int $limit
     * @return array
     */
    public function getOrdersByUid(string $uid, $paymentStatus = null, $subscriptionStatus = null, $limit = 10)
    {
        $query = $this->where('uid', $uid);
        
        if(!is_null($paymentStatus)){
            $query->where('payment_status', $paymentStatus);
        }
        
        if(!is_null($subscriptionStatus)){
            $query->where('subscription_status', $subscriptionStatus);
        }
        
        $rs = $query->orderBy('created_at', 'desc')->limit($limit)->get();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 根据交易ID获取订单
     * @param string $transactionId
     * @param string $appKey
     * @return array
     */
    public function getOrderByTransactionId(string $transactionId, string $appKey)
    {
        if(empty($transactionId)){
            return [];
        }
        $rs = $this->where(['transaction_id' => $transactionId, 'app_key' => $appKey])->first();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 根据原始交易ID获取订单
     * @param string $originalTransactionId
     * @param string $appKey
     * @return array
     */
    public function getOrderByOriginalTransactionId(string $originalTransactionId, string $appKey)
    {
        if(empty($originalTransactionId)){
            return [];
        }
        $rs = $this->where(['original_transaction_id' => $originalTransactionId, 'app_key' => $appKey])->first();
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 获取订阅相关订单
     * @param string $originalTransactionId
     * @param string $tenantId
     * @param string $appKey
     * @return array
     */
    public function getSubscriptionOrders(string $originalTransactionId, string $tenantId, string $appKey)
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
     * 获取用户当前有效的订阅
     * @param string $uid
     * @param string $appKey
     * @return array
     */
    public function getUserActiveSubscriptions(string $uid, string $tenantId, string $appKey)
    {
        $rs = $this->where([
            'uid' => $uid,
            'app_key' => $appKey,
            'product_type' => self::PRODUCT_TYPE_AUTO_RENEWABLE,
            'subscription_status' => self::SUBSCRIPTION_STATUS_ACTIVE
        ])->where('expires_date', '>', date('Y-m-d H:i:s'))
          ->orderBy('expires_date', 'desc')
          ->get();
        
        if(empty($rs)){
            return [];
        }
        return $rs->toArray();
    }

    /**
     * 检查是否为订阅类型产品
     * @param int $productType
     * @return bool
     */
    public static function isSubscriptionProduct(int $productType): bool
    {
        return in_array($productType, [
            self::PRODUCT_TYPE_AUTO_RENEWABLE,
            self::PRODUCT_TYPE_NON_RENEWING
        ]);
    }

     /**
     * 获取Apple产品类型
     * 根据产品信息判断Apple产品的类型（消耗型、非消耗型、订阅等）
     * 
     * @param array $product 产品信息数组
     * @return int Apple产品类型常量
     */
    public static function getAppleProductType(array $product): int
    {
        // 根据产品信息判断类型，这里需要根据实际的产品表结构来实现
        // 假设产品表中有 product_type 字段
        if (isset($product['apple_product_type'])) {
            return (int)$product['apple_product_type'];
        }
        
        // 默认返回消耗型产品
        return AppleOrder::PRODUCT_TYPE_CONSUMABLE;
    }

    /**
     * 获取支付状态文本
     * @param int $status
     * @return string
     */
    public static function getPaymentStatusText(int $status): string
    {
        return self::$paymentStatusMap[$status] ?? '未知状态';
    }

    /**
     * 获取订阅状态文本
     * @param int|null $status
     * @return string
     */
    public static function getSubscriptionStatusText($status): string
    {
        if (is_null($status)) {
            return '非订阅';
        }
        return self::$subscriptionStatusMap[$status] ?? '未知状态';
    }

    /**
     * 获取产品类型文本
     * @param int $type
     * @return string
     */
    public static function getProductTypeText(int $type): string
    {
        return self::$productTypeMap[$type] ?? '未知类型';
    }

    /**
     * 获取通知类型文本
     * @param string $notificationType
     * @return string
     */
    public static function getNotificationTypeText(string $notificationType): string
    {
        return self::$notificationTypeMap[$notificationType] ?? '未知通知类型';
    }

    /**
     * 获取通知子类型文本
     * @param string|null $subtype
     * @return string
     */
    public static function getNotificationSubtypeText(?string $subtype): string
    {
        if (is_null($subtype)) {
            return '无子类型';
        }
        return self::$notificationSubtypeMap[$subtype] ?? '未知子类型';
    }

    /**
     * 获取通知类型详细描述
     * @param string $notificationType
     * @return string
     */
    public static function getNotificationTypeDescription(string $notificationType): string
    {
        return self::$notificationTypeDescriptionMap[$notificationType] ?? '未知通知类型描述';
    }

    /**
     * 获取通知子类型详细描述
     * @param string|null $subtype
     * @return string
     */
    public static function getNotificationSubtypeDescription(?string $subtype): string
    {
        if (is_null($subtype)) {
            return '无子类型描述';
        }
        return self::$notificationSubtypeDescriptionMap[$subtype] ?? '未知子类型描述';
    }

    /**
     * 获取完整的通知描述（类型 + 子类型）
     * @param string $notificationType
     * @param string|null $subtype
     * @return string
     */
    public static function getFullNotificationDescription(string $notificationType, ?string $subtype = null): string
    {
        $typeText = self::getNotificationTypeText($notificationType);
        $subtypeText = $subtype ? ' - ' . self::getNotificationSubtypeText($subtype) : '';
        
        return $typeText . $subtypeText;
    }

    /**
     * 获取通知的业务含义描述
     * @param string $notificationType
     * @param string|null $subtype
     * @return string
     */
    public static function getNotificationBusinessDescription(string $notificationType, ?string $subtype = null): string
    {
        $typeDesc = self::getNotificationTypeDescription($notificationType);
        $subtypeDesc = $subtype ? '，' . self::getNotificationSubtypeDescription($subtype) : '';
        
        return $typeDesc . $subtypeDesc;
    }
}