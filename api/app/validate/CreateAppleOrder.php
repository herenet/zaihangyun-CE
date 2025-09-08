<?php

namespace app\validate;

use app\lib\ZHYVolidate;
use app\model\AppleOrder;

class CreateAppleOrder extends ZHYVolidate
{
    protected $rule = [];
    protected $message = [];

    public function __construct()
    {
        $this->rule = [
            'pid' => 'require|integer',
            'apple_product_id' => 'require|string|max:128',
            'environment' => 'string|in:'.implode(',', [AppleOrder::ENVIRONMENT_SANDBOX, AppleOrder::ENVIRONMENT_PRODUCTION]),
        ];

        $this->message = [
            'pid.require' => '400101|pid must be required',
            'pid.integer' => '400102|pid must be integer',
            'apple_product_id.require' => '400103|apple_product_id must be required',
            'apple_product_id.string' => '400104|apple_product_id must be string',
            'apple_product_id.max' => '400105|apple_product_id must be less than 128 characters',
            'environment.string' => '400106|environment must be string',
            'environment.in' => '400107|environment must be '.implode(',', [AppleOrder::ENVIRONMENT_SANDBOX, AppleOrder::ENVIRONMENT_PRODUCTION]),
        ];

        parent::__construct();
    }
}