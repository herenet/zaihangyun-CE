<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class AppleLogin extends ZHYVolidate
{
    protected $rule = [
        'user' => 'require|string|max:256',
        'full_name' => 'string|max:64',
        'token' => 'require|string|max:2048',
        'version_number' => 'require|integer',
        'channel' => 'require|string|max:32',
        'oaid' => 'string|max:128',
        'device_id' => 'string|max:128',
        'need_user_detail' => 'integer|in:0,1',
    ];  

    protected $message = [
        'user.require' => '400104|user must be required',
        'user.string' => '400105|user must be string',
        'user.max' => '400106|user length must be less than 256',
        'full_name.string' => '400107|full_name must be string',
        'full_name.max' => '400108|full_name length must be less than 64',
        'token.require' => '400109|token must be required',
        'token.string' => '400110|token must be string',
        'token.max' => '400111|token length must be less than 2048',
        'version_number.require' => '400112|version_number must be required',
        'version_number.integer' => '400113|version_number must be integer',
        'channel.require' => '400114|channel must be required',
        'channel.max' => '400115|channel length must be less than 32',
        'need_user_detail.integer' => '400116|need_user_detail must be integer',
        'need_user_detail.in' => '400117|need_user_detail must be in 0 or 1',
        'oaid.string' => '400118|oaid must be string',
        'oaid.max' => '400119|oaid length must be less than 128',
        'device_id.string' => '400120|device_id must be string',
        'device_id.max' => '400121|device_id length must be less than 128',
    ];  
}