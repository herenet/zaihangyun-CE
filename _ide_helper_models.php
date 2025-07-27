<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AlipayConfig
 *
 * @property string $app_key
 * @property int $tenant_id
 * @property string $alipay_app_id
 * @property string $alipay_public_cert
 * @property string $app_private_cert
 * @property int $interface_check
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig whereAlipayAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig whereAlipayPublicCert($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig whereAppPrivateCert($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig whereInterfaceCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlipayConfig whereUpdatedAt($value)
 */
	class AlipayConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AliyunAccessConfig
 *
 * @property int $id
 * @property int $tenant_id
 * @property string|null $name
 * @property string|null $access_key
 * @property string|null $access_key_secret
 * @property string|null $remark
 * @property int|null $interface_check
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereAccessKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereAccessKeySecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereInterfaceCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AliyunAccessConfig whereUpdatedAt($value)
 */
	class AliyunAccessConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * CREATE TABLE `apps` (
 *  `app_key` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `tenant_id` bigint(20) unsigned NOT NULL,
 *  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `platform_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1为安卓，2为iphone',
 *  `launcher_icon` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `app_secret` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`app_key`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * @property string $app_key
 * @property int $tenant_id
 * @property string $name
 * @property int $platform_type 1为安卓，2为iphone
 * @property string|null $launcher_icon
 * @property string|null $app_secret
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|App newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|App newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|App query()
 * @method static \Illuminate\Database\Eloquent\Builder|App whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|App whereAppSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|App whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|App whereLauncherIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|App whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|App wherePlatformType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|App whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|App whereUpdatedAt($value)
 */
	class App extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AppConfig
 *
 * @property int $id
 * @property string $app_key
 * @property int $tenant_id
 * @property string $title 配置名称
 * @property string $name 配置名，英文，用于接口传参
 * @property array $params 键值对，json格式
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppConfig whereUpdatedAt($value)
 */
	class AppConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AppUpgrade
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $app_key
 * @property int $channel_id
 * @property string $version_str
 * @property int $version_num
 * @property int|null $min_version_num 最小支持版本
 * @property int $force_upgrade
 * @property int $enabled 0为未生效, 1为生效中
 * @property int|null $gray_upgrade
 * @property int $gray_percent 灰度比例
 * @property int $upgrade_from 1应用商店, 2为官网下载
 * @property string|null $package_download_url
 * @property string|null $package_md5
 * @property string|null $package_size
 * @property string|null $upgrade_note
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereForceUpgrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereGrayPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereGrayUpgrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereMinVersionNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade wherePackageDownloadUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade wherePackageMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade wherePackageSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereUpgradeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereUpgradeNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereVersionNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade whereVersionStr($value)
 */
	class AppUpgrade extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AppUpgradeChannel
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $app_key
 * @property string $channel_name
 * @property int $is_default 1为是，2为否
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel whereChannelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgradeChannel whereUpdatedAt($value)
 */
	class AppUpgradeChannel extends \Eloquent {}
}

namespace App\Models{
/**
 * CREATE TABLE `apple_dev_s2s_config` (
 * `id` bigint unsigned NOT NULL AUTO_INCREMENT,
 * `tenant_id` bigint unsigned NOT NULL,
 * `dev_account_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '苹果开发者账户名称',
 * `issuer_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 * `key_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 * `p8_cert_content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'p8证书内容',
 * `interface_check` tinyint unsigned NOT NULL DEFAULT '0',
 * `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 * `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 * PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $dev_account_name 苹果开发者账户名称
 * @property string $issuer_id
 * @property string $key_id
 * @property string $p8_cert_content p8证书内容
 * @property int $interface_check
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereDevAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereInterfaceCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereIssuerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereP8CertContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleDevS2SConfig whereUpdatedAt($value)
 */
	class AppleDevS2SConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * CREATE TABLE `apple_notifications` (
 *  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
 *  `tenant_id` bigint(20) unsigned NOT NULL COMMENT '租户ID',
 *  `app_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用标识',
 *  `notification_uuid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *  `notification_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '通知类型(如INITIAL_BUY,DID_RENEW,DID_CANCEL等)',
 *  `subtype` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '通知子类型',
 *  `transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '交易ID',
 *  `original_transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '原始交易ID',
 *  `environment` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '环境：sandbox或production',
 *  `notification_data` text COLLATE utf8mb4_unicode_ci COMMENT '完整的通知数据(JSON格式字符串)',
 *  `processed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已处理：0=未处理，1=已处理',
 *  `process_result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '处理结果描述',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
 *  PRIMARY KEY (`id`),
 *  KEY `idx_processed` (`processed`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果S2S通知记录表';
 *
 * @property int $id 自增ID
 * @property int $tenant_id 租户ID
 * @property string $app_key 应用标识
 * @property string|null $notification_uuid
 * @property string $notification_type 通知类型(如INITIAL_BUY,DID_RENEW,DID_CANCEL等)
 * @property string|null $subtype 通知子类型
 * @property string|null $transaction_id 交易ID
 * @property string|null $original_transaction_id 原始交易ID
 * @property string $environment 环境：sandbox或production
 * @property string|null $notification_data 完整的通知数据(JSON格式字符串)
 * @property int $processed 是否已处理：0=未处理，1=已处理
 * @property string|null $process_result 处理结果描述
 * @property string|null $updated_at
 * @property string|null $created_at 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereNotificationData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereNotificationUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereOriginalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereProcessResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereSubtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleNotification whereUpdatedAt($value)
 */
	class AppleNotification extends \Eloquent {}
}

namespace App\Models{
/**
 * CREATE TABLE `apple_orders` (
 * `oid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内部订单号',
 * `tenant_id` bigint(20) unsigned NOT NULL COMMENT '租户ID',
 * `app_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用标识',
 * `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID',
 * `product_id` int(10) unsigned NOT NULL COMMENT '内部产品ID',
 * `apple_product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '苹果产品标识符',
 * `product_type` tinyint(3) unsigned NOT NULL COMMENT '产品类型：1=消耗型(consumable)，2=非消耗型(non_consumable)，3=自动续期订阅(auto_renewable_subscription)，4=非续期订阅(non_renewing_subscription)',
 * `amount` int(10) unsigned NOT NULL COMMENT '订单金额(分)',
 * `payment_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '支付状态：1=待验证，2=支付成功，3=支付失败，4=已退款',
 * `subscription_status` tinyint(3) unsigned DEFAULT NULL COMMENT '订阅状态：1=活跃，2=已过期，3=已取消，4=宽限期，5=计费重试',
 * `transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果交易ID',
 * `original_transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '原始交易ID(订阅关联标识)',
 * `environment` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '环境：sandbox或production',
 * `is_trial_period` tinyint(1) DEFAULT NULL COMMENT '是否试用期：0=否，1=是',
 * `is_in_intro_offer_period` tinyint(1) DEFAULT NULL COMMENT '是否促销期：0=否，1=是',
 * `expires_date` timestamp NULL DEFAULT NULL COMMENT '订阅过期时间',
 * `auto_renew_status` tinyint(1) DEFAULT NULL COMMENT '自动续订状态：0=关闭，1=开启',
 * `auto_renew_product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '下一周期续订的产品ID',
 * `purchase_date` timestamp NULL DEFAULT NULL COMMENT '购买时间',
 * `original_purchase_date` timestamp NULL DEFAULT NULL COMMENT '原始购买时间',
 * `cancellation_date` timestamp NULL DEFAULT NULL COMMENT '取消时间(退款时苹果返回)',
 * `data_source` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '数据来源：1=Receipt验证，2=S2S通知',
 * `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
 * `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
 * PRIMARY KEY (`oid`),
 * UNIQUE KEY `uk_tenant_app_transaction` (`tenant_id`,`app_key`,`transaction_id`),
 * KEY `idx_tenant_app_original_transaction` (`tenant_id`,`app_key`,`original_transaction_id`),
 * KEY `idx_uid` (`uid`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='苹果支付订单表';
 *
 * @property string $oid 内部订单号
 * @property int $tenant_id 租户ID
 * @property string $app_key 应用标识
 * @property int $uid 用户ID
 * @property int $product_id 内部产品ID
 * @property string $apple_product_id 苹果产品标识符
 * @property int $product_type 产品类型：1=消耗型(consumable)，2=非消耗型(non_consumable)，3=自动续期订阅(auto_renewable_subscription)，4=非续期订阅(non_renewing_subscription)
 * @property int $amount 订单金额(分)
 * @property int $payment_status 支付状态：1=待验证，2=支付成功，3=支付失败，4=已退款
 * @property int|null $subscription_status 订阅状态：1=活跃，2=已过期，3=已取消，4=宽限期，5=计费重试
 * @property string|null $transaction_id 苹果交易ID
 * @property string|null $original_transaction_id 原始交易ID(订阅关联标识)
 * @property string $environment 环境：sandbox或production
 * @property int|null $is_trial_period 是否试用期：0=否，1=是
 * @property int|null $is_in_intro_offer_period 是否促销期：0=否，1=是
 * @property string|null $expires_date 订阅过期时间
 * @property int|null $auto_renew_status 自动续订状态：0=关闭，1=开启
 * @property string|null $auto_renew_product_id 下一周期续订的产品ID
 * @property string|null $purchase_date 购买时间
 * @property string|null $original_purchase_date 原始购买时间
 * @property string|null $cancellation_date 取消时间(退款时苹果返回)
 * @property int $data_source 数据来源：1=Receipt验证，2=S2S通知
 * @property string|null $updated_at 更新时间
 * @property string|null $created_at 创建时间
 * @property-read \App\Models\IAPProduct|null $product
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereAppleProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereAutoRenewProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereAutoRenewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereCancellationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereDataSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereExpiresDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereIsInIntroOfferPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereIsTrialPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereOid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereOriginalPurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereOriginalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereSubscriptionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleOrder whereUpdatedAt($value)
 */
	class AppleOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AppleReceiptData
 *
 * @property int $verification_id 验证记录ID
 * @property int $tenant_id 租户ID（冗余）
 * @property string $app_key 应用标识（冗余）
 * @property string $receipt_data_hash 票据数据哈希（冗余）
 * @property string $receipt_data 票据数据：成功时为解密后JSON，失败时为原始数据
 * @property string $created_at 创建时间
 * @property-read \App\Models\AppleReceiptVerification|null $verification
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData whereReceiptData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData whereReceiptDataHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptData whereVerificationId($value)
 */
	class AppleReceiptData extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AppleReceiptVerification
 *
 * @property int $id 自增ID
 * @property int $tenant_id 租户ID
 * @property string $app_key 应用标识
 * @property string $receipt_data_hash 票据数据SHA256哈希
 * @property int $verification_status 验证状态：1=成功，2=失败
 * @property int|null $apple_status_code 苹果返回的状态码
 * @property string|null $error_message 错误信息
 * @property string|null $bundle_id 应用Bundle ID
 * @property string|null $environment 环境：sandbox或production
 * @property string|null $transaction_id 交易ID
 * @property string|null $original_transaction_id 原始交易ID
 * @property string|null $product_id 产品ID
 * @property \Illuminate\Support\Carbon|null $purchase_date 购买时间
 * @property int|null $quantity 购买数量
 * @property string $created_at 创建时间
 * @property string|null $updated_at 更新时间
 * @property-read \App\Models\AppleReceiptData|null $receiptData
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereAppleStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereBundleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereOriginalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereReceiptDataHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleReceiptVerification whereVerificationStatus($value)
 */
	class AppleReceiptVerification extends \Eloquent {}
}

namespace App\Models{
/**
 * CREATE TABLE `apple_verify_config` (
 *  `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `tenant_id` bigint unsigned NOT NULL,
 *  `bundle_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `multiple_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许多次验证：0不允许，1允许',
 *  `subscrip_switch` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否开启订阅：0关闭，1开启',
 *  `shared_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果共享密钥',
 *  `interface_check` tinyint unsigned DEFAULT '0',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`app_key`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * @property string $app_key
 * @property int $tenant_id
 * @property string $bundle_id
 * @property int $multiple_verify 是否允许多次验证：0不允许，1允许
 * @property int $subscrip_switch 是否开启订阅：0关闭，1开启
 * @property string|null $shared_secret 苹果共享密钥
 * @property int|null $interface_check
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereBundleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereInterfaceCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereMultipleVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereSharedSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereSubscripSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppleVerifyConfig whereUpdatedAt($value)
 */
	class AppleVerifyConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Article
 *
 * @property string $id
 * @property int $tenant_id
 * @property string $app_key
 * @property string $title
 * @property int $category_id
 * @property string $content
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\ArticleCategory|null $category
 * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereUpdatedAt($value)
 */
	class Article extends \Eloquent implements \Spatie\EloquentSortable\Sortable {}
}

namespace App\Models{
/**
 * App\Models\ArticleCategory
 *
 * @property int $id
 * @property string $app_key
 * @property int $tenant_id
 * @property string $name
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereUpdatedAt($value)
 */
	class ArticleCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ArticleConfig
 *
 * @property string $app_key
 * @property int $tenant_id
 * @property int $switch
 * @property string $list_theme
 * @property string $content_theme
 * @property string|null $updated_at
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig whereContentTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig whereListTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig whereSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleConfig whereUpdatedAt($value)
 */
	class ArticleConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ArticleContentShow
 *
 * @property string $article_id
 * @property int $tenant_id
 * @property string $app_key
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleContentShow whereUpdatedAt($value)
 */
	class ArticleContentShow extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Feedback
 *
 * @property int $id
 * @property string $app_key
 * @property int $tenant_id
 * @property int $uid
 * @property int $type 1为bug反馈，2为意见
 * @property string $content 内容
 * @property string|null $contact
 * @property string|null $reply
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUpdatedAt($value)
 */
	class Feedback extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IAPConfig
 *
 * @property int $app_key
 * @property int $tenant_id
 * @property string $bundle_id
 * @property int $app_apple_id
 * @property int $subscrip_switch 用户是否开启了苹果的订阅功能
 * @property string|null $shared_secret 用于验证自动续期订阅收据（verifyReceipt）
 * @property int|null $apple_dev_s2s_config_id
 * @property int $interface_check
 * @property string|null $updated_at
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereAppAppleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereAppleDevS2sConfigId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereBundleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereInterfaceCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereSharedSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereSubscripSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPConfig whereUpdatedAt($value)
 */
	class IAPConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * CREATE TABLE `iap_products` (
 *  `pid` bigint unsigned NOT NULL,
 *  `app_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `tenant_id` bigint unsigned NOT NULL,
 *  `iap_product_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果产品ID',
 *  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '产品名称',
 *  `sub_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '子标题',
 *  `apple_product_type` tinyint unsigned DEFAULT '1' COMMENT '苹果产品类型',
 *  `subscription_duration` tinyint unsigned DEFAULT NULL COMMENT '苹果订阅时长周期类型',
 *  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '产品类型：1、会员时长；2、永久会员；99、自定义',
 *  `function_value` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '产品功能值，用于购买后逻辑处理',
 *  `cross_price` int unsigned NOT NULL COMMENT '划线价，单位分',
 *  `sale_price` int unsigned NOT NULL COMMENT '售价',
 *  `desc` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商品描述',
 *  `sale_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1在售，2为待售',
 *  `order` int unsigned NOT NULL DEFAULT '1' COMMENT '排序',
 *  `ext_data` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户自定义扩展字段，jsons格式',
 *  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`pid`) USING BTREE,
 *  UNIQUE KEY `unq_iap_pid` (`app_key`,`iap_product_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * @property int $pid
 * @property string $app_key
 * @property int $tenant_id
 * @property string|null $iap_product_id 苹果产品ID
 * @property string $name 产品名称
 * @property string|null $sub_name 子标题
 * @property int $apple_product_type 苹果产品类型
 * @property int|null $subscription_duration 苹果订阅时长周期类型
 * @property int $type 产品类型：1、会员时长；2、永久会员；99、自定义
 * @property string $function_value 产品功能值，用于购买后逻辑处理
 * @property int $cross_price 划线价，单位分
 * @property int $sale_price 售价
 * @property string|null $desc 商品描述
 * @property int $sale_status 1在售，2为待售
 * @property int $order 排序
 * @property string|null $ext_data 用户自定义扩展字段，jsons格式
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereAppleProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereCrossPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereExtData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereFunctionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereIapProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereSaleStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereSubName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereSubscriptionDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|IAPProduct withoutTrashed()
 */
	class IAPProduct extends \Eloquent implements \Spatie\EloquentSortable\Sortable {}
}

namespace App\Models{
/**
 * CREATE TABLE `login_interface_config` (
 *  `app_key` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 *  `tenant_id` bigint(20) unsigned NOT NULL,
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
 *  UNIQUE KEY `uniq_tenantid_appkey` (`tenant_id`,`app_key`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * @property string $app_key
 * @property int $tenant_id
 * @property int $switch
 * @property int|null $token_effective_duration
 * @property int|null $suport_wechat_login
 * @property int|null $wechat_platform_config_id
 * @property int|null $suport_mobile_login
 * @property int|null $aliyun_access_config_id
 * @property string|null $aliyun_sms_sign_name
 * @property string|null $aliyun_sms_tmp_code
 * @property int|null $aliyun_sms_verify_code_expire
 * @property int|null $suport_apple_login
 * @property string|null $apple_nickname_prefix
 * @property int $endpoint_allow_count
 * @property int|null $cancel_after_days 申请注销多少天后删除
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereAliyunAccessConfigId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereAliyunSmsSignName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereAliyunSmsTmpCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereAliyunSmsVerifyCodeExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereAppleNicknamePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereCancelAfterDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereEndpointAllowCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereSuportAppleLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereSuportMobileLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereSuportWechatLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereTokenEffectiveDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginInterfaceConfig whereWechatPlatformConfigId($value)
 */
	class LoginInterfaceConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MessageConfig
 *
 * @property string $app_key
 * @property int $tenant_id
 * @property int $switch
 * @property string|null $updated_at
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|MessageConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageConfig whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageConfig whereSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageConfig whereUpdatedAt($value)
 */
	class MessageConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property string $oid
 * @property int $tenant_id
 * @property string $app_key
 * @property int $uid
 * @property int $product_id
 * @property int $product_price 产品价格
 * @property int $discount_amount 优惠金额
 * @property int $order_amount 订单金额
 * @property int|null $payment_amount 实际支付金额
 * @property int|null $platform_order_amount 支付平台订单金额
 * @property int $status 1为待支付，2为已支付，3为已退款，4为支付失败
 * @property int $pay_channel 1为微信，2为支付宝，3为苹果
 * @property string|null $tid 第三方订单号
 * @property string|null $trade_type 交易类型
 * @property string|null $bank_type 银行类型
 * @property string|null $refund_id 第三方退款ID
 * @property int|null $refund_type 1退款退功能，2仅退款
 * @property int|null $refund_amount
 * @property string|null $refund_channel
 * @property string|null $refund_reason
 * @property string|null $open_id 第三方支付用户标识
 * @property string $channel 来源渠道
 * @property string|null $pay_time 支付时间
 * @property string|null $refund_send_time
 * @property string|null $refund_time
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBankType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOpenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePlatformOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProductPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundSendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTradeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderInterfaceConfig
 *
 * @property string $app_key
 * @property int $tenant_id
 * @property int $switch
 * @property string|null $oid_prefix 设置订单号前缀
 * @property int|null $suport_wechat_pay
 * @property int|null $wechat_platform_config_id
 * @property int|null $wechat_payment_config_id
 * @property int|null $suport_alipay
 * @property int|null $suport_apple_pay
 * @property int|null $suport_apple_verify
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereOidPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereSuportAlipay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereSuportApplePay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereSuportAppleVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereSuportWechatPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereWechatPaymentConfigId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderInterfaceConfig whereWechatPlatformConfigId($value)
 */
	class OrderInterfaceConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Product
 *
 * @property int $pid
 * @property string $app_key
 * @property int $tenant_id
 * @property string $name 产品名称
 * @property string|null $sub_name 子标题
 * @property int $type 产品类型：1、会员时长；2、永久会员；99、自定义
 * @property string $function_value 产品功能值，用于购买后逻辑处理
 * @property int $cross_price 划线价，单位分
 * @property int $sale_price 售价
 * @property string|null $desc 商品描述
 * @property int $sale_status 1在售，2为待售
 * @property int $platform_type 适用平台：1为所有平台，2为安卓，3为苹果
 * @property int $order 排序
 * @property string|null $ext_data 用户自定义扩展字段，jsons格式
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCrossPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereExtData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereFunctionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePlatformType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSaleStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSubName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product withoutTrashed()
 */
	class Product extends \Eloquent implements \Spatie\EloquentSortable\Sortable {}
}

namespace App\Models{
/**
 * App\Models\SubscriptionOrder
 *
 * @property string $order_id 订单号
 * @property int $tenant_id 租户ID
 * @property string $order_type 订单类型:new_purchase,upgrade,renew
 * @property string|null $from_product 升级前套餐
 * @property string $to_product 目标套餐
 * @property string $product_name 套餐名称
 * @property int $original_price 套餐原价(分)
 * @property int $final_price 实际支付金额(分)
 * @property int|null $status 订单状态:1待支付,2已支付,3已取消,4支付失败
 * @property int|null $pay_channel 支付渠道:1微信,2支付宝
 * @property string|null $wechat_prepay_id 微信预支付ID
 * @property string|null $wechat_code_url 微信支付二维码链接
 * @property string|null $third_party_order_id 第三方订单号
 * @property string|null $third_party_transaction_id 第三方交易号
 * @property \Illuminate\Support\Carbon|null $paid_at 支付时间
 * @property array|null $upgrade_info 升级计算详情
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_final_price
 * @property-read mixed $formatted_original_price
 * @property-read mixed $pay_channel_text
 * @property-read mixed $status_text
 * @property-read mixed $type_text
 * @property-read \App\Models\Tenant|null $tenant
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereFromProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder wherePayChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereThirdPartyOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereThirdPartyTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereToProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereUpgradeInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereWechatCodeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOrder whereWechatPrepayId($value)
 */
	class SubscriptionOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tenant
 *
 * @property int $id
 * @property string|null $avatar
 * @property string $nickname
 * @property string $phone_number
 * @property string|null $password
 * @property int $company_id
 * @property string $product
 * @property \Illuminate\Support\Carbon|null $subscription_expires_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Encore\Admin\Auth\Database\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $name
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereSubscriptionExpiresAt($value)
 */
	class Tenant extends \Eloquent implements \Illuminate\Contracts\Auth\Authenticatable {}
}

namespace App\Models{
/**
 * CREATE TABLE `tenant_api_stats` (
 *  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 *  `app_key` varchar(64) NOT NULL COMMENT '应用标识',
 *  `tenant_id` bigint(20) unsigned NOT NULL COMMENT '租户ID',
 *  `call_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '调用次数',
 *  `stat_date` date NOT NULL COMMENT '统计日期',
 *  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 *  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 *  PRIMARY KEY (`id`),
 *  UNIQUE KEY `uk_app_tenant_date` (`app_key`,`tenant_id`,`stat_date`),
 *  KEY `idx_tenant_id` (`tenant_id`),
 *  KEY `idx_stat_date` (`stat_date`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='租户API调用统计表';
 *
 * @property int $id
 * @property string $app_key 应用标识
 * @property int $tenant_id 租户ID
 * @property int $call_count 调用次数
 * @property string $stat_date 统计日期
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats whereCallCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats whereStatDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantApiStats whereUpdatedAt($value)
 */
	class TenantApiStats extends \Eloquent {}
}

namespace App\Models{
/**
 * CREATE TABLE `wechat_open_platform_config` (
 * `id` bigint unsigned NOT NULL AUTO_INCREMENT,
 * `tenant_id` bigint NOT NULL,
 * `app_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
 * `wechat_appid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 * `wechat_appsecret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 * `interface_check` tinyint unsigned NOT NULL DEFAULT '0',
 * `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 * `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 * `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 * PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $app_name
 * @property string $wechat_appid
 * @property string $wechat_appsecret
 * @property int $interface_check
 * @property string|null $remark
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereAppName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereInterfaceCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereWechatAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatOpenPlatformConfig whereWechatAppsecret($value)
 */
	class WechatOpenPlatformConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WechatPaymentConfig
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $mch_id
 * @property string $mch_name
 * @property string $mch_cert_serial 商户API证书的序列号
 * @property string $mch_api_v3_secret 解密回调APIv3密钥
 * @property string $mch_private_key_path 微信商户API证书私钥
 * @property string $mch_platform_cert_path 微信支付平台证书
 * @property int $interface_check
 * @property int|null $callback_check
 * @property string|null $remark
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereCallbackCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereInterfaceCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereMchApiV3Secret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereMchCertSerial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereMchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereMchName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereMchPlatformCertPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereMchPrivateKeyPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WechatPaymentConfig whereUpdatedAt($value)
 */
	class WechatPaymentConfig extends \Eloquent {}
}

