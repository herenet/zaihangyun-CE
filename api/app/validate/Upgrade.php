<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class Upgrade extends ZHYVolidate
{
    protected $rule = [
        'device_uuid' => 'require|string|max:128',
        'channel_name' => 'string|max:32',
        'version_number' => 'require|integer',
    ];

    protected $message = [
        'device_uuid.require' => '400101|device_uuid is required',
        'device_uuid.string' => '400102|device_uuid must be string',
        'device_uuid.max' => '400103|device_uuid length must be less than 128 characters',
        'channel_name.string' => '400104|channel_name must be string',
        'channel_name.max' => '400105|channel_name length must be less than 32 characters',
        'version_number.require' => '400109|version_number is required',
        'version_number.integer' => '400110|version_number must be integer',
    ];
}