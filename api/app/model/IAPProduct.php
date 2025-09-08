<?php

namespace app\model;

use support\Model;

/**
 * CREATE TABLE `iap_products` (
 * `pid` bigint unsigned NOT NULL,
 * `app_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 * `iap_product_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '苹果产品ID',
 * `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '产品名称',
 * `sub_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '子标题',
 * `apple_product_type` tinyint unsigned DEFAULT '1' COMMENT '苹果产品类型',
 * `subscription_duration` tinyint unsigned DEFAULT NULL COMMENT '苹果订阅时长周期类型',
 * `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '产品类型：1、会员时长；2、永久会员；99、自定义',
 * `function_value` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '产品功能值，用于购买后逻辑处理',
 * `cross_price` int unsigned NOT NULL COMMENT '划线价，单位分',
 * `sale_price` int unsigned NOT NULL COMMENT '售价',
 * `desc` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商品描述',
 * `sale_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1在售，2为待售',
 * `order` int unsigned NOT NULL DEFAULT '1' COMMENT '排序',
 * `ext_data` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户自定义扩展字段，jsons格式',
 * `deleted_at` timestamp NULL DEFAULT NULL,
 * `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 * `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 * PRIMARY KEY (`pid`) USING BTREE,
 * UNIQUE KEY `unq_iap_pid` (`app_key`,`iap_product_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

class IAPProduct extends Model
{
    protected $table = 'iap_products';
    protected $primaryKey = 'pid';
    public $incrementing = false;
    public $timestamps = false;

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

    const TYPE_MEMBER_DURATION = 1;
    const TYPE_MEMBER_FOREVER = 2;
    const TYPE_MEMBER_CUSTOM = 99;

    const IS_SUBSCRIPTION_YES = 1;
    const IS_SUBSCRIPTION_NO = 0;

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

     // 产品类型
     const PRODUCT_TYPE_CONSUMABLE = 1;               // 消耗型
     const PRODUCT_TYPE_NON_CONSUMABLE = 2;           // 非消耗型
     const PRODUCT_TYPE_AUTO_RENEWABLE = 3;           // 自动续期订阅
     const PRODUCT_TYPE_NON_RENEWING = 4;             // 非续期订阅
 
     public static $productTypeMap = [
         self::PRODUCT_TYPE_CONSUMABLE => '消耗型',
         self::PRODUCT_TYPE_NON_CONSUMABLE => '非消耗型',
         self::PRODUCT_TYPE_AUTO_RENEWABLE => '自动续期订阅',
         self::PRODUCT_TYPE_NON_RENEWING => '非续期订阅'
     ];

    public $hidden = ['app_key', 'updated_at', 'created_at', 'order', 'deleted_at'];

    public function getProductListByAppKey($appkey, $status = null, $type = null, $apple_product_type = null, $limit = 100, $offset = 0)
    {
        $query = $this->where('app_key', $appkey)->orderBy('order', 'asc');
        if(!empty($status)){
            $query->where('sale_status', $status);
        }
        if(!empty($type)){
            $query->where('type', $type);
        }
        if(!empty($apple_product_type)){
            $query->where('apple_product_type', $apple_product_type);
        }
        $query->where('deleted_at', null);

        $product_list = $query->limit($limit)->offset($offset)->get();
        if(empty($product_list)){
            return [];
        }
        $product_list = $product_list->toArray();
        return $product_list;
    }

    public function getProductInfoByPid($pid, $appkey)
    {
        $product = $this->where('pid', $pid)->where('app_key', $appkey)->first();
        if(empty($product)){
            return [];
        }
        $product = $product->toArray();
        return $product;
    }

    /**
     * 根据苹果产品ID获取产品信息
     * @param string $apple_product_id
     * @param string $appkey
     * @return array
     */
    public function getProductInfoByAppleProductId($apple_product_id, $appkey)
    {
        $product = $this->where('iap_product_id', $apple_product_id)->where('app_key', $appkey)->first();
        if(empty($product)){
            return [];
        }
        $product = $product->toArray();
        return $product;
    }

    public function getProductsByPids(array $pids)
    {
        $products = $this->whereIn('pid', $pids)->get();
        if(empty($products)){
            return [];
        }
        $products = $products->toArray();
        return $products;
    }
}