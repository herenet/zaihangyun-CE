<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class VerifyAppleOrder extends ZHYVolidate
{
    protected $rule = [
        'oid' => 'require|string',
        'receipt_data' => 'require|string',
        'transaction_id' => 'string|max:128',
    ];

    protected $message = [
        'oid.require' => '400201|oid is required',
        'oid.string' => '400202|oid must be string',
        'receipt_data.require' => '400203|receipt_data is required',
        'receipt_data.string' => '400204|receipt_data must be string',
        'transaction_id.string' => '400205|transaction_id must be string',
        'transaction_id.max' => '400206|transaction_id must be less than 128 characters',
    ];
}