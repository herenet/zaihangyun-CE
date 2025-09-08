<?php

namespace app\model;

use support\Cache;
use support\Model;

/**
 * CREATE TABLE `iap_config` (
 *   `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
 *   `bundle_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *   `app_apple_id` bigint NOT NULL,
 *   `subscrip_switch` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '用户是否开启了苹果的订阅功能',
 *   `shared_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用于验证自动续期订阅收据（verifyReceipt）\n\n',
 *   `apple_dev_s2s_config_id` bigint DEFAULT NULL,
 *   `interface_check` tinyint unsigned NOT NULL DEFAULT '0',
 *   `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`app_key`),
 *   UNIQUE KEY `unq_buildle_id` (`bundle_id`) COMMENT '不能添加两个bundleID相同的配置'
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果In-App Purchase配置';
 */

class IAPConfig extends Model
{
    protected $table = 'iap_config';

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_IAP_CONFIG = 'iap_config|{appKey}';

    protected $fillable = [
        'app_key', 
        'bundle_id', 
        'app_apple_id', 
        'subscrip_switch', 
        'shared_secret', 
        'apple_dev_s2s_config_id', 
        'interface_check', 
    ];

    public function getIAPConfig($appKey)
    {
        $cacheKey = str_replace('{appKey}', $appKey, self::CACHE_KEY_IAP_CONFIG);
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where('app_key', $appKey)->first();
            if(empty($rs)){
                return [];
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
}
