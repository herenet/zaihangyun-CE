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
                'method' => 'GET',
                'uri' => '/v1/app/config',
                'title' => '获取APP配置',
                'doc_url' => config('app.url').'/docs/1.x/apis/app_config',
                'parameters' => json_encode([
                    ['name' => 'appkey', 'type' => 'string', 'required' => true, 'readonly' => true, 'description' => '系统分配的appkey，提交时系统自动填充', 'defaultValue' => '{{appkey}}'],
                    ['name' => 'timestamp', 'type' => 'string', 'readonly' => true, 'description' => '当前时间戳，提交时系统自动填充', 'defaultValue' => '{{timestamp}}', 'required' => true],
                    ['name' => 'sign', 'type' => 'string', 'readonly' => true, 'description' => '签名，提交时系统自动生成', 'defaultValue' => '{{sign}}', 'required' => true],
                    ['name' => 'name', 'type' => 'string', 'required' => true, 'description' => '配置名称'],
                ])
            ],
            [
                'method' => 'POST',
                'uri' => '/v1/login/wechat',
                'title' => '微信登录',
                'token' => false,
                'doc_url' => config('app.url').'/docs/1.x/apis/login_wechat',
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
                'doc_url' => config('app.url').'/docs/1.x/apis/login_verify_code',
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
                'doc_url' => config('app.url').'/docs/1.x/apis/login_mobile',
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
                'doc_url' => config('app.url').'/docs/1.x/apis/user_info',
                'token' => true,
                'parameters' => json_encode([]),
            ],
            [
                'method' => 'GET',
                'uri' => '/v1/article/list',
                'title' => '文档列表',
                'doc_url' => config('app.url').'/docs/1.x/apis/article_list',
                'parameters' => json_encode([
                    ['name' => 'appkey', 'type' => 'string', 'required' => true, 'readonly' => true, 'description' => '系统分配的appkey，提交时系统自动填充', 'defaultValue' => '{{appkey}}'],
                    ['name' => 'timestamp', 'type' => 'string', 'readonly' => true, 'description' => '当前时间戳，提交时系统自动填充', 'defaultValue' => '{{timestamp}}', 'required' => true],
                    ['name' => 'sign', 'type' => 'string', 'readonly' => true, 'description' => '签名，提交时系统自动生成', 'defaultValue' => '{{sign}}', 'required' => true],
                    ['name' => 'page', 'type' => 'number', 'description' => '页码'],
                    ['name' => 'page_size', 'type' => 'number', 'description' => '每页条数'],
                ])
            ],
            [
                'method' => 'GET',
                'uri' => '/v1/product/list',
                'title' => '产品列表',
                'doc_url' => config('app.url').'/docs/1.x/apis/product_list',
                'parameters' => json_encode([
                    ['name' => 'appkey', 'type' => 'string', 'required' => true, 'readonly' => true, 'description' => '系统分配的appkey，提交时系统自动填充', 'defaultValue' => '{{appkey}}'],
                    ['name' => 'timestamp', 'type' => 'string', 'readonly' => true, 'description' => '当前时间戳，提交时系统自动填充', 'defaultValue' => '{{timestamp}}', 'required' => true],
                    ['name' => 'sign', 'type' => 'string', 'readonly' => true, 'description' => '签名，提交时系统自动生成', 'defaultValue' => '{{sign}}', 'required' => true],
                    ['name' => 'status', 'type' => 'number', 'description' => '状态：1-上架，2-下架'],
                    ['name' => 'platform', 'type' => 'number', 'description' => '平台：1-IOS, 2-Android'],
                    ['name' => 'type', 'type' => 'number', 'description' => '类型：1:时长会员, 2:永久会员, 99:自定义会员'],
                ]),
            ],
            [
                'method' => 'POST',
                'uri' => '/v1/order/create',
                'title' => '创建订单',
                'doc_url' => config('app.url').'/docs/1.x/apis/create_order',
                'token' => true,
                'parameters' => json_encode([
                    ['name' => 'pid', 'type' => 'string', 'required' => true, 'description' => '产品pid，从产品列表接口获取'],
                    ['name' => 'pay_channel', 'type' => 'number', 'required' => true, 'description' => '支付渠道：1:微信支付, 2:支付宝, 3:苹果支付'],
                    ['name' => 'channel', 'type' => 'number', 'description' => '用户注册来源渠道'],
                ])
            ],
            [
                'method' => 'GET',
                'uri' => '/v1/order/info',
                'title' => '获取订单详情',
                'doc_url' => config('app.url').'/docs/1.x/apis/order_info',
                'token' => true,
                'parameters' => json_encode([
                    ['name' => 'oid', 'type' => 'string', 'required' => true, 'description' => '订单号'],
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