# 套餐购买功能实现说明

## 功能概述

为BaaS系统实现了完整的套餐购买功能，支持微信Native支付，包含订单确认、支付、回调处理等完整流程。

## 主要特性

- ✅ 支持套餐新购、升级、续费
- ✅ 智能价格计算（升级按比例补差价）
- ✅ 微信Native扫码支付
- ✅ 支付状态实时轮询
- ✅ 安全的支付回调处理
- ✅ 事务保证数据一致性
- ✅ 完整的订单管理
- ✅ 响应式用户界面

## 文件结构

### 后端文件

```
config/
├── wechat_pay.php                     # 微信支付配置

app/Models/
├── SubscriptionOrder.php              # 套餐订单模型
└── Tenant.php                         # 租户模型（已修改）

app/Services/
└── SubscriptionWechatPayService.php   # 微信支付服务

app/Http/Controllers/
├── SubscriptionController.php         # 套餐购买控制器
└── API/WechatPayCallbackController.php # 支付回调控制器（已修改）

routes/
├── web.php                            # Web路由（已修改）
└── api.php                            # API路由（已修改）
```

### 前端文件

```
resources/views/
├── subscription/
│   ├── confirm.blade.php              # 订单确认页面
│   └── payment.blade.php              # 支付页面
└── pricing.blade.php                  # 套餐选择页面（已修改）
```

### 数据库文件

```
database_tables.sql                    # 数据库表SQL
```

## 业务逻辑

### 价格计算逻辑

1. **免费版 → 付费版**：按目标套餐全价收费
2. **付费版 → 同级续费**：按目标套餐全价收费，延长到期时间
3. **付费版 → 升级**：按比例补差价，重置计费周期

### 升级价格计算公式

```php
// 计算剩余天数
$remainingDays = max(0, now()->diffInDays($currentExpiresAt, false));

// 计算剩余价值（按分计算，避免小数问题）
$dailyCurrentPrice = intval($currentPrice / 365);
$remainingValue = $dailyCurrentPrice * $remainingDays;

// 计算升级价格
$upgradePrice = max(0, $targetPrice - $remainingValue);
```

### 订单状态流转

```
创建订单 → 待支付 → 已支付 → 更新租户套餐
         ↓
      支付失败/取消
```

## 部署步骤

### 1. 数据库配置

执行 `database_tables.sql` 中的SQL语句：

```sql
-- 创建套餐订单表
CREATE TABLE `subscription_orders` (...);

-- 为租户表添加到期时间字段
ALTER TABLE `tenant` ADD COLUMN `subscription_expires_at` timestamp NULL DEFAULT NULL;
```

### 2. 环境变量配置

在 `.env` 文件中添加微信支付配置：

```env
# 微信支付配置
WECHAT_PAY_APP_ID=your_wechat_app_id
WECHAT_PAY_MCH_ID=your_merchant_id
WECHAT_PAY_API_KEY=your_api_key
WECHAT_PAY_CERT_SERIAL_NO=your_cert_serial_no
WECHAT_PAY_PRIVATE_KEY_PATH=/path/to/storage/app/certs/wechat_pay_private_key.pem
WECHAT_PAY_PLATFORM_CERT_PATH=/path/to/storage/app/certs/wechat_pay_platform_cert.pem
WECHAT_PAY_NOTIFY_URL=/api/AXstsastaxa/wechat/pay/callback
```

### 3. 证书文件配置

将微信支付证书文件放置到 `storage/app/certs/` 目录：

```
storage/app/certs/
├── wechat_pay_private_key.pem     # 商户私钥
└── wechat_pay_platform_cert.pem   # 微信支付平台证书
```

### 4. 依赖包

确保已安装微信支付SDK：

```bash
composer require wechatpay/wechatpay
```

## 页面流程

### 用户购买流程

1. **选择套餐**：在 `/pricing` 页面选择套餐
2. **确认订单**：在 `/subscription/confirm` 页面确认价格和详情
3. **创建订单**：调用 `/subscription/create-order` API创建订单
4. **扫码支付**：在 `/subscription/payment/{orderId}` 页面扫码支付
5. **支付完成**：自动跳转到控制台

### 技术细节

- **前端轮询**：支付页面每秒轮询订单状态
- **回调处理**：微信支付成功后调用回调接口更新订单和租户信息
- **事务处理**：使用数据库事务保证数据一致性
- **签名验证**：验证微信支付回调签名确保安全性

## API接口

### Web接口

- `GET /subscription/confirm` - 订单确认页面
- `POST /subscription/create-order` - 创建订单
- `GET /subscription/payment/{orderId}` - 支付页面
- `GET /subscription/order-status/{orderId}` - 查询订单状态

### 回调接口

- `POST /api/AXstsastaxa/wechat/pay/callback` - 微信支付回调

## 注意事项

1. **证书安全**：确保微信支付证书文件权限正确，避免泄露
2. **回调验证**：严格验证回调签名，防止恶意请求
3. **幂等性**：回调处理支持重复调用，避免重复处理
4. **日志记录**：详细记录支付流程日志，便于问题排查
5. **错误处理**：完善的异常处理机制，提供友好的错误提示

## 扩展支持

当前实现预留了支付宝接入的扩展能力：

- 订单表包含 `pay_channel` 字段区分支付渠道
- 控制器和服务层可轻松扩展支持其他支付方式
- 前端页面支持多种支付方式选择

## 测试建议

1. **沙箱测试**：使用微信支付沙箱环境进行测试
2. **边界测试**：测试各种套餐升级组合
3. **并发测试**：测试同时创建多个订单的情况
4. **回调测试**：模拟各种回调场景（成功、失败、重复等）
5. **异常测试**：测试网络异常、数据库异常等情况 