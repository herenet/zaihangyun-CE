<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

/**
 * CREATE TABLE `wechat_open_platform_config` (
 * `id` bigint unsigned NOT NULL AUTO_INCREMENT,
 * `app_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 * `wechat_appid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 * `wechat_appsecret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 * `interface_check` tinyint unsigned NOT NULL DEFAULT '0',
 * `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 * `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 * `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 * PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
class WechatOpenPlatformConfig extends Model
{
    use DefaultDatetimeFormat;

    protected $primaryKey = 'id';

    protected $table = 'wechat_open_platform_config';
    protected $fillable = [
        'app_name',
        'wechat_appid', 
        'wechat_appsecret',
        'interface_check',
        'remark',
    ];
}
