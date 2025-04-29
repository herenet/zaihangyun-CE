<?php

namespace App\SaaSAdmin\Extensions;

use Encore\Admin\ApiTester\ApiTester as BaseApiTester;

class ZaihangyunApiTester extends BaseApiTester
{
    /**
     * 重写获取路由的方法，返回自定义API列表
     */
    public function getRoutes()
    {
        return [
            [
                'method' => 'POST',
                'uri' => '/v1/login/wechat',
                'title' => '微信登录',
                'token' => false,
                'parameters' => json_encode([
                    ['name' => 'appkey', 'type' => 'string', 'required' => true, 'readonly' => true, 'description' => '系统分配的appkey，提交时系统自动填充', 'defaultValue' => '{{appkey}}'],
                    ['name' => 'timestamp', 'type' => 'string', 'readonly' => true, 'description' => '当前时间戳，提交时系统自动填充', 'defaultValue' => '{{timestamp}}', 'required' => true],
                    ['name' => 'sign', 'type' => 'string', 'readonly' => true, 'description' => '签名，提交时系统自动生成', 'defaultValue' => '{{sign}}', 'required' => true],
                    ['name' => 'code', 'type' => 'string', 'required' => true, 'description' => '微信授权码'],
                    ['name' => 'version_number', 'type' => 'number', 'required' => true, 'description' => '版本号'],
                    ['name' => 'channel', 'type' => 'string', 'required' => true, 'description' => '渠道'],
                    ['name' => 'oaid', 'type' => 'string', 'required' => true, 'description' => '设备oaid'],
                    ['name' => 'need_user_detail', 'type' => 'number', 'required' => true, 'description' => '是否需要用户信息：0-不需要，1-需要'],
                    ['name' => 'device_id', 'type' => 'string', 'required' => true, 'description' => '设备id'],
                ])
            ],
            [
                'method' => 'POST',
                'uri' => 'api/example/create',
                'parameters' => json_encode([
                    ['name' => 'name', 'type' => 'string', 'required' => true],
                    ['name' => 'email', 'type' => 'string', 'required' => true]
                ])
            ],
            // 添加更多API...
        ];
    }
}