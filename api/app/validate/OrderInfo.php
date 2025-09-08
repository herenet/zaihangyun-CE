<?php

namespace app\validate;

use app\model\Order;
use app\lib\ZHYVolidate;

class OrderInfo extends ZHYVolidate
{

    protected $rule = [
        'oid' => 'require|string',
        'need_product_info' => 'integer|in:0,1',
    ];  

    protected $message = [
        'oid.require' => '400101|oid is required',
        'oid.string' => '400102|oid must be string',
        'need_product_info.integer' => '400103|need_product_info must be integer',
        'need_product_info.in' => '400109|need_product_info must be in 0,1',
    ];
}