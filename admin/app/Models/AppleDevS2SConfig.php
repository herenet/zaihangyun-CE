<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;
/**
 * CREATE TABLE `apple_dev_s2s_config` (
 * `id` bigint unsigned NOT NULL AUTO_INCREMENT,
 * `dev_account_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '苹果开发者账户名称',
 * `issuer_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 * `key_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 * `p8_cert_content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'p8证书内容',
 * `interface_check` tinyint unsigned NOT NULL DEFAULT '0',
 * `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 * `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 * PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

class AppleDevS2SConfig extends Model
{
    use DefaultDatetimeFormat;

    protected $table = 'apple_dev_s2s_config';

    protected $fillable = [
        'dev_account_name',
        'issuer_id',
        'key_id',
        'p8_cert_content',
        'interface_check',
    ];
}
