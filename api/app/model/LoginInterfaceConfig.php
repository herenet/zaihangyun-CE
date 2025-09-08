<?php

namespace app\model;

use support\Cache;
use support\Model;

/**
 * CREATE TABLE `login_interface_config` (
 *  `app_key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `switch` tinyint(1) NOT NULL DEFAULT '0',
 *  `token_effective_duration` mediumint unsigned DEFAULT '365',
 *  `suport_wechat_login` tinyint(1) DEFAULT '0',
 *  `wechat_platform_config_id` bigint unsigned DEFAULT NULL,
 *  `suport_mobile_login` tinyint(1) DEFAULT '0',
 *  `aliyun_access_config_id` bigint DEFAULT NULL,
 *  `aliyun_sms_sign_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `aliyun_sms_tmp_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `aliyun_sms_verify_code_expire` tinyint unsigned DEFAULT NULL,
 *  `suport_apple_login` tinyint(1) DEFAULT '0',
 *  `apple_nickname_prefix` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `suport_huawei_login` tinyint unsigned DEFAULT '0',
 *  `huawei_oauth_client_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `huawei_oauth_client_secret` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `endpoint_allow_count` tinyint unsigned NOT NULL DEFAULT '1',
 *  `cancel_after_days` tinyint unsigned DEFAULT '15' COMMENT '申请注销多少天后删除',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`app_key`),
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
class LoginInterfaceConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'login_interface_config';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'app_key';

    protected $autoIncrement = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    const CACHE_TTL = 24*60*60;

    const CACHE_KEY_LOGIN_INTERFACE_CONFIG = 'login_interface_config|';

    public function getLoginInterfaceConfigByAppKey($appKey)
    {
        $cacheKey = self::CACHE_KEY_LOGIN_INTERFACE_CONFIG . $appKey;
        $rs = Cache::get($cacheKey);
        if(empty($rs)){
            $rs = $this->where('app_key', $appKey)->first();
            if(empty($rs)){
                return null;
            }
            $rs = $rs->toArray();
            Cache::set($cacheKey, $rs, self::CACHE_TTL);
        }
        return $rs;
    }
}