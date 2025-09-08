<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Readdle\AppStoreServerAPI\Environment;

/**
 * CREATE TABLE `apple_orders` (
 * `oid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内部订单号',
 * `app_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用标识',
 * `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID',
 * `product_id` int(10) unsigned NOT NULL COMMENT '内部产品ID',
 * `apple_product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '苹果产品标识符',
 * `product_type` tinyint(3) unsigned NOT NULL COMMENT '产品类型：1=消耗型(consumable)，2=非消耗型(non_consumable)，3=自动续期订阅(auto_renewable_subscription)，4=非续期订阅(non_renewing_subscription)',
 * `amount` int(10) unsigned NOT NULL COMMENT '订单金额(分)',
 * `payment_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '支付状态：1=待验证，2=支付成功，3=支付失败，4=已退款',
 * `subscription_status` tinyint(3) unsigned DEFAULT NULL COMMENT '订阅状态：1=活跃，2=已过期，3=已取消，4=宽限期，5=计费重试',
 * `transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果交易ID',
 * `original_transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '原始交易ID(订阅关联标识)',
 * `environment` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '环境：sandbox或production',
 * `is_trial_period` tinyint(1) DEFAULT NULL COMMENT '是否试用期：0=否，1=是',
 * `is_in_intro_offer_period` tinyint(1) DEFAULT NULL COMMENT '是否促销期：0=否，1=是',
 * `expires_date` timestamp NULL DEFAULT NULL COMMENT '订阅过期时间',
 * `auto_renew_status` tinyint(1) DEFAULT NULL COMMENT '自动续订状态：0=关闭，1=开启',
 * `auto_renew_product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '下一周期续订的产品ID',
 * `purchase_date` timestamp NULL DEFAULT NULL COMMENT '购买时间',
 * `original_purchase_date` timestamp NULL DEFAULT NULL COMMENT '原始购买时间',
 * `cancellation_date` timestamp NULL DEFAULT NULL COMMENT '取消时间(退款时苹果返回)',
 * `data_source` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '数据来源：1=Receipt验证，2=S2S通知',
 * `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
 * `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
 * PRIMARY KEY (`oid`),
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果支付订单表';
 */
class AppleOrder extends Model
{
    protected $table = 'apple_orders';

    protected $primaryKey = 'oid';

    public $timestamps = false;

    public $incrementing = false;

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
    const PRODUCT_TYPE_CONSUMABLE = 1;               // 消耗型
    const PRODUCT_TYPE_NON_CONSUMABLE = 2;           // 非消耗型
    const PRODUCT_TYPE_AUTO_RENEWABLE = 3;           // 自动续期订阅
    const PRODUCT_TYPE_NON_RENEWING = 4;             // 非续期订阅

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
        'data_source', 
    ];

    public function product()
    {
        return $this->belongsTo(IAPProduct::class, 'product_id', 'pid')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}

