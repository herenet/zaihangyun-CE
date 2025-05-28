<?php

namespace App\Models;

use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
* CREATE TABLE `iap_products` (
*  `pid` bigint unsigned NOT NULL,
*  `app_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
*  `tenant_id` bigint unsigned NOT NULL,
*  `iap_product_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果产品ID',
*  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '产品名称',
*  `sub_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '子标题',
*  `is_subscription` tinyint unsigned DEFAULT '0' COMMENT '是否为订阅',
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
*/

class IAPProduct extends Model implements Sortable
{
    use DefaultDatetimeFormat, SortableTrait, SoftDeletes;

    protected $table = 'iap_products';

    protected $fillable = [
        'app_key', 
        'tenant_id', 
        'iap_product_id',
        'name', 
        'sub_name', 
        'is_subscription', 
        'subscription_duration',
        'type', 
        'function_value', 
        'cross_price', 
        'sale_price', 
        'desc', 
        'sale_status', 
        'order', 
        'ext_data'
    ];

    /**
     * 苹果订阅时长周期类型
     */
    const SUBSCRIPTION_DURATION_TYPE_WEEK = 1;
    const SUBSCRIPTION_DURATION_TYPE_ONE_MONTH = 2;
    const SUBSCRIPTION_DURATION_TYPE_TWO_MONTH = 3;
    const SUBSCRIPTION_DURATION_TYPE_THREE_MONTH = 4;
    const SUBSCRIPTION_DURATION_TYPE_SIX_MONTH = 5;
    const SUBSCRIPTION_DURATION_TYPE_ONE_YEAR = 6;

    public static $subscriptionDurationTypeMap = [
        self::SUBSCRIPTION_DURATION_TYPE_WEEK => '1周',
        self::SUBSCRIPTION_DURATION_TYPE_ONE_MONTH => '1个月',
        self::SUBSCRIPTION_DURATION_TYPE_TWO_MONTH => '2个月',
        self::SUBSCRIPTION_DURATION_TYPE_THREE_MONTH => '3个月',
        self::SUBSCRIPTION_DURATION_TYPE_SIX_MONTH => '6个月',
        self::SUBSCRIPTION_DURATION_TYPE_ONE_YEAR => '1年'
    ];

    public static $subscriptionDurationTypeValueMap = [
        self::SUBSCRIPTION_DURATION_TYPE_WEEK => 7,
        self::SUBSCRIPTION_DURATION_TYPE_ONE_MONTH => 30,
        self::SUBSCRIPTION_DURATION_TYPE_TWO_MONTH => 60,
        self::SUBSCRIPTION_DURATION_TYPE_THREE_MONTH => 90,
        self::SUBSCRIPTION_DURATION_TYPE_SIX_MONTH => 180,
        self::SUBSCRIPTION_DURATION_TYPE_ONE_YEAR => 365
    ];

    const TYPE_VALUE_KEY_FOR_DURATION_MEMBER = 1;
    const TYPE_VALUE_KEY_FOR_FOREVER_MEMBER = 2;
    // const TYPE_VALUE_KEY_FOR_CUSTOM_FUNCTION = 99;
    
    public static $typeMap = [
        self::TYPE_VALUE_KEY_FOR_DURATION_MEMBER => '会员时长',
        self::TYPE_VALUE_KEY_FOR_FOREVER_MEMBER => '永久会员',
        // self::TYPE_VALUE_KEY_FOR_CUSTOM_FUNCTION => '自定义',
    ];

    const FOREVER_VIP_FUNCTION_VALUE = 'forever_vip';

    public static $saleStatusMap = [
        1 => '在售',
        2 => '待售'
    ];

    public static $isSubscriptionMap = [
        0 => '否',
        1 => '是'
    ];

    protected $primaryKey = 'pid';

    protected $autoIncrement = false;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
        // 'ignore_timestamps' => false,
    ];

    // 增加构建排序查询的方法
    public function buildSortQuery()
    {
        // 仅用同一个 app_key 的记录进行排序比较
        return static::where('app_key', $this->app_key); 
    }
}

