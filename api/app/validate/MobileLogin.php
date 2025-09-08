<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class MobileLogin extends ZHYVolidate
{
    protected $rule = [
        'mcode' => 'require|regex:^\+\d{1,3}$',
        'mobile' => 'require|regex:^1[3-9]\d{9}$',
        'verify_code' => 'require|string|max:6',
        'version_number' => 'require|integer',
        'channel' => 'require|string|max:32',
        'oaid' => 'string|max:128',
        'device_id' => 'string|max:128',
        'need_user_detail' => 'integer|in:0,1',
    ];  

    protected $message = [
        'mcode.require' => '400104|mcode must be required',
        'mcode.regex' => '400105|mcode must be valid',
        'mobile.require' => '400106|mobile must be required',
        'mobile.regex' => '400107|mobile must be valid',
        'verify_code.require' => '400108|verify_code must be required',
        'verify_code.string' => '400109|verify_code must be string',
        'verify_code.max' => '400110|verify_code length must be less than 6',
        'version_number.require' => '400111|version_number must be required',
        'version_number.integer' => '400112|version_number must be integer',
        'channel.require' => '400113|channel must be required',
        'channel.max' => '400114|channel length must be less than 32',
        'need_user_detail.integer' => '400115|need_user_detail must be integer',
        'need_user_detail.in' => '400116|need_user_detail must be in 0 or 1',
        'oaid.string' => '400117|oaid must be string',
        'oaid.max' => '400114|oaid length must be less than 128',
        'device_id.string' => '400115|device_id must be string',
        'device_id.max' => '400116|device_id length must be less than 128',
    ];  
}