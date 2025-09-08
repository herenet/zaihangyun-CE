<?php

namespace app\model;

use support\Cache;
use support\Model;


/**
 * CREATE TABLE `apple_dev_s2s_config` (
 *  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 *  `dev_account_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '苹果开发者账户名称',
 *  `issuer_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `key_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `p8_cert_content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'p8证书内容',
 *  `interface_check` tinyint(3) unsigned NOT NULL DEFAULT '0',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=212312 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
class AppleDevS2SConfig extends Model
{
    protected $table = 'apple_dev_s2s_config';

    protected $primaryKey = 'id';

    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_APPLE_DEV_S2S_CONFIG = 'apple_dev_s2s_config|{id}';

    public function getAppleDevS2SConfig($id)
    {
        $cacheKey = str_replace('{id}', $id, self::CACHE_KEY_APPLE_DEV_S2S_CONFIG);
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where(['id' => $id])->first();
            if(empty($rs)){
                return [];
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
}