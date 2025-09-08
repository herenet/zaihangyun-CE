<?php

namespace app\validate;

use app\model\Order;
use app\lib\ZHYVolidate;

class OrderList extends ZHYVolidate
{

    protected $rule = [
        'status' => 'integer|in:'.Order::STATUS_READY.','.Order::STATUS_SUCCESS.','.Order::STATUS_REFUNDING.','.Order::STATUS_REFUNDED.','.Order::STATUS_PAYMENT_FAILED.','.Order::STATUS_REFUND_FAILED,
        'pay_channel' => 'integer|in:'.Order::PAY_CHANNEL_WECHAT.','.Order::PAY_CHANNEL_ALIPAY.','.Order::PAY_CHANNEL_APPLE,
        'need_product_info' => 'integer|in:0,1',
        'limit' => 'integer|min:1|max:100',
    ];  

    protected $message = [
        'status.integer' => '400101|status must be integer',
        'status.in' => '400102|status must be in '.Order::STATUS_READY.','.Order::STATUS_SUCCESS.','.Order::STATUS_REFUNDING.','.Order::STATUS_REFUNDED.','.Order::STATUS_PAYMENT_FAILED.','.Order::STATUS_REFUND_FAILED,
        'pay_channel.integer' => '400103|pay_channel must be integer',
        'pay_channel.in' => '400104|pay_channel must be in '.Order::PAY_CHANNEL_WECHAT.','.Order::PAY_CHANNEL_ALIPAY.','.Order::PAY_CHANNEL_APPLE,
        'limit.integer' => '400105|limit must be integer',
        'limit.min' => '400106|limit must be greater than 0',
        'limit.max' => '400107|limit must be less than 100',
        'need_product_info.integer' => '400108|need_product_info must be integer',
        'need_product_info.in' => '400109|need_product_info must be in 0,1',
    ];
}