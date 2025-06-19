<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CREATE TABLE `tenant_api_stats` (
 *  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 *  `app_key` varchar(64) NOT NULL COMMENT '应用标识',
 *  `tenant_id` bigint(20) unsigned NOT NULL COMMENT '租户ID',
 *  `call_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '调用次数',
 *  `stat_date` date NOT NULL COMMENT '统计日期',
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 *  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`id`),
 *  UNIQUE KEY `uk_app_tenant_date` (`app_key`,`tenant_id`,`stat_date`),
 *  KEY `idx_tenant_id` (`tenant_id`),
 *  KEY `idx_stat_date` (`stat_date`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='租户API调用统计表';
 */
class TenantApiStats extends Model
{
    protected $table = 'tenant_api_stats';

    protected $fillable = ['app_key', 'tenant_id', 'call_count', 'stat_date'];
}