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
 * App\Models\App
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
 * @property int $platform_type 1.android, 2.ios，99.others
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
 * @method static \Illuminate\Database\Eloquent\Builder|AppUpgrade wherePlatformType($value)
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
 * App\Models\LoginInterfaceConfig
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
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAppKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCrossPrice($value)
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
 */
	class Product extends \Eloquent implements \Spatie\EloquentSortable\Sortable {}
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
 */
	class Tenant extends \Eloquent implements \Illuminate\Contracts\Auth\Authenticatable {}
}

namespace App\Models{
/**
 * App\Models\WechatOpenPlatformConfig
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

