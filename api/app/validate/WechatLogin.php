<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class WechatLogin extends ZHYVolidate
{
    protected $rule = [
        'code' => 'require|string|max:128',
        'version_number' => 'require|integer',
        'channel' => 'require|string|max:32',
        'oaid' => 'string|max:128',
        'device_id' => 'string|max:128',
        'need_user_detail' => 'integer|in:0,1',
    ];  

    protected $message = [
        'code.require' => '400104|code must be required',
        'code.string' => '400105|code must be string',
        'code.max' => '400106|code length must be less than 128',
        'version_number.require' => '400107|version_number must be required',
        'version_number.integer' => '400108|version_number must be integer',
        'channel.require' => '400109|channel must be required',
        'channel.max' => '400110|channel length must be less than 32',
        'need_user_detail.integer' => '400111|need_user_detail must be integer',
        'need_user_detail.in' => '400112|need_user_detail must be in 0 or 1',
        'oaid.string' => '400113|oaid must be string',
        'oaid.max' => '400114|oaid length must be less than 128',
        'device_id.string' => '400115|device_id must be string',
        'device_id.max' => '400116|device_id length must be less than 128',
    ];  
}