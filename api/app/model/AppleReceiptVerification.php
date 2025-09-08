<?php

namespace app\model;

use support\Model;


/**
 * CREATE TABLE `apple_receipt_verifications` (
 *  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
 *  `app_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用标识',
 *  `receipt_data_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '票据数据SHA256哈希',
 *  `verification_status` tinyint unsigned NOT NULL COMMENT '验证状态：1=成功，2=失败',
 *  `apple_status_code` int DEFAULT NULL COMMENT '苹果返回的状态码',
 *  `error_message` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '错误信息',
 *  `bundle_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '应用Bundle ID',
 *  `environment` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '环境：sandbox或production',
 *  `transaction_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '交易ID',
 *  `original_transaction_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '原始交易ID',
 *  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '产品ID',
 *  `purchase_date` timestamp NULL DEFAULT NULL COMMENT '购买时间',
 *  `quantity` int unsigned DEFAULT NULL COMMENT '购买数量',
 *  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
 *  PRIMARY KEY (`id`),
 *  KEY `idx_app` (`app_key`),
 *  KEY `idx_transaction_id` (`transaction_id`),
 *  KEY `idx_verification_status` (`verification_status`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果票据验证记录表';
 */
class AppleReceiptVerification extends Model
{
    protected $table = 'apple_receipt_verifications';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    // 验证状态
    const VERIFICATION_STATUS_SUCCESS = 1;
    const VERIFICATION_STATUS_FAILED = 2;

    public static $verificationStatusMap = [
        self::VERIFICATION_STATUS_SUCCESS => '成功',
        self::VERIFICATION_STATUS_FAILED => '失败',
    ];
    
    protected $fillable = [
        'id',
        'app_key', 
        'receipt_data_hash', 
        'verification_status', 
        'apple_status_code', 
        'error_message', 
        'bundle_id', 
        'environment', 
        'transaction_id', 
        'original_transaction_id', 
        'product_id', 
        'purchase_date', 
        'quantity', 
        'created_at', 
        'updated_at'
    ];
}

