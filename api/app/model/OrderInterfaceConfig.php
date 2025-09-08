<?php

namespace app\model;

use support\Cache;
use support\Model;


/**
 * CREATE TABLE `order_interface_config` (
 *  `app_key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `switch` tinyint(1) NOT NULL DEFAULT '0',
 *  `oid_prefix` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '设置订单号前缀',
 *  `suport_wechat_pay` tinyint(1) DEFAULT '0',
 *  `wechat_platform_config_id` bigint unsigned DEFAULT NULL,
 *  `wechat_payment_config_id` bigint unsigned DEFAULT NULL,
 *  `suport_alipay` tinyint(1) DEFAULT '0',
 *  `suport_apple_pay` tinyint(1) DEFAULT '0',
 *  `suport_apple_verify` tinyint unsigned DEFAULT '0',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`app_key`),
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
class OrderInterfaceConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_interface_config';

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

    const CACHE_KEY_ORDER_INTERFACE_CONFIG = 'order_interface_config|';

    public function getOrderInterfaceConfigByAppKey($appKey)
    {
        $cacheKey = self::CACHE_KEY_ORDER_INTERFACE_CONFIG . $appKey;
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