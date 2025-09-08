<?php

namespace app\validate;

use app\model\Order;
use app\lib\ZHYVolidate;

class CreateOrder extends ZHYVolidate
{
    protected $rule = [];
    protected $message = [];

    public function __construct()
    {
        $this->rule = [
            'pid' => 'require|integer',
            'pay_channel' => 'require|integer|in:'.implode(',', [Order::PAY_CHANNEL_WECHAT, Order::PAY_CHANNEL_ALIPAY, Order::PAY_CHANNEL_APPLE]),
            'channel' => 'string|max:32',
        ];
        $this->message = [
            'pid.require' => '400104|pid must be required',
            'pid.integer' => '400105|pid must be integer',
            'pay_channel.require' => '400107|pay_channel must be required',
            'pay_channel.integer' => '400108|pay_channel must be integer',
            'pay_channel.in' => '400109|pay_channel must be in:'.implode(',', [Order::PAY_CHANNEL_WECHAT, Order::PAY_CHANNEL_ALIPAY, Order::PAY_CHANNEL_APPLE]),
            'channel.string' => '400110|channel must be string',
            'channel.max' => '400111|channel must be less than 32 characters',
        ];

        parent::__construct();
    }
}