<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CREATE TABLE `apple_verify_config` (
 *  `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `bundle_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `multiple_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许多次验证：0不允许，1允许',
 *  `subscrip_switch` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否开启订阅：0关闭，1开启',
 *  `shared_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果共享密钥',
 *  `interface_check` tinyint unsigned DEFAULT '0',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`app_key`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
class AppleVerifyConfig extends Model
{
    protected $table = 'apple_verify_config';
    
    protected $primaryKey = 'app_key';

    public $incrementing = false;

    protected $fillable = [
        'app_key',
        'bundle_id',
        'multiple_verify',
        'subscrip_switch',
        'shared_secret',
        'interface_check',
    ];

    public function getConfig($appKey)
    {
        return self::where(['app_key' => $appKey])->first();
    }

    public function saveConfig($appKey, $data)
    {
        return self::updateOrCreate(['app_key' => $appKey], $data);
    }
}
