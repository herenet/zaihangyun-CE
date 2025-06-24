# 微信支付v3版本部署说明

## 概述

系统已升级到微信支付APIv3版本，提供更安全的证书签名验证机制。

## 部署步骤

### 1. 证书配置

从微信商户平台下载以下文件：

1. **商户API私钥** (`apiclient_key.pem`)
   - 在商户平台 -> API安全 -> API证书 -> 下载证书
   - 将文件放到 `storage/app/certs/apiclient_key.pem`

2. **微信支付平台证书** (`wechat_pay_platform_cert.pem`)
   - 可通过微信支付SDK工具下载
   - 或使用微信支付官方工具获取
   - 将文件放到 `storage/app/certs/wechat_pay_platform_cert.pem`

### 2. 环境变量配置

在 `.env` 文件中添加以下配置：

```bash
# 微信支付配置（APIv3版本）
WECHAT_PAY_APP_ID=wx607fb8cd81ac2944
WECHAT_PAY_MCH_ID=1637149154

# 证书配置
WECHAT_PAY_CERT_SERIAL_NO=你的证书序列号
WECHAT_PAY_PRIVATE_KEY_PATH=/path/to/storage/app/certs/apiclient_key.pem
WECHAT_PAY_PLATFORM_CERT_PATH=/path/to/storage/app/certs/wechat_pay_platform_cert.pem

# APIv3密钥（32位字符串）
WECHAT_PAY_API_V3_KEY=你的32位APIv3密钥

# 回调地址
WECHAT_PAY_NOTIFY_URL=https://your-domain.com/api/AXstsastaxa/wechat/pay/callback
```

### 3. 获取证书序列号

可以使用以下命令获取证书序列号：

```bash
openssl x509 -in storage/app/certs/apiclient_cert.pem -noout -serial
```

### 4. 设置APIv3密钥

1. 登录微信商户平台
2. 进入 "账户中心" -> "API安全"
3. 设置APIv3密钥（32位字符串）
4. 将密钥配置到环境变量 `WECHAT_PAY_API_V3_KEY`

### 5. 文件权限

确保证书文件有正确的权限：

```bash
chmod 600 storage/app/certs/*.pem
chown www-data:www-data storage/app/certs/*.pem
```

## 主要变化

### v2 -> v3 升级变化

1. **签名方式**：从MD5签名改为RSA-SHA256证书签名
2. **数据格式**：从XML改为JSON
3. **回调格式**：回调数据加密，需要使用APIv3密钥解密
4. **接口地址**：使用新的v3接口地址

### 技术实现

- 使用官方 `wechatpay/wechatpay` SDK
- 支持证书自动验证和签名
- 回调数据AES-256-GCM解密
- 完整的错误处理和日志记录

## 测试验证

### 1. 下单测试

访问套餐购买页面，选择套餐进行测试下单。

### 2. 回调测试

可以使用微信支付沙箱环境进行回调测试。

### 3. 日志检查

查看Laravel日志文件，确认：
- 微信支付初始化成功
- 下单请求和响应正常
- 回调签名验证通过
- 订单状态更新正确

```bash
tail -f storage/logs/laravel.log | grep "微信支付"
```

## 常见问题

### 1. 证书加载失败

**错误**：`私钥文件不存在` 或 `无法读取私钥文件内容`

**解决**：
- 确认证书文件路径正确
- 检查文件权限
- 确认证书文件格式正确（PEM格式）

### 2. 签名验证失败

**错误**：`微信支付v3回调签名验证失败`

**解决**：
- 确认平台证书是最新的
- 检查证书序列号是否正确
- 验证时间戳是否在有效范围内

### 3. 解密失败

**错误**：`APIv3密钥长度不正确` 或 `解密失败`

**解决**：
- 确认APIv3密钥为32位字符串
- 检查密钥是否与商户平台设置一致
- 确认加密算法为AES-256-GCM

## 安全建议

1. **证书保护**：证书文件权限设置为600，仅应用可读
2. **密钥保护**：APIv3密钥不要硬编码，使用环境变量
3. **HTTPS**：回调地址必须使用HTTPS
4. **日志脱敏**：生产环境注意敏感信息脱敏

## 支持

如有问题，请查看：
- [微信支付官方文档](https://pay.weixin.qq.com/wiki/doc/apiv3/index.shtml)
- [wechatpay-php SDK文档](https://github.com/wechatpay-im/wechatpay-php)
- Laravel应用日志文件 