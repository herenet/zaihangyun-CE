# 订单支付票据验证（Apple）

---
- [接口说明](#section-1)
- [业务流程说明](#section-2)
- [请求路径](#section-3)
- [请求参数](#section-4)
- [请求示例代码](#section-5)
- [返回响应](#section-6)
- [业务流程图](#section-7)
- [错误码说明](#section-8)

<a name="section-1"></a>
## 接口说明

验证苹果应用内购买票据，确认订单支付状态并执行相应的业务逻辑。

### 注意事项

1. 需要用户登录后才能验证支付票据
2. 只能验证当前登录用户的订单
3. 订单必须处于待验证状态才能进行验证
4. 验证成功后会自动执行会员开通等业务逻辑
5. 支持沙盒和生产环境的凭证验证
6. 会验证Bundle ID的一致性以确保安全性
7. 如果订单已验证成功，会返回成功状态而不重复处理
8. **支持交易ID重复处理**：如果同一交易ID对应不同订单，会自动合并处理

### 安全特性

- **Bundle ID验证**：确保凭证中的Bundle ID与配置的Bundle ID完全匹配
- **交易ID唯一性检查**：防止同一交易被重复使用
- **环境隔离**：沙盒和生产环境分别验证，确保安全性
- **凭证完整性验证**：通过Apple官方API验证凭证真实性

<a name="section-2"></a>
## 业务流程说明

### 主要验证步骤

1. **参数验证**：验证必要参数的完整性和格式
2. **用户权限检查**：确认订单存在且属于当前用户
3. **交易ID冲突处理**：检查是否存在相同交易ID的其他订单
4. **订单状态检查**：检查订单当前的支付状态
5. **配置完整性验证**：验证IAP和Apple开发者配置
6. **Apple凭证验证**：向苹果服务器验证支付凭证真实性
7. **Bundle ID安全验证**：确保凭证中的Bundle ID与配置一致
8. **订单状态更新**：更新订单状态和相关信息
9. **业务逻辑执行**：根据产品类型执行相应的业务逻辑

### 特殊处理逻辑

- **重复验证处理**：如果订单已验证成功且交易ID匹配，直接返回成功
- **交易ID冲突解决**：如果发现相同交易ID的不同订单，会自动合并处理
- **失败订单处理**：对于已标记为失败的订单，不允许重新验证
- **环境自适应**：根据订单创建时的环境选择对应的Apple验证服务器

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
| Authorization | string | Bearer + token | 是 | 登录令牌，**Header传值**，格式：Bearer eyJxx... |

### 业务参数
| 参数名 | 类型 | 是否必须 | 取值范围 | 说明 |
| -- | -- | -- | -- | -- |
| oid | string | 是 | - | 订单号，通过创建订单接口获得 |
| receipt_data | string | 是 | Base64编码 | 苹果支付凭证（从iOS客户端获取） |
| transaction_id | string | 否 | 最大128字符 | 苹果交易ID，用于额外验证 |

<a name="section-5"></a>
## 请求示例代码

```javascript
curl --location --request POST 'https://api.zaihangyun.com/v1/apple/order/verify' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...' \
--header 'User-Agent: Apifox/1.0.0' \
--data '{
    "oid": "32025052815063297469627",
    "receipt_data": "ewoJInNpZ25hdHVyZSIgPSAiQWpCa2NsOUJjQ0JoYnpOdVpTQnRhVzVwYldGc0lIUmxjM1FnYzJsbmJtRjBkWEpsT2lBaVFXeGxlR0Z1WkdWeUlGUmxjM1FuT3lCVGFXZHVZWFIxY21VZ1ZtVnljMmx2YmpvZ01TNHdMakE9IjsKCSJwdXJjaGFzZS1pbmZvIiA9ICJld29nSUNBaWIzSnBaMmx1WVd3dFkzVnlZMmhoYzJVdFpHRjBaU0lnUFNBaU1qQXhOQzB3TkMweE5TQXhOVG96TURvek1DNHdNREF3TURBd1dpSTdDaUFnSUNBaWIzSnBaMmx1WVd3dGRISmhibk5oWTNScGIyNHRhV1FpSUQwZ0lqRXdNREF3TURBeE1qTTBOVFkzT0RraU93b2dJQ0FnSW1KMWJtUnNaUzFwWkNJZ1BTQWlZMjl0TG1WNFlXMXdiR1V1WVhCd0xtMXZiblJvYkhraTt3b2dJQ0FnSW5CeWIyUjFZM1F0YVdRaUlEMGdJbU52YlM1bGVHRnRjR3hsTG1Gd2NHNXRiMjUwYUd4NUlqc0tJQ0FnSUNKd2RYSmphR0Z6WlMxa1lYUmxJaUE5SUNJeU1ERTBMVEEwTFRFMUlERTFPak13T2pNd0xqQXdNREF3TURCYUlqc0tJQ0FnSUNKdmNtbG5hVzVoYkMxd2RYSmphR0Z6WlMxa1lYUmxJaUE5SUNJeU1ERTBMVEEwTFRFMUlERTFPak13T2pNd0xqQXdNREF3TURCYUlqc0tJQ0FnSUNKMGNtRnVjMkZqZEdsdmJpMXBaQ0lnUFNBaU1UQXdNREF3TURFeU16UTFOamM0T1NJN0NpQWdJQ0FpY1hWaGJuUnBkSGtpSUQwZ0lqRWlPd29nSUNBZ0ltbDBaVzB0YVdRaUlEMGdJakV3TURBd01EQXhNak0wTlRZM09Ea2lPd29nSUNBZ0ltbDBaVzB0ZEhsd1pTSWdQU0FpWVhCd0lqc0tJQ0FnSUNKaGNIQXRhWFJsYlMxcFpDSWdQU0FpTVRBd01EQXdNREV5TXpRMU5qYzRPU0k3Q2lBZ0lDQWlkbVZ5YzJsdmJpMWxlSFJsY201aGJDMXBaR1Z1ZEdsbWFXVnlJaUE5SUNJeE1qTTBOVFkzT0RraU93b2dJQ0FnSW1KcFpDSWdQU0FpWTI5dExtVjRZVzF3YkdVdVlYQndJanNLSUNBZ0lDSjJaWEp6YVc5dUxXVjRkR1Z5Ym1Gc0xXbGtaVzUwYVdacFpYSWlJRDBnSWpFeU16UTFOamM0T1NJN0NuMD0iOwp9",
    "transaction_id": "1000000123456789"
}'
```

<a name="section-6"></a>
## 返回响应

### 验证成功

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "oid": "32025052815063297469627",
        "payment_status": 2,
        "transaction_id": "1000000123456789"
    }
}
```

### 订单已验证成功（重复验证）

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "oid": "32025052815063297469627",
        "payment_status": 2,
        "transaction_id": "1000000123456789"
    }
}
```

### 验证失败示例

#### 凭证验证失败
```json
{
    "code": 400193,
    "msg": "receipt verification failed",
    "data": {
        "error": "Invalid receipt data"
    }
}
```

#### Bundle ID不匹配
```json
{
    "code": 400189,
    "msg": "bundle id mismatch"
}
```

#### 订单不存在
```json
{
    "code": 400199,
    "msg": "order not found"
}
```

#### 交易ID不匹配
```json
{
    "code": 400198,
    "msg": "order already verified, but transaction id mismatch"
}
```

### 返回参数说明

| 参数名 | 类型 | 说明 |
| -- | -- | -- |
| oid | string | 订单号 |
| payment_status | integer | 支付状态：2-支付成功 |
| transaction_id | string | 苹果交易ID |

<a name="section-7"></a>
## 业务流程图

### Apple凭证验证流程
<image src="/images/docs/verify_apple_order.png" width="1300px"/>

### 交易ID冲突处理流程
<image src="/images/docs/verify_apple_oid.png" width="1300px" />

<a name="section-8"></a>
## 错误码说明

[查看全局错误码](/{{route}}/{{version}}/code#section-2)

### 参数验证错误
| 错误码 | 说明 |
| -- | -- |
| `400201` | oid参数缺失 |
| `400202` | oid参数必须为字符串 |
| `400203` | receipt_data参数缺失 |
| `400204` | receipt_data参数必须为字符串 |
| `400205` | transaction_id参数必须为字符串 |
| `400206` | transaction_id参数长度不能超过128字符 |

### 订单相关错误
| 错误码 | 说明 | 处理建议 |
| -- | -- | -- |
| `400199` | 订单不存在 | 检查订单号是否正确，确认订单是否属于当前用户 |
| `400198` | 订单已验证成功，但交易ID不匹配 | 检查交易ID是否正确，可能存在重复验证 |
| `400197` | 订单验证失败 | 该订单已标记为失败，无法重新验证 |
| `400188` | 交易ID不匹配 | 同一交易ID对应多个订单时的冲突 |
| `400187` | 交易ID与支付凭证中的交易ID不匹配 | 请检查交易ID字段是否填写错误 |

### 配置相关错误
| 错误码 | 说明 | 处理建议 |
| -- | -- | -- |
| `400196` | IAP配置未找到 | 联系管理员检查IAP配置 |
| `400195` | Apple开发者S2S配置未找到 | 检查Apple开发者配置 |
| `400194` | Apple开发者S2S配置未找到 | 检查租户级别的Apple配置 |

### 安全验证错误
| 错误码 | 说明 | 处理建议 |
| -- | -- | -- |
| `400193` | 凭证验证失败 | 检查凭证数据是否正确，确认网络连接 |
| `400191` | Bundle ID未配置 | 联系管理员配置Bundle ID |
| `400190` | 凭证中未找到Bundle ID | 检查凭证数据完整性 |
| `400189` | Bundle ID不匹配 | 确认应用Bundle ID与配置一致 |

### 系统错误
| 错误码 | 说明 | 处理建议 |
| -- | -- | -- |
| `400250` | 验证苹果订单失败（系统错误） | 稍后重试，如持续失败请联系技术支持 |

### 错误处理最佳实践

1. **参数错误（400201-400206）**：
   - 检查客户端参数传递是否正确
   - 确认数据类型和长度限制

2. **订单错误（400188, 400197-400199）**：
   - 确认订单状态和用户权限
   - 避免重复验证已成功的订单

3. **配置错误（400194-400196）**：
   - 联系系统管理员检查后台配置
   - 确认Apple开发者账号配置正确

4. **安全错误（400189-400193）**：
   - 检查Bundle ID配置
   - 确认凭证数据来源和完整性
   - 验证网络连接和Apple服务可用性

5. **系统错误（400250）**：
   - 实施重试机制（建议指数退避）
   - 记录详细错误日志
   - 必要时联系技术支持