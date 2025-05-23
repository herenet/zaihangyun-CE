<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IAPConfig extends Model
{
    protected $table = 'iap_config';
    
    /**
     * CREATE TABLE `iap_config` (
     *   `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
     *   `tenant_id` bigint unsigned NOT NULL,
     *   `bundle_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
     *   `app_apple_id` bigint NOT NULL,
     *   `subscrip_switch` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '用户是否开启了苹果的订阅功能',
     *   `shared_secret` varchar(255) COLLATE utf8mb4_unicode_ci COMMENT '用于验证自动续期订阅收据（verifyReceipt）\n\n',
     *   `server_api_p8_cert` text COLLATE utf8mb4_unicode_ci COMMENT '请求苹果API需要的证书',
     *   `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
     *   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
     *   PRIMARY KEY (`app_key`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果In-App Purchase配置';
     */

    protected $fillable = [
        'app_key',
        'tenant_id',
        'bundle_id',
        'app_apple_id',
        'subscrip_switch',
        'shared_secret',
        'server_api_p8_cert',
    ];

    public function getConfig($tenantId, $appKey)
    {
        $config = $this->where(['tenant_id' => $tenantId, 'app_key' => $appKey])->first();
        return $config;
    }

    public function saveConfig($tenantId, $appKey, $data)
    {
        return self::updateOrCreate(['tenant_id' => $tenantId, 'app_key' => $appKey], $data);
    }
    
}
