<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class ProductInfo extends ZHYVolidate
{

    protected $rule = [
        'pid' => 'require|string',
    ];

    protected $message = [
        'pid.require' => '400101|pid is required',
        'pid.string' => '400102|pid must be string',
    ];
}