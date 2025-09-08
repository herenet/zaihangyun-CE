<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class VerifyAppleReceipt extends ZHYVolidate
{
    protected $rule = [
        'appkey' => 'require|max:64',
        'receipt_data' => 'require',
        'environment' => 'require|in:Sandbox,Production',
        'transaction_id' => 'require|string|max:128',
    ];

    protected $message = [
        'appkey.require' => '400101|appkey parameter is required',
        'appkey.max' => '400102|appkey parameter length cannot exceed 64 characters',
        'receipt_data.require' => '400103|receipt_data parameter is required',
        'environment.require' => '400104|environment parameter is required',
        'environment.in' => '400105|environment parameter must be Sandbox or Production',
        'transaction_id.require' => '400106|transaction_id parameter is required',
        'transaction_id.string' => '400107|transaction_id parameter must be string',
        'transaction_id.max' => '400108|transaction_id parameter length cannot exceed 128 characters',
    ];
} 