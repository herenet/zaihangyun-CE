<?php

namespace app\validate;

use app\model\Product;
use app\lib\ZHYVolidate;

class ProductList extends ZHYVolidate
{

    protected $rule = [
        'status' => 'integer|in:'.Product::STATUS_ON.','.Product::STATUS_OFF,
        'platform' => 'integer|in:'.Product::PLATFORM_ALL.','.Product::PLATFORM_IOS.','.Product::PLATFORM_ANDROID,
        'type' => 'integer|in:'.Product::TYPE_MEMBER_DURATION.','.Product::TYPE_MEMBER_FOREVER.','.Product::TYPE_MEMBER_CUSTOM,
    ];  

    protected $message = [
        'status.integer' => '400101|status must be integer',
        'status.in' => '400102|status must be in '.Product::STATUS_ON.','.Product::STATUS_OFF,
        'platform.integer' => '400103|platform must be integer',
        'platform.in' => '400104|platform must be in '.Product::PLATFORM_ALL.','.Product::PLATFORM_IOS.','.Product::PLATFORM_ANDROID,
        'type.integer' => '400105|type must be integer',
        'type.in' => '400106|type must be in '.Product::TYPE_MEMBER_DURATION.','.Product::TYPE_MEMBER_FOREVER.','.Product::TYPE_MEMBER_CUSTOM,
    ];
}