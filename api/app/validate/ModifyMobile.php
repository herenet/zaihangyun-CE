<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class ModifyMobile extends ZHYVolidate
{
    protected $rule = [
        'mcode' => 'require|regex:^\+\d{1,3}$',
        'mobile' => 'require|regex:^1[3-9]\d{9}$',
        'verify_code' => 'require|string|max:6',
    ];  

    protected $message = [
        'mcode.require' => '400104|mcode must be required',
        'mcode.regex' => '400105|mcode must be valid',
        'mobile.require' => '400106|mobile must be required',
        'mobile.regex' => '400107|mobile must be valid',
        'verify_code.require' => '400108|verify_code must be required',
        'verify_code.string' => '400109|verify_code must be string',
        'verify_code.max' => '400110|verify_code length must be less than 6',
    ];  
}