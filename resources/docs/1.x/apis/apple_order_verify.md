# 支付凭证校验（Apple）

---
- [接口说明](#section-1)
- [业务流程说明](#section-2)
- [请求路径](#section-3)
- [请求参数](#section-4)
- [请求示例代码](#section-5)
- [返回响应](#section-6)
- [错误码说明](#section-7)

<a name="section-1"></a>
## 接口说明

验证苹果应用内购买凭证，确认订单支付状态并执行相应的业务逻辑。

### 注意事项

1. 需要用户登录后才能验证支付凭证
2. 只能验证当前登录用户的订单
3. 订单必须处于待验证状态才能进行验证
4. 验证成功后会自动执行会员开通等业务逻辑
5. 支持沙盒和生产环境的凭证验证
6. 会验证Bundle ID的一致性以确保安全性
7. 如果订单已验证成功，会返回成功状态而不重复处理

<a name="section-2"></a>
## 业务流程说明

1. **参数验证**：验证必要参数的完整性和格式
2. **订单检查**：确认订单存在且属于当前用户
3. **状态检查**：检查订单当前的支付状态
4. **配置验证**：验证IAP相关配置的完整性
5. **凭证验证**：向苹果服务器验证支付凭证
6. **Bundle ID验证**：确保凭证中的Bundle ID与配置一致
7. **订单更新**：更新订单状态和相关信息
8. **业务逻辑**：执行会员开通等业务逻辑

### 注意事项

- 凭证验证会根据订单环境（沙盒/生产）选择对应的苹果验证服务器
- 验证成功后会自动根据产品类型执行相应的业务逻辑（如开通会员）
- 对于订阅类产品，会设置正确的过期时间和自动续费状态
- 系统会记录所有S2S回调原始数据以便后续问题排查

<a name="section-3"></a>
## 请求路径

| Method | URI Path | 鉴权方式 |
| -- | -- | -- |
| POST | `/v1/order/apple/verify` | [Token认证](/{{route}}/{{version}}/intro#section-4) |

<a name="section-4"></a>
## 请求参数

### 公共参数
| 参数名 | 类型 | 取值范围 | 是否必须 | 说明 |
| -- | -- | -- | -- | -- |
| Authorization | string | - | 是 | 用户登录Token |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 说明 |
| -- | -- | -- | -- | -- |
| oid | string | 是 | - | 订单号 |
| receipt_data | string | 是 | - | 苹果支付凭证（Base64编码） |
| transaction_id | string | 否 | 最大128字符 | 苹果交易ID |

<a name="section-5"></a>
## 请求示例代码

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/order/apple/verify' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "oid": "AO202404151530001",
    "receipt_data": "ewoJInNpZ25hdHVyZSIgPSAiQWpCa2NsOUJjQ0JoYnpOdVpTQnRhVzVwYldGc0lIUmxjM1FnYzJsbmJtRjBkWEpsT2lBaVFXeGxlR0Z1WkdWeUlGUmxjM1FuT3lCVGFXZHVZWFIxY21VZ1ZtVnljMmx2YmpvZ01TNHdMakE9IjsKCSJwdXJjaGFzZS1pbmZvIiA9ICJld29nSUNBaWIzSnBaMmx1WVd3dGNIVnlZMmhoYzJVdFpHRjBaU0lnUFNBaU1qQXhOQzB3TkMweE5TQXhOVG96TURvek1DNHdNREF3TURBd1dpSTdDaUFnSUNBaWIzSnBaMmx1WVd3dGRISmhibk5oWTNScGIyNHRhV1FpSUQwZ0lqRXdNREF3TURBeE1qTTBOVFkzT0RraU93b2dJQ0FnSW1KMWJtUnNaUzFwWkNJZ1BTQWlZMjl0TG1WNFlXMXdiR1V1WVhCd0xtMXZiblJvYkhraTt3b2dJQ0FnSW5CeWIyUjFZM1F0YVdRaUlEMGdJbU52YlM1bGVHRnRjR3hsTG1Gd2NHNXRiMjUwYUd4NUlqc0tJQ0FnSUNKd2RYSmphR0Z6WlMxa1lYUmxJaUE5SUNJeU1ERTBMVEEwTFRFMUlERTFPak13T2pNd0xqQXdNREF3TURCYUlqc0tJQ0FnSUNKdmNtbG5hVzVoYkMxd2RYSmphR0Z6WlMxa1lYUmxJaUE5SUNJeU1ERTBMVEEwTFRFMUlERTFPak13T2pNd0xqQXdNREF3TURCYUlqc0tJQ0FnSUNKMGNtRnVjMkZqZEdsdmJpMXBaQ0lnUFNBaU1UQXdNREF3TURFeU16UTFOamM0T1NJN0NpQWdJQ0FpY1hWaGJuUnBkSGtpSUQwZ0lqRWlPd29nSUNBZ0ltbDBaVzB0YVdRaUlEMGdJakV3TURBd01EQXhNak0wTlRZM09Ea2lPd29nSUNBZ0ltbDBaVzB0ZEhsd1pTSWdQU0FpWVhCd0lqc0tJQ0FnSUNKaGNIQXRhWFJsYlMxcFpDSWdQU0FpTVRBd01EQXdNREV5TXpRMU5qYzRPU0k3Q2lBZ0lDQWlkbVZ5YzJsdmJpMWxlSFJsY201aGJDMXBaR1Z1ZEdsbWFXVnlJaUE5SUNJeE1qTTBOVFkzT0RraU93b2dJQ0FnSW1KcFpDSWdQU0FpWTI5dExtVjRZVzF3YkdVdVlYQndJanNLSUNBZ0lDSjJaWEp6YVc5dUxXVjRkR1Z5Ym1Gc0xXbGtaVzUwYVdacFpYSWlJRDBnSWpFeU16UTFOamM0T1NJN0NuMD0iOwp9",
    "transaction_id": "1000000123456789"
}'
```

<a name="section-6"></a>
## 返回响应

- 验证成功

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "oid": "AO202404151530001",
        "payment_status": 2,
        "transaction_id": "1000000123456789"
    }
}
```

- 订单已验证成功（重复验证）

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "oid": "AO202404151530001",
        "payment_status": 2,
        "transaction_id": "1000000123456789"
    }
}
```

- 验证失败

```json
{
    "code": 400193,
    "msg": "receipt verification failed",
    "data": {
        "error": "Invalid receipt data"
    }
}
```

### 返回参数说明

| 参数名 | 类型 | 说明 |
| -- | -- | -- |
| oid | string | 订单号 |
| payment_status | integer | 支付状态：2-支付成功 |
| transaction_id | string | 苹果交易ID |

<a name="section-7"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

| 错误码 | 说明 |
| -- | -- |
| `400201` | oid参数缺失 |
| `400202` | oid参数必须为字符串 |
| `400203` | receipt_data参数缺失 |
| `400204` | receipt_data参数必须为字符串 |
| `400205` | transaction_id参数必须为字符串 |
| `400206` | transaction_id参数长度不能超过128字符 |
| `400199` | 订单不存在 |
| `400198` | 订单已验证成功，但交易ID不匹配 |
| `400197` | 订单验证失败 |
| `400196` | IAP配置未找到 |
| `400195` | Apple开发者S2S配置未找到 |
| `400194` | Apple开发者S2S配置未找到 |
| `400193` | 凭证验证失败 |
| `400191` | Bundle ID未配置 |
| `400190` | 凭证中未找到Bundle ID |
| `400189` | Bundle ID不匹配 |
| `400250` | 验证苹果订单失败（系统错误） |