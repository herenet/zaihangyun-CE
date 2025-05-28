# 创建订单（Apple）

---
- [接口说明](#section-1)
- [请求路径](#section-2)
- [请求参数](#section-3)
- [请求示例代码](#section-4)
- [返回响应](#section-5)
- [返回字段解析](#section-6)
- [业务流程图](#section-7)
- [错误码说明](#section-8)

<a name="section-1"></a>
## 接口说明

创建Apple内购订单，支持消耗型、非消耗型和订阅型产品

该接口需要在APP后台启用订单和Apple支付配置

> {warning} 订阅型产品，购买成功之后，后续的续费、取消、退款等事件系统会通过Apple S2S通知自动处理，保持订阅状态与Apple平台同步

### 注意事项

1. 每个用户1秒最多创建1个订单
2. 需要先配置Apple开发者S2S通知和IAP产品信息
3. 支持沙盒环境和生产环境
4. **创建订单后需要调用verify接口验证Apple凭证**

### 特殊说明

1. **限流提示**: 如果触发限流会返回 `operation too frequent` 错误
2. **产品匹配**: apple_product_id必须与商品配置的IAP产品ID完全匹配
3. **环境配置**: 沙盒环境用于测试，生产环境用于正式发布
4. **后续步骤**: 创建订单成功后，需要调用 `/v1/apple/order/verify` 接口验证Apple支付凭证

<a name="section-2"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/apple/order/create` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-3"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 说明 |
| -- | -- | -- | -- | -- |
| pid | integer | 是 | - | 商品ID |
| apple_product_id | string | 是 | 最大128字符 | Apple产品标识符，如：com.example.premium_monthly |
| environment | string | 否 | Sandbox, Production | 环境类型，默认为Production |

<a name="section-4"></a>
## 请求示例代码

- application/x-www-form-urlencoded方式

```javascript
curl --location --request POST 'https://api.zaihangyun.com/api/apple-order/create' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Authorization: Bearer ZaihangyunToken' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'pid=267370' \
--data-urlencode 'apple_product_id=com.example.premium_monthly' \
--data-urlencode 'environment=Production'
```

- application/json方式：

```javascript
curl --location --request POST 'https://api.zaihangyun.com/api/apple-order/create' \
--header 'User-Agent: Apifox/1.0.0 (https://apifox.com)' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer ZaihangyunToken' \
--header 'Accept: */*' \
--header 'Host: api.zaihangyun.com' \
--header 'Connection: keep-alive' \
--data-raw '{
  "pid": 267370,
  "apple_product_id": "com.example.premium_monthly",
  "environment": "Production"
}'
```

<a name="section-5"></a>
## 返回响应

- 创建订单成功响应

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "oid": "32025052815063297469627",
        "apple_product_id": "timestamp.kongmuhu.com.forver_vip",
        "amount": 1100,
        "environment": "Sandbox"
    }
}
```

- 错误返回

```json
{
    "code": 400199,
    "msg": "product not found"
}
```

<a name="section-6"></a>
## 返回字段解析

### 成功响应 data 对象
| 字段名 | 类型 | 说明 |
| -- | -- | -- |
| oid | string | 订单号，用于后续验证凭证 |
| apple_product_id | string | Apple产品标识符 |
| amount | number | 商品价格（单位：分） |
| environment | string | 环境类型（Sandbox/Production） |

<a name="section-7"></a>
## 业务流程图

### Apple内购支付流程
<image src="/images/docs/iap_buy.png" width="1300px"/>

<a name="section-8"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400101` | pid参数缺失 |
| `400102` | pid参数类型必须是整数 |
| `400103` | apple_product_id参数缺失 |
| `400104` | apple_product_id参数类型必须是字符串 |
| `400105` | apple_product_id参数长度不能超过128个字符 |
| `400106` | environment参数类型必须是字符串 |
| `400107` | environment参数值必须是Sandbox或Production |
| `400192` | IAP接口检查未通过 |
| `400193` | 未找到IAP配置 |
| `400194` | 未开启Apple IAP购买功能 |
| `400195` | 未开启订单接口 |
| `400196` | 未找到订单接口配置 |
| `400197` | Apple产品ID不匹配 |
| `400198` | 商品已下架 |
| `400199` | 商品不存在 |
| `400250` | 创建订单失败 |
