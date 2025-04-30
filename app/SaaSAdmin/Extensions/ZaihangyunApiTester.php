<?php

namespace App\SaaSAdmin\Extensions;

use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
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
                    ['name' => 'oaid', 'type' => 'string', 'description' => '设备oaid'],
                    ['name' => 'need_user_detail', 'type' => 'number', 'description' => '是否需要用户信息：0-不需要，1-需要'],
                    ['name' => 'device_id', 'type' => 'string', 'description' => '设备id'],
                ])
            ],
            [
                'method' => 'POST',
                'uri' => '/v1/login/verify_code',
                'title' => '验证码登录，发送验证码',
                'parameters' => json_encode([
                    ['name' => 'appkey', 'type' => 'string', 'required' => true, 'readonly' => true, 'description' => '系统分配的appkey，提交时系统自动填充', 'defaultValue' => '{{appkey}}'],
                    ['name' => 'timestamp', 'type' => 'string', 'readonly' => true, 'description' => '当前时间戳，提交时系统自动填充', 'defaultValue' => '{{timestamp}}', 'required' => true],
                    ['name' => 'sign', 'type' => 'string', 'readonly' => true, 'description' => '签名，提交时系统自动生成', 'defaultValue' => '{{sign}}', 'required' => true],
                    ['name' => 'mcode', 'type' => 'string', 'required' => true, 'description' => '国家区号', 'defaultValue' => '+86'],
                    ['name' => 'mobile', 'type' => 'string', 'required' => true, 'description' => '手机号'],
                ])
            ],
            [
                'method' => 'POST',
                'uri' => '/v1/login/mobile',
                'title' => '手机号验证码登录',
                'parameters' => json_encode([
                    ['name' => 'appkey', 'type' => 'string', 'required' => true, 'readonly' => true, 'description' => '系统分配的appkey，提交时系统自动填充', 'defaultValue' => '{{appkey}}'],
                    ['name' => 'timestamp', 'type' => 'string', 'readonly' => true, 'description' => '当前时间戳，提交时系统自动填充', 'defaultValue' => '{{timestamp}}', 'required' => true],
                    ['name' => 'sign', 'type' => 'string', 'readonly' => true, 'description' => '签名，提交时系统自动生成', 'defaultValue' => '{{sign}}', 'required' => true],
                    ['name' => 'mcode', 'type' => 'string', 'required' => true, 'description' => '国家区号', 'defaultValue' => '+86'],
                    ['name' => 'mobile', 'type' => 'string', 'required' => true, 'description' => '手机号'],
                    ['name' => 'verify_code', 'type' => 'string', 'required' => true, 'description' => '验证码'],
                    ['name' => 'version_number', 'type' => 'number', 'required' => true, 'description' => '版本号'],
                    ['name' => 'channel', 'type' => 'string', 'required' => true, 'description' => '渠道'],
                    ['name' => 'oaid', 'type' => 'string', 'description' => '设备oaid'],
                    ['name' => 'need_user_detail', 'type' => 'number', 'defaultValue' => 0, 'description' => '是否需要用户信息：0-不需要，1-需要'],
                    ['name' => 'device_id', 'type' => 'string', 'description' => '设备id'],
                ])
            ],
            [
                'method' => 'GET',
                'uri' => '/v1/user/info',
                'title' => '获取当前登录用户信息',
                'token' => true,
                'parameters' => json_encode([]),
            ],
            [
                'method' => 'GET',
                'uri' => '/v1/article/list',
                'title' => '文档列表',
                'parameters' => json_encode([
                    ['name' => 'appkey', 'type' => 'string', 'required' => true, 'readonly' => true, 'description' => '系统分配的appkey，提交时系统自动填充', 'defaultValue' => '{{appkey}}'],
                    ['name' => 'timestamp', 'type' => 'string', 'readonly' => true, 'description' => '当前时间戳，提交时系统自动填充', 'defaultValue' => '{{timestamp}}', 'required' => true],
                    ['name' => 'sign', 'type' => 'string', 'readonly' => true, 'description' => '签名，提交时系统自动生成', 'defaultValue' => '{{sign}}', 'required' => true],
                    ['name' => 'page', 'type' => 'number', 'description' => '页码'],
                    ['name' => 'page_size', 'type' => 'number', 'description' => '每页条数'],
                ])
            ],
            // 添加更多API...
        ];
    }

    public function call($method, $uri, $parameters = [], $token = null)
    {
        $client = new Client();

        $reqeust_params = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $parameters,
            'on_stats' => function (TransferStats $stats) use (&$requestInfo) {
                // 获取请求对象
                $request = $stats->getRequest();
                
                // 获取请求方法和URL（包含查询参数）
                $method = $request->getMethod();
                $uri = $request->getUri();
                $path = $uri->getPath();
                $query = $uri->getQuery(); // 获取查询字符串
                
                // 解析查询参数
                parse_str($query, $queryParams);
                
                // 获取请求体内容
                $body = (string)$request->getBody();
                $jsonData = json_decode($body, true);
                
                // 存储所有信息
                $requestInfo = [
                    'method' => $method,
                    'url' => (string)$uri,
                    'path' => $path,
                    'query_string' => $query,
                    'query_params' => $queryParams,
                    'body' => $body,
                    'json_data' => $jsonData,
                    'headers' => $request->getHeaders(),
                    'transfer_time' => $stats->getTransferTime()
                ];
            }
        ];

        if ($token && $token !== '') {
            $reqeust_params['headers']['Authorization'] = 'Bearer ' . $token;
        }

        $response = $client->request($method, $uri, $reqeust_params);

        return [$response, $requestInfo];
    }
}