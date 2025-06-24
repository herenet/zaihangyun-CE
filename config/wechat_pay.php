<?php

return [
    // 微信支付基本配置
    'app_id' => env('WECHAT_PAY_APP_ID', 'wxe9afa2185ddce5aa'),
    'mch_id' => env('WECHAT_PAY_MCH_ID', '1637149154'),
    
    // 证书配置（APIv3）
    'cert_serial_no' => env('WECHAT_PAY_CERT_SERIAL_NO', '132C35F282F8B457A755015991666DF676D8EC59'),
    'private_key_path' => env('WECHAT_PAY_PRIVATE_KEY_PATH', storage_path('app/certs/apiclient_key.pem')),
    'platform_cert_path' => env('WECHAT_PAY_PLATFORM_CERT_PATH', storage_path('app/certs/wechat_pay_platform_cert.pem')),
    
    // APIv3密钥（32位字符串，用于回调解密）
    'api_v3_key' => env('WECHAT_PAY_API_V3_KEY', 'zs8jymmc3s5ud2a4ta9bkvlugndlsu63'),
    
    // 回调地址
    'notify_url' => env('WECHAT_PAY_NOTIFY_URL', '/api/AXstsastaxa/wechat/pay/callback'),
    
    // 其他配置
    'timeout_express' => '2h', // 订单超时时间
    'currency' => 'CNY', // 货币类型
    
    // API版本
    'version' => 'v3', // 使用v3版本的Native支付
]; 