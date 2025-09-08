<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CREATE TABLE `login_interface_config` (
 *  `app_key` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `switch` tinyint(1) NOT NULL DEFAULT '0',
 *  `token_effective_duration` mediumint(8) unsigned DEFAULT '365',
 *  `suport_wechat_login` tinyint(1) DEFAULT '0',
 *  `wechat_platform_config_id` bigint(20) unsigned DEFAULT NULL,
 *  `suport_mobile_login` tinyint(1) DEFAULT '0',
 *  `aliyun_access_config_id` bigint(20) DEFAULT NULL,
 *  `aliyun_sms_sign_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `aliyun_sms_tmp_code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `aliyun_sms_verify_code_expire` tinyint(3) unsigned DEFAULT '5',
 *  `suport_apple_login` tinyint(1) DEFAULT '0',
 *  `apple_nickname_prefix` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `suport_huawei_login` tinyint(1) unsigned DEFAULT '0',
 *  `huawei_oauth_client_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `huawei_oauth_client_secret` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `endpoint_allow_count` tinyint(3) unsigned NOT NULL DEFAULT '1',
 *  `cancel_after_days` tinyint(2) DEFAULT NULL COMMENT '申请注销多少天后删除',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`app_key`),
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
class LoginInterfaceConfig extends Model
{
    protected $table = 'login_interface_config';

    protected $primaryKey = 'app_key';

    public $incrementing = false;

    protected $fillable = [
        'app_key', 
        'switch', 
        'token_effective_duration',
        'suport_wechat_login', 
        'wechat_platform_config_id',
        'suport_mobile_login',
        'aliyun_access_config_id',
        'aliyun_sms_sign_name',
        'aliyun_sms_tmp_code',
        'aliyun_sms_verify_code_expire',
        'suport_apple_login',
        'apple_nickname_prefix',
        'suport_huawei_login',
        'huawei_oauth_client_id',
        'huawei_oauth_client_secret',
        'endpoint_allow_count',
        'cancel_after_days',
    ];

    public function getConfig($appKey)
    {
        $config = $this->where(['app_key' => $appKey])->first();
        return $config;
    }

    public function saveConfig($appKey, $data)
    {
        return self::updateOrCreate(['app_key' => $appKey], $data);
    }
}
