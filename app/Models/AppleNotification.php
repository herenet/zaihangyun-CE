<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * CREATE TABLE `apple_notifications` (
 *  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
 *  `tenant_id` bigint(20) unsigned NOT NULL COMMENT '租户ID',
 *  `app_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用标识',
 *  `notification_uuid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `notification_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '通知类型(如INITIAL_BUY,DID_RENEW,DID_CANCEL等)',
 *  `subtype` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '通知子类型',
 *  `transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '交易ID',
 *  `original_transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '原始交易ID',
 *  `environment` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '环境：sandbox或production',
 *  `notification_data` text COLLATE utf8mb4_unicode_ci COMMENT '完整的通知数据(JSON格式字符串)',
 *  `processed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已处理：0=未处理，1=已处理',
 *  `process_result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '处理结果描述',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
 *  PRIMARY KEY (`id`),
 *  KEY `idx_processed` (`processed`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果S2S通知记录表';
 */
class AppleNotification extends Model
{
    protected $table = 'apple_notifications';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public static $processedMap = [
        0 => '待处理',
        1 => '已处理'
    ];

    protected $fillable = [
        'tenant_id', 
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
}
