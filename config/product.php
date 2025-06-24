<?php

return [
    'free' => [
        'name' => '免费版',
        'price' => 0,
        'duration' => 'permanent', // 永久有效
        'app_limit' => 1,
        'request_limit' => 10000,
        'attach_size' => '100MB',
        'module_enable' => [
            'app_sets' => true,
            'user_mng' => true,
            'sales_mng' => false,
            'doc_mng' => true,
            'msg_mng' => true,
        ],
    ],
    'basic' => [
        'name' => '基础版',
        'price' => 1,
        'duration' => 365, // 365天
        'app_limit' => 3,
        'request_limit' => 30000,
        'attach_size' => '1GB',
        'module_enable' => [
            'app_sets' => true,
            'user_mng' => true,
            'sales_mng' => true,
            'doc_mng' => true,
            'msg_mng' => true,
        ],
    ],
    'adv' => [
        'name' => '进阶版',
        'price' => 1,
        'duration' => 365, // 365天
        'app_limit' => 10,
        'request_limit' => 100000,
        'attach_size' => '10GB',
        'module_enable' => [
            'app_sets' => true,
            'user_mng' => true,
            'sales_mng' => true,
            'doc_mng' => true,
            'msg_mng' => true,
        ],
    ],
    'pro' => [
        'name' => '专业版',
        'price' => 1,
        'duration' => 365, // 永久有效
        'app_limit' => 50,
        'request_limit' => 500000,
        'attach_size' => '100GB',
        'module_enable' => [
            'app_sets' => true,
            'user_mng' => true,
            'sales_mng' => true,
            'doc_mng' => true,
            'msg_mng' => true,
        ],
    ],
    'company' => [
        'name' => '企业版',
        'price' => 1,
        'duration' => 365, // 永久有效
        'app_limit' => 9999,
        'request_limit' => 999999999, // 表示不限制
        'attach_size' => '1TB',
        'module_enable' => [
            'app_sets' => true,
            'user_mng' => true,
            'sales_mng' => true,
            'doc_mng' => true,
            'msg_mng' => true,
        ],
    ],
];