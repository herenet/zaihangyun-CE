<?php

namespace app\model;

use support\Model;

/**
 * CREATE TABLE `apple_receipt_data` (
 *  `verification_id` bigint unsigned NOT NULL COMMENT '验证记录ID',
 *  `app_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用标识（冗余）',
 *  `receipt_data_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '票据数据哈希（冗余）',
 *  `receipt_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '票据数据：成功时为解密后JSON，失败时为原始数据',
 *  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
 *  PRIMARY KEY (`verification_id`),
 *  KEY `idx_tenant_app` (`tenant_id`,`app_key`),
 *  KEY `idx_app_hash` (`app_key`,`receipt_data_hash`),
 *  CONSTRAINT `fk_receipt_data_verification` FOREIGN KEY (`verification_id`) REFERENCES `apple_receipt_verifications` (`id`) ON DELETE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果票据原始数据表';
 */

class AppleReceiptData extends Model
{
    protected $table = 'apple_receipt_data';

    protected $primaryKey = 'verification_id';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'verification_id',
        'app_key',
        'receipt_data_hash',
        'receipt_data',
    ];
}