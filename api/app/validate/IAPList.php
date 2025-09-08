<?php

namespace app\validate;

use app\model\IAPProduct;
use app\lib\ZHYVolidate;

class IAPList extends ZHYVolidate
{

    protected $rule = [
        'status' => 'integer|in:'.IAPProduct::STATUS_ON.','.IAPProduct::STATUS_OFF,
        'type' => 'integer|in:'.IAPProduct::TYPE_MEMBER_DURATION.','.IAPProduct::TYPE_MEMBER_FOREVER.','.IAPProduct::TYPE_MEMBER_CUSTOM,
        'apple_product_type' => 'integer|in:'.IAPProduct::PRODUCT_TYPE_CONSUMABLE.','.IAPProduct::PRODUCT_TYPE_NON_CONSUMABLE.','.IAPProduct::PRODUCT_TYPE_AUTO_RENEWABLE.','.IAPProduct::PRODUCT_TYPE_NON_RENEWING,
    ];  

    protected $message = [
        'status.integer' => '400101|status must be integer',
        'status.in' => '400102|status must be in '.IAPProduct::STATUS_ON.','.IAPProduct::STATUS_OFF,
        'type.integer' => '400103|type must be integer',
        'type.in' => '400104|type must be in '.IAPProduct::TYPE_MEMBER_DURATION.','.IAPProduct::TYPE_MEMBER_FOREVER.','.IAPProduct::TYPE_MEMBER_CUSTOM,
        'apple_product_type.integer' => '400105|apple_product_type must be integer',
        'apple_product_type.in' => '400106|apple_product_type must be in '.IAPProduct::PRODUCT_TYPE_CONSUMABLE.','.IAPProduct::PRODUCT_TYPE_NON_CONSUMABLE.','.IAPProduct::PRODUCT_TYPE_AUTO_RENEWABLE.','.IAPProduct::PRODUCT_TYPE_NON_RENEWING,
    ];
}