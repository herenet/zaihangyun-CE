<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class HuaweiLogin extends ZHYVolidate
{
    protected $rule = [
        'code' => 'require|string|max:512',
        'version_number' => 'require|integer',
        'channel' => 'require|string|max:32',
        'oaid' => 'string|max:128',
        'device_id' => 'string|max:128',
        'need_user_detail' => 'integer|in:0,1',
    ];  

    protected $message = [
        'code.require' => '400120|code must be required',
        'code.string' => '400121|code must be string',
        'code.max' => '400122|code length must be less than 512',
        'version_number.require' => '400123|version_number must be required',
        'version_number.integer' => '400124|version_number must be integer',
        'channel.require' => '400125|channel must be required',
        'channel.max' => '400126|channel length must be less than 32',
        'need_user_detail.integer' => '400127|need_user_detail must be integer',
        'need_user_detail.in' => '400128|need_user_detail must be in 0 or 1',
        'oaid.string' => '400129|oaid must be string',
        'oaid.max' => '400130|oaid length must be less than 128',
        'device_id.string' => '400131|device_id must be string',
        'device_id.max' => '400132|device_id length must be less than 128',
    ];  
}