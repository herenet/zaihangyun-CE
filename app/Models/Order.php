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

    public static $statusMap = [
        1 => '待支付',
        2 => '已支付',
        3 => '已退款',
        4 => '支付失败',
    ];

    public static $payChannelMap = [
        1 => '微信',
        2 => '支付宝',
        3 => '苹果',
    ];


    /**
     * CREATE TABLE `order` (
  `oid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `app_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uid` bigint unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `product_price` int unsigned NOT NULL COMMENT '产品价格',
  `discount_amount` int unsigned NOT NULL DEFAULT '0' COMMENT '优惠金额',
  `order_amount` int unsigned NOT NULL COMMENT '订单金额',
  `payment_amount` int unsigned DEFAULT NULL COMMENT '实际支付金额',
  `platform_order_amount` int DEFAULT NULL COMMENT '支付平台订单金额',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1为待支付，2为已支付，3为已退款，4为支付失败',
  `prepay_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '预支付ID',
  `pay_channel` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1为微信，2为支付宝，3为苹果',
  `tid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方订单号',
  `trade_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '交易类型',
  `bank_type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '银行类型',
  `open_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方支付用户标识',
  `channel` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'official' COMMENT '来源渠道',
  `pay_time` timestamp NULL DEFAULT NULL COMMENT '支付时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`oid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
