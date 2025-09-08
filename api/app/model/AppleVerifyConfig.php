<?php

namespace App\Model;

use support\Cache;
use Illuminate\Database\Eloquent\Model;

/**
 * CREATE TABLE `apple_verify_config` (
 *  `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `bundle_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `multiple_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许多次验证：0不允许，1允许',
 *  `subscrip_switch` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启订阅：0关闭，1开启',
 *  `shared_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果共享密钥',
 *  `interface_check` tinyint(1) unsigned NOT NULL DEFAULT '0',
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

    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_APP_VERIFY_CONFIG = 'apple_verify_config|{appKey}';

    public function getVerifyConfigByAppKey($appKey)
    {
        $cacheKey = str_replace('{appKey}', $appKey, self::CACHE_KEY_APP_VERIFY_CONFIG);
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