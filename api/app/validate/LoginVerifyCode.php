<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class LoginVerifyCode extends ZHYVolidate
{
    protected $rule = [
        'mcode' => 'require|regex:^\+\d{1,3}$',
        'mobile' => 'require|regex:^1[3-9]\d{9}$',
    ];  

    protected $message = [
        'mcode.require' => '400104|mcode must be required',
        'mcode.regex' => '400105|mcode must be valid',
        'mobile.require' => '400106|mobile must be required',
        'mobile.regex' => '400107|mobile must be valid',
    ];  
}