<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use DefaultDatetimeFormat;
    protected $table = 'orders';

    protected $primaryKey = 'oid';

    public $incrementing = false;

    const STATUS_PENDING = 1;
    const STATUS_PAID = 2;
    const STATUS_REFUNDING = 3;
    const STATUS_REFUNDED = 4;
    const STATUS_PAYMENT_FAILED = 5;
    const STATUS_REFUND_FAILED = 6;

    public static $statusMap = [
        self::STATUS_PENDING => '待支付',
        self::STATUS_PAID => '已支付',
        self::STATUS_REFUNDING => '退款中',
        self::STATUS_REFUNDED => '已退款',
        self::STATUS_PAYMENT_FAILED => '支付失败',
        self::STATUS_REFUND_FAILED => '退款失败',
    ];

    const PAY_CHANNEL_WECHAT = 1;
    const PAY_CHANNEL_ALIPAY = 2;
    const PAY_CHANNEL_APPLE = 3;

    public static $refundChannelMap = [
        'ORIGINAL' => '原路退款',
        'BALANCE' => '退款到余额',
        'OTHER_BALANCE' => '原账户异常退到其他余额账户',
        'OTHER_BANKCARD' => '原银行卡异常退到其他银行卡',
    ];

    public static $payChannelMap = [
        self::PAY_CHANNEL_WECHAT => '微信',
        self::PAY_CHANNEL_ALIPAY => '支付宝',
        self::PAY_CHANNEL_APPLE => '苹果',
    ];

    const REFUND_TYPE_ORIGINAL = 1;
    const REFUND_TYPE_ONLY = 2;

    public static $refundTypeMap = [
        self::REFUND_TYPE_ORIGINAL => '退款并退功能',
        self::REFUND_TYPE_ONLY => '仅退款',
    ];


    /**
     * CREATE TABLE `orders` (
     * `oid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
     * `tenant_id` bigint unsigned NOT NULL,
     * `app_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
     * `uid` bigint unsigned NOT NULL,
     * `product_id` int unsigned NOT NULL,
     * `product_price` int unsigned NOT NULL COMMENT '产品价格',
     * `discount_amount` int unsigned NOT NULL DEFAULT '0' COMMENT '优惠金额',
     * `order_amount` int unsigned NOT NULL COMMENT '订单金额',
     * `payment_amount` int unsigned DEFAULT NULL COMMENT '实际支付金额',
     * `platform_order_amount` int DEFAULT NULL COMMENT '支付平台订单金额',
     * `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1为待支付，2为已支付，3为已退款，4为支付失败',
     * `pay_channel` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1为微信，2为支付宝，3为苹果',
     * `tid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方订单号',
     * `trade_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '交易类型',
     * `bank_type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '银行类型',
     * `refund_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方退款ID',
     * `refund_type` tinyint unsigned DEFAULT NULL COMMENT '1退款退功能，2仅退款',
     * `refund_amount` int unsigned DEFAULT NULL,
     * `refund_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     * `open_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方支付用户标识',
     * `channel` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'official' COMMENT '来源渠道',
     * `pay_time` timestamp NULL DEFAULT NULL COMMENT '支付时间',
     * `refund_send_time` timestamp NULL DEFAULT NULL,
     * `refund_time` timestamp NULL DEFAULT NULL,
     * `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
     * `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
     * PRIMARY KEY (`oid`),
     * KEY `idx_uid` (`uid`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
     *
     * @var array
     */
    protected $fillable = [
        'oid',
        'tenant_id',
        'app_key',
        'uid',
        'product_id',
        'product_price',
        'discount_amount',
        'order_amount',
        'payment_amount',
        'platform_order_amount',
        'status',
        'prepay_id',
        'pay_channel',
        'tid',
        'trade_type',
        'bank_type',
        'open_id',
        'channel',
        'pay_time',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'pid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
} 
